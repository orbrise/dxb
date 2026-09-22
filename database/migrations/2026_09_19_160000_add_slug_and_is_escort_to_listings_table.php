<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'slug')) {
                $table->string('slug', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('listings', 'is_escort_category')) {
                $table->boolean('is_escort_category')->default(false)->after('slug');
            }
            if (!Schema::hasColumn('listings', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_escort_category');
            }
        });

        // Canonical categories shown in the profile-form dropdown and listing-page filter.
        // The "escorts" row keeps the legacy /{gender}-escorts-in-{city} URLs; the other
        // rows resolve via the new /{slug}-in-{city} route.
        $categories = [
            ['name' => 'escorts',        'slug' => 'escorts',        'is_escort_category' => 1, 'sort_order' => 1],
            ['name' => 'Phone & Cam',    'slug' => 'phone-cam',      'is_escort_category' => 0, 'sort_order' => 2],
            ['name' => 'Massage',        'slug' => 'massage',        'is_escort_category' => 0, 'sort_order' => 3],
            ['name' => 'Adult Products', 'slug' => 'adult-products', 'is_escort_category' => 0, 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            $existing = DB::table('listings')->where('slug', $cat['slug'])->first();
            if ($existing) {
                DB::table('listings')->where('id', $existing->id)->update([
                    'name' => $cat['name'],
                    'is_escort_category' => $cat['is_escort_category'],
                    'sort_order' => $cat['sort_order'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('listings')->insert(array_merge($cat, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        try {
            Schema::table('listings', function (Blueprint $table) {
                $table->unique('slug', 'listings_slug_unique');
            });
        } catch (\Throwable $e) {
            // Index may already exist on re-run; ignore.
        }
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            try { $table->dropUnique('listings_slug_unique'); } catch (\Throwable $e) {}
            foreach (['slug', 'is_escort_category', 'sort_order'] as $col) {
                if (Schema::hasColumn('listings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
