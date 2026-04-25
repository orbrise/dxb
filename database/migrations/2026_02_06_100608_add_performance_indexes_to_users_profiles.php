<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Comprehensive performance indexes for listing/detail queries.
     * Idempotent: safely skips any index that already exists.
     */
    public function up(): void
    {
        $this->addIndex('users_profiles', ['is_active', 'archived_at', 'city', 'gender'], 'idx_profiles_active_city_gender');
        $this->addIndex('users_profiles', ['package_id', 'is_active'], 'idx_profiles_package_active');
        $this->addIndex('users_profiles', ['id', 'city', 'gender', 'is_active'], 'idx_profiles_auction_lookup');
        $this->addIndex('users_profiles', ['slug'], 'idx_profiles_slug');
        $this->addIndex('users_profiles', ['user_id', 'is_active'], 'idx_profiles_user_active');
        $this->addIndex('users_profiles', ['is_verified'], 'idx_profiles_is_verified');
        $this->addIndex('users_profiles', ['age'], 'idx_profiles_age');
        $this->addIndex('users_profiles', ['height'], 'idx_profiles_height');
        $this->addIndex('users_profiles', ['incallprice'], 'idx_profiles_incallprice');
        $this->addIndex('users_profiles', ['ethnicity'], 'idx_profiles_ethnicity');
        $this->addIndex('users_profiles', ['nationality'], 'idx_profiles_nationality');
        $this->addIndex('users_profiles', ['created_at'], 'idx_profiles_created_at');

        $this->addIndex('profile_images', ['profile_id', 'id'], 'idx_profile_images_profile');
        $this->addIndex('profile_images', ['user_id'], 'idx_profile_images_user');

        if (Schema::hasTable('reviews')) {
            $this->addIndex('reviews', ['profile_id', 'status', 'created_at'], 'idx_reviews_profile_status');
            $this->addIndex('reviews', ['status', 'created_at'], 'idx_reviews_recent');
            if (Schema::hasColumn('reviews', 'user_id')) {
                $this->addIndex('reviews', ['user_id', 'profile_id'], 'idx_reviews_user_profile');
            }
        }

        if (Schema::hasTable('auctions')) {
            $this->addIndex('auctions', ['status', 'city_id', 'gender', 'end_date'], 'idx_auctions_active_city');
        }

        if (Schema::hasTable('user_languages')) {
            $this->addIndex('user_languages', ['language_id', 'user_id'], 'idx_user_languages_lang');
            if (Schema::hasColumn('user_languages', 'profile_id')) {
                $this->addIndex('user_languages', ['profile_id', 'language_id'], 'idx_user_languages_profile');
            }
        }

        if (Schema::hasTable('user_services')) {
            $this->addIndex('user_services', ['service_id', 'user_id'], 'idx_user_services_service');
            if (Schema::hasColumn('user_services', 'profile_id')) {
                $this->addIndex('user_services', ['profile_id', 'service_id'], 'idx_user_services_profile');
            }
        }

        if (Schema::hasColumn('users', 'type')) {
            $this->addIndex('users', ['type'], 'idx_users_type');
        }

        if (Schema::hasTable('cities')) {
            if (Schema::hasColumn('cities', 'slug')) {
                $this->addIndex('cities', ['slug'], 'idx_cities_slug');
            }
            if (Schema::hasColumn('cities', 'name')) {
                $this->addIndex('cities', ['name'], 'idx_cities_name');
            }
            if (Schema::hasColumn('cities', 'country')) {
                $this->addIndex('cities', ['country'], 'idx_cities_country');
            }
        }

        if (Schema::hasTable('questions')) {
            $this->addIndex('questions', ['profile_id', 'status'], 'idx_questions_profile_status');
        }

        if (Schema::hasTable('profile_visits')) {
            $this->addIndex('profile_visits', ['profile_id', 'ip_address', 'visited_at'], 'idx_profile_visits_lookup');
        }
    }

    public function down(): void
    {
        // No-op: indexes are safe to keep; dropping them could regress performance.
    }

    protected function addIndex(string $table, array $columns, string $name): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if ($this->indexExists($table, $name)) {
            return;
        }

        foreach ($columns as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return;
            }
        }

        try {
            Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                $t->index($columns, $name);
            });
        } catch (\Throwable $e) {
            // Swallow — index may have been created concurrently or there is a pre-existing incompatible one.
        }
    }

    protected function indexExists(string $table, string $name): bool
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();
        $result = $connection->select(
            'SELECT COUNT(1) AS cnt FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $name]
        );

        return !empty($result) && (int) $result[0]->cnt > 0;
    }
};
