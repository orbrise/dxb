<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Short URL-safe handle used in route params (e.g. "1m", "6m", "free").
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0);
            // Length of the subscription in days. 0 = free / unlimited.
            $table->unsignedInteger('duration_days')->default(0);
            // Human-readable period suffix shown next to price (e.g. "/ month").
            $table->string('period_label')->default('');
            $table->text('description')->nullable();
            $table->string('cta_label')->default('Select Plan');
            // Optional top tag pill (e.g. "MONTHLY", "MOST POPULAR"). Null = no tag.
            $table->string('tag')->nullable();
            // Tag colour variant: "lime" or "pink".
            $table->string('tag_color')->nullable();
            // Card variant drives background + border colour: free | lime | pink.
            $table->string('variant')->default('free');
            // Features as array of { label, included }.
            $table->json('features')->nullable();
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Seed the three default plans that match the current hard-coded UI.
        DB::table('premium_plans')->insert([
            [
                'name'          => 'FREE',
                'slug'          => 'free',
                'price'         => 0,
                'duration_days' => 0,
                'period_label'  => '/ forever',
                'description'   => 'Essential access for casual explorers.',
                'cta_label'     => 'Active Plan',
                'tag'           => null,
                'tag_color'     => null,
                'variant'       => 'free',
                'features'      => json_encode([
                    ['label' => 'Chat with up to 5 different people', 'included' => true],
                    ['label' => 'Basic account access',               'included' => true],
                    ['label' => 'Standard messaging features',        'included' => true],
                    ['label' => 'Unlimited chats and calls',          'included' => false],
                    ['label' => 'Premium membership badge',           'included' => false],
                ]),
                'is_free'       => true,
                'is_active'     => true,
                'sort_order'    => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => '1 Month Premium',
                'slug'          => '1m',
                'price'         => 10.00,
                'duration_days' => 30,
                'period_label'  => '/ month',
                'description'   => 'Enjoy unlimited connections with exclusive Premium benefits.',
                'cta_label'     => 'Select 1 Month Premium',
                'tag'           => 'MONTHLY',
                'tag_color'     => 'lime',
                'variant'       => 'lime',
                'features'      => json_encode([
                    ['label' => 'Unlimited Chat',                 'included' => true],
                    ['label' => 'Unlimited Calls',                'included' => true],
                    ['label' => 'Exclusive Premium Badge',        'included' => true],
                    ['label' => 'Unlimited Unique Conversations', 'included' => true],
                    ['label' => 'Full Access to Premium Features','included' => true],
                    ['label' => 'Connect with Anyone, Anytime',   'included' => true],
                ]),
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => '6 Months Premium',
                'slug'          => '6m',
                'price'         => 50.00,
                'duration_days' => 180,
                'period_label'  => '/ 6 months',
                'description'   => 'Comprehensive 6-month membership option for uninterrupted luxury connections.',
                'cta_label'     => 'Select 6 Months Premium',
                'tag'           => 'MOST POPULAR',
                'tag_color'     => 'pink',
                'variant'       => 'pink',
                'features'      => json_encode([
                    ['label' => 'Unlimited Chat',                 'included' => true],
                    ['label' => 'Unlimited Calls',                'included' => true],
                    ['label' => 'Exclusive Premium Badge',        'included' => true],
                    ['label' => 'Unlimited Unique Conversations', 'included' => true],
                    ['label' => 'Full Access to Premium Features','included' => true],
                    ['label' => 'Connect with Anyone, Anytime',   'included' => true],
                ]),
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_plans');
    }
};
