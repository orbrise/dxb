<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Imported MR profiles got slugs like
     *   "jenny-new-real-independent-filipino-esco-mr-dnb8ez"
     * because the importer slugged the full name (which includes "– Filipino
     * escort in Dubai") then bolted on "-mr-XXXXXX". The route is /{id}/{slug}
     * (id is the route key, slug has no unique index), so we can shrink each
     * existing slug to just the real-name portion before the en/em dash.
     */
    public function up(): void
    {
        DB::table('users_profiles')
            ->where('imported_from', 'massage_republic')
            ->whereNotNull('name')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $primary = preg_split('/\s+[\x{2013}\x{2014}\-]\s+/u', (string) $row->name, 2)[0] ?? '';
                    $slug = Str::slug($primary, '-');
                    if ($slug === '') continue;
                    $slug = trim(Str::limit($slug, 50, ''), '-');
                    if ($slug === '' || $slug === $row->slug) continue;

                    DB::table('users_profiles')
                        ->where('id', $row->id)
                        ->update(['slug' => $slug]);
                }
            });
    }

    public function down(): void
    {
        // One-shot SEO cleanup — no safe reversal (the original "-mr-XXXXXX"
        // suffix was random per row and not recoverable).
    }
};
