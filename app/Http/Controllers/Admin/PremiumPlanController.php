<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremiumPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PremiumPlanController extends Controller
{
    public function index()
    {
        $plans = PremiumPlan::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.premium-plans', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request);
        $data['features'] = $this->normaliseFeatures($data['features'] ?? []);
        PremiumPlan::create($data);
        return back()->with('success', 'Plan created.');
    }

    public function update(Request $request, PremiumPlan $plan)
    {
        $data = $this->validatePlan($request, $plan->id);
        $data['features'] = $this->normaliseFeatures($data['features'] ?? []);
        $plan->update($data);
        return back()->with('success', 'Plan updated.');
    }

    public function destroy(PremiumPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }

    private function validatePlan(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = 'required|string|max:40|regex:/^[a-z0-9-]+$/|unique:premium_plans,slug';
        if ($ignoreId) $slugRule .= ',' . $ignoreId;

        return $request->validate([
            'name'          => 'required|string|max:120',
            'slug'          => $slugRule,
            'price'         => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:0',
            'period_label'  => 'nullable|string|max:40',
            'description'   => 'nullable|string|max:500',
            'cta_label'     => 'required|string|max:60',
            'tag'           => 'nullable|string|max:40',
            'tag_color'     => 'nullable|in:lime,pink',
            'variant'       => 'required|in:free,lime,pink',
            'features'      => 'nullable|array',
            'features.*.label'    => 'required_with:features|string|max:120',
            'features.*.included' => 'nullable',
            'is_free'       => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
            'sort_order'    => 'nullable|integer|min:0',
        ]);
    }

    /**
     * Make sure each feature row is `{label, included:bool}`. Checkboxes
     * from the form only submit when checked, so a missing key = false.
     */
    private function normaliseFeatures(array $features): array
    {
        return array_values(array_filter(array_map(function ($f) {
            $label = trim((string) ($f['label'] ?? ''));
            if ($label === '') return null;
            return [
                'label'    => $label,
                'included' => filter_var($f['included'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }, $features)));
    }
}
