<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * These indexes dramatically improve homepage and profile listing performance
     */
    public function up(): void
    {
        Schema::table('users_profiles', function (Blueprint $table) {
            // Composite index for main homepage query (most important!)
            // Covers: WHERE is_active = 1 AND archived_at IS NULL AND city = X AND gender = Y
            $table->index(['is_active', 'archived_at', 'city', 'gender'], 'idx_profiles_active_city_gender');
            
            // Index for package-based filtering (VIP, Featured, etc.)
            $table->index(['package_id', 'is_active'], 'idx_profiles_package_active');
            
            // Index for auction winner lookups
            $table->index(['id', 'city', 'gender', 'is_active'], 'idx_profiles_auction_lookup');
            
            // Index for profile slug lookups (detail page)
            $table->index('slug', 'idx_profiles_slug');
            
            // Index for user's profiles
            $table->index(['user_id', 'is_active'], 'idx_profiles_user_active');
        });

        // Add indexes to profile_images for eager loading
        Schema::table('profile_images', function (Blueprint $table) {
            $table->index(['profile_id', 'id'], 'idx_profile_images_profile');
            $table->index('user_id', 'idx_profile_images_user');
        });

        // Add indexes to reviews
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['profile_id', 'status', 'created_at'], 'idx_reviews_profile_status');
            $table->index(['status', 'created_at'], 'idx_reviews_recent');
        });

        // Add indexes to auctions
        Schema::table('auctions', function (Blueprint $table) {
            $table->index(['status', 'city', 'end_date'], 'idx_auctions_active_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_profiles', function (Blueprint $table) {
            $table->dropIndex('idx_profiles_active_city_gender');
            $table->dropIndex('idx_profiles_package_active');
            $table->dropIndex('idx_profiles_auction_lookup');
            $table->dropIndex('idx_profiles_slug');
            $table->dropIndex('idx_profiles_user_active');
        });

        Schema::table('profile_images', function (Blueprint $table) {
            $table->dropIndex('idx_profile_images_profile');
            $table->dropIndex('idx_profile_images_user');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_profile_status');
            $table->dropIndex('idx_reviews_recent');
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->dropIndex('idx_auctions_active_city');
        });
    }
};
