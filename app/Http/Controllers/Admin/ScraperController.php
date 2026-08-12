<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ScraperAutoCity;
use App\Models\ScraperRun;
use App\Services\ScraperRunLauncher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ScraperController extends Controller
{
    public const SOURCES = [
        'massagerepublic' => [
            'label'   => 'Massage Republic',
            'command' => 'scrape:massagerepublic',
        ],
        'ivysociete' => [
            'label'   => 'Ivy Societe',
            'command' => 'scrape:ivysociete',
        ],
    ];

    // ---------- Manual runner ----------

    public function index()
    {
        $runs = ScraperRun::orderByDesc('id')->limit(50)->get();

        return view('admin.scrapers.index', [
            'sources' => self::SOURCES,
            'runs'    => $runs,
            'cities'  => $this->cityOptions(),
        ]);
    }

    public function store(Request $request, ScraperRunLauncher $launcher): RedirectResponse
    {
        $sources = self::SOURCES;
        $data = $request->validate([
            'source'   => ['required', 'string', 'in:' . implode(',', array_keys($sources))],
            'cities'   => ['required', 'array', 'min:1', 'max:100'],
            'cities.*' => ['required', 'string', 'max:80'],
            'limit'    => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $source = $data['source'];
        $selected = array_values(array_unique($data['cities']));

        $created = [];
        foreach ($selected as $citySlug) {
            $created[] = ScraperRun::create([
                'source'          => $source,
                'city_slug'       => $citySlug,
                'requested_count' => (int) $data['limit'],
                'status'          => ScraperRun::STATUS_PENDING,
                'progress_total'  => (int) $data['limit'],
                'started_by'      => auth()->id(),
            ]);
        }

        // Kick off the queue: if nothing is running for this source, promote
        // the oldest pending run. Chained runs after this one are launched
        // automatically by the scraper commands when they finish.
        $launcher->launchNextPendingIfIdle($source);

        $msg = count($created) === 1
            ? "{$sources[$source]['label']} scraper queued for {$selected[0]} (limit {$data['limit']})."
            : "{$sources[$source]['label']} scraper queued for " . count($created) . " cities (limit {$data['limit']} each). Running sequentially.";

        return redirect()->route('admin.scrapers.index')->with('success', $msg);
    }

    public function status(ScraperRun $run): JsonResponse
    {
        return response()->json([
            'id'               => $run->id,
            'source'           => $run->source,
            'city_slug'        => $run->city_slug,
            'requested_count'  => $run->requested_count,
            'status'           => $run->status,
            'progress_current' => $run->progress_current,
            'progress_total'   => $run->progress_total,
            'progress_stage'   => $run->progress_stage,
            'progress_percent' => $run->progressPercent(),
            'exit_code'        => $run->exit_code,
            'error_message'    => $run->error_message,
            'started_at'       => optional($run->started_at)->toDateTimeString(),
            'completed_at'     => optional($run->completed_at)->toDateTimeString(),
            'is_terminal'      => $run->isTerminal(),
        ]);
    }

    public function log(ScraperRun $run)
    {
        if (! $run->log_path || ! File::exists($run->log_path)) {
            return response('(no log yet)', 200)->header('Content-Type', 'text/plain');
        }
        $contents = File::get($run->log_path);
        if (strlen($contents) > 8192) {
            $contents = "... (truncated) ...\n" . substr($contents, -8192);
        }
        return response($contents, 200)->header('Content-Type', 'text/plain');
    }

    // ---------- Auto-scheduled cities ----------

    public function auto()
    {
        $entries = ScraperAutoCity::orderBy('source')->orderBy('city_slug')->get()->groupBy('source');

        return view('admin.scrapers.auto', [
            'sources' => self::SOURCES,
            'entries' => $entries,
            'cities'  => $this->cityOptions(),
        ]);
    }

    public function autoStore(Request $request): RedirectResponse
    {
        $sources = self::SOURCES;
        $data = $request->validate([
            'source'        => ['required', 'string', 'in:' . implode(',', array_keys($sources))],
            'cities'        => ['required', 'array', 'min:1', 'max:100'],
            'cities.*'      => ['required', 'string', 'max:80'],
            'limit_per_run' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $added = 0;
        $skipped = 0;
        $cityMap = collect($this->cityOptions())->keyBy('slug');

        foreach (array_unique($data['cities']) as $slug) {
            $exists = ScraperAutoCity::where('source', $data['source'])
                ->where('city_slug', $slug)
                ->exists();
            if ($exists) {
                $skipped++;
                continue;
            }
            ScraperAutoCity::create([
                'source'        => $data['source'],
                'city_slug'     => $slug,
                'city_name'     => $cityMap[$slug]['name'] ?? null,
                'limit_per_run' => (int) $data['limit_per_run'],
                'is_active'     => true,
            ]);
            $added++;
        }

        $msg = "Added {$added} city(ies) to {$sources[$data['source']]['label']} auto-scraper";
        if ($skipped) $msg .= " ({$skipped} already existed)";
        return redirect()->route('admin.scrapers.auto')->with('success', $msg . '.');
    }

    public function autoToggle(ScraperAutoCity $city): RedirectResponse
    {
        $city->update(['is_active' => ! $city->is_active]);
        return back()->with('success', "{$city->city_slug} " . ($city->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function autoDestroy(ScraperAutoCity $city): RedirectResponse
    {
        $slug = $city->city_slug;
        $city->delete();
        return back()->with('success', "Removed {$slug} from the auto-scraper list.");
    }

    /**
     * Return DB city options as [{slug, name}, ...] sorted alphabetically.
     * Filters out rows without a slug — those can't be scraped anyway.
     */
    private function cityOptions(): array
    {
        return City::whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name])
            ->values()
            ->all();
    }
}
