<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, boolean
            $table->string('group')->default('general'); // general, seo, social
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default SEO settings
        $settings = [
            // General SEO
            ['key' => 'blog_title', 'value' => 'Massage Republic Blog', 'type' => 'text', 'group' => 'seo', 'label' => 'Blog Title', 'description' => 'Main title for the blog (used in title tag)', 'sort_order' => 1],
            ['key' => 'blog_tagline', 'value' => 'Helping adult providers and users to have more fun and success', 'type' => 'text', 'group' => 'seo', 'label' => 'Blog Tagline', 'description' => 'Tagline or subtitle for the blog', 'sort_order' => 2],
            ['key' => 'meta_description', 'value' => 'The Massage Republic Blog - helping adult providers and users to have more fun and success.', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Default Meta Description', 'description' => 'Default meta description for blog pages (160 chars max)', 'sort_order' => 3],
            ['key' => 'meta_keywords', 'value' => 'massage, wellness, spa, therapy, relaxation', 'type' => 'text', 'group' => 'seo', 'label' => 'Default Meta Keywords', 'description' => 'Default meta keywords (comma separated)', 'sort_order' => 4],
            
            // Open Graph / Social
            ['key' => 'og_image', 'value' => '', 'type' => 'image', 'group' => 'social', 'label' => 'Default OG Image', 'description' => 'Default image for social sharing (1200x630 recommended)', 'sort_order' => 5],
            ['key' => 'og_site_name', 'value' => 'Massage Republic Blog', 'type' => 'text', 'group' => 'social', 'label' => 'OG Site Name', 'description' => 'Site name for Open Graph', 'sort_order' => 6],
            ['key' => 'twitter_handle', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Twitter Handle', 'description' => 'Twitter handle without @ (e.g., massagerepublic)', 'sort_order' => 7],
            ['key' => 'facebook_app_id', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook App ID', 'description' => 'Facebook App ID for insights', 'sort_order' => 8],
            
            // Indexing
            ['key' => 'robots_index', 'value' => '1', 'type' => 'boolean', 'group' => 'seo', 'label' => 'Allow Search Indexing', 'description' => 'Allow search engines to index the blog', 'sort_order' => 9],
            ['key' => 'robots_follow', 'value' => '1', 'type' => 'boolean', 'group' => 'seo', 'label' => 'Allow Link Following', 'description' => 'Allow search engines to follow links', 'sort_order' => 10],
            
            // Analytics
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'text', 'group' => 'analytics', 'label' => 'Google Analytics ID', 'description' => 'Google Analytics tracking ID (e.g., G-XXXXXXXXXX)', 'sort_order' => 11],
            ['key' => 'google_tag_manager_id', 'value' => '', 'type' => 'text', 'group' => 'analytics', 'label' => 'Google Tag Manager ID', 'description' => 'GTM Container ID (e.g., GTM-XXXXXXX)', 'sort_order' => 12],
            
            // Schema/Structured Data
            ['key' => 'schema_organization_name', 'value' => 'Massage Republic', 'type' => 'text', 'group' => 'schema', 'label' => 'Organization Name', 'description' => 'Organization name for structured data', 'sort_order' => 13],
            ['key' => 'schema_organization_logo', 'value' => '', 'type' => 'image', 'group' => 'schema', 'label' => 'Organization Logo', 'description' => 'Logo URL for structured data', 'sort_order' => 14],
        ];

        foreach ($settings as $setting) {
            DB::table('blog_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_settings');
    }
};
