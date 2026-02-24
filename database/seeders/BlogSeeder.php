<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Blog Categories
        $categories = [
            [
                'name' => 'Massage Techniques',
                'slug' => 'massage-techniques',
                'description' => 'Explore various massage techniques and their benefits for health and wellness.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Wellness Tips',
                'slug' => 'wellness-tips',
                'description' => 'Daily wellness tips and advice for a healthier lifestyle.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Industry News',
                'slug' => 'industry-news',
                'description' => 'Latest news and updates from the massage and wellness industry.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Self Care',
                'slug' => 'self-care',
                'description' => 'Self-care practices and routines for mind, body, and soul.',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            BlogCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create Blog Tags
        $tags = [
            'relaxation', 'deep-tissue', 'swedish-massage', 'aromatherapy', 
            'wellness', 'stress-relief', 'health', 'self-care', 
            'spa', 'therapy', 'mindfulness', 'body-care'
        ];

        foreach ($tags as $tagName) {
            BlogTag::updateOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => ucwords(str_replace('-', ' ', $tagName)), 'slug' => Str::slug($tagName)]
            );
        }

        // Get categories for posts
        $massageTechniques = BlogCategory::where('slug', 'massage-techniques')->first();
        $wellnessTips = BlogCategory::where('slug', 'wellness-tips')->first();
        $industryNews = BlogCategory::where('slug', 'industry-news')->first();
        $selfCare = BlogCategory::where('slug', 'self-care')->first();

        // Get first available user for author
        $authorId = \App\Models\User::first()?->id;

        // Create Blog Posts
        $posts = [
            [
                'title' => 'The Ultimate Guide to Swedish Massage: Benefits and Techniques',
                'slug' => 'ultimate-guide-swedish-massage-benefits-techniques',
                'excerpt' => 'Discover the art of Swedish massage, the most popular massage technique worldwide. Learn about its origins, benefits, and why it remains the gold standard for relaxation.',
                'content' => '<p>Swedish massage is often considered the foundation of all Western massage techniques. Developed in the 19th century by Swedish physiologist Per Henrik Ling, this therapeutic approach has stood the test of time and remains one of the most requested massage styles worldwide.</p>

<h2>What is Swedish Massage?</h2>
<p>Swedish massage uses a combination of five basic strokes to manipulate the soft tissues of the body:</p>
<ul>
<li><strong>Effleurage:</strong> Long, gliding strokes that warm up the muscles</li>
<li><strong>Petrissage:</strong> Kneading movements to release muscle tension</li>
<li><strong>Friction:</strong> Deep circular movements to break down knots</li>
<li><strong>Tapotement:</strong> Rhythmic tapping to energize the muscles</li>
<li><strong>Vibration:</strong> Rapid shaking to loosen and relax the body</li>
</ul>

<h2>Benefits of Swedish Massage</h2>
<p>Regular Swedish massage sessions can provide numerous health benefits:</p>
<ul>
<li>Reduces stress and promotes relaxation</li>
<li>Improves blood circulation</li>
<li>Relieves muscle tension and pain</li>
<li>Enhances flexibility and range of motion</li>
<li>Boosts immune system function</li>
<li>Improves sleep quality</li>
</ul>

<h2>What to Expect During Your Session</h2>
<p>A typical Swedish massage session lasts 60-90 minutes. Your therapist will use massage oil or lotion to reduce friction and allow smooth strokes. The pressure can be adjusted from light to firm based on your preferences and needs.</p>

<p>Whether you\'re new to massage or a seasoned spa-goer, Swedish massage offers the perfect balance of relaxation and therapeutic benefit.</p>',
                'featured_image' => null,
                'category_id' => $massageTechniques->id,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'is_featured' => true,
                'views' => 245,
                'tags' => ['swedish-massage', 'relaxation', 'wellness'],
            ],
            [
                'title' => 'Deep Tissue Massage: When You Need More Than Relaxation',
                'slug' => 'deep-tissue-massage-when-you-need-more-than-relaxation',
                'excerpt' => 'Deep tissue massage goes beyond surface-level relaxation to target chronic muscle tension and pain. Learn when this powerful technique is right for you.',
                'content' => '<p>While Swedish massage is perfect for relaxation, sometimes your body needs something more intense. Deep tissue massage is designed to reach the deeper layers of muscle and fascia, making it an excellent choice for chronic pain relief and muscle recovery.</p>

<h2>Understanding Deep Tissue Massage</h2>
<p>Deep tissue massage uses slower, more forceful strokes to target the inner layers of your muscles and connective tissues. This technique is particularly beneficial for:</p>
<ul>
<li>Chronic back pain</li>
<li>Tight leg muscles</li>
<li>Sore shoulders and neck</li>
<li>Sports injuries</li>
<li>Postural problems</li>
</ul>

<h2>What Makes It Different?</h2>
<p>Unlike Swedish massage, which focuses on overall relaxation, deep tissue massage applies sustained pressure using slow, deep strokes. The therapist uses their thumbs, knuckles, forearms, and elbows to break up scar tissue and physically break down muscle knots.</p>

<h2>Is Deep Tissue Right for You?</h2>
<p>Deep tissue massage is ideal if you:</p>
<ul>
<li>Have chronic muscle pain or tension</li>
<li>Are recovering from an injury</li>
<li>Experience limited mobility</li>
<li>Have muscle tightness in specific areas</li>
</ul>

<p>Always communicate with your therapist about pressure levels and any discomfort you may experience during the session.</p>',
                'featured_image' => null,
                'category_id' => $massageTechniques->id,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'is_featured' => false,
                'views' => 189,
                'tags' => ['deep-tissue', 'therapy', 'stress-relief'],
            ],
            [
                'title' => '10 Self-Care Rituals to Practice Daily for Better Well-being',
                'slug' => '10-self-care-rituals-practice-daily-better-wellbeing',
                'excerpt' => 'Self-care isn\'t selfish—it\'s essential. Discover 10 simple daily rituals that can transform your physical and mental well-being.',
                'content' => '<p>In our fast-paced world, taking time for self-care often falls to the bottom of our priority list. However, incorporating simple self-care rituals into your daily routine can significantly improve your overall well-being.</p>

<h2>1. Morning Stretching</h2>
<p>Start your day with 5-10 minutes of gentle stretching to wake up your body and increase blood flow.</p>

<h2>2. Mindful Breathing</h2>
<p>Practice deep breathing exercises for at least 5 minutes daily to reduce stress and improve focus.</p>

<h2>3. Hydration First</h2>
<p>Begin each morning with a glass of warm water with lemon to kickstart your metabolism.</p>

<h2>4. Digital Detox</h2>
<p>Set aside at least one hour daily without screens to give your mind a break from constant stimulation.</p>

<h2>5. Gratitude Practice</h2>
<p>Write down three things you\'re grateful for each day to shift your mindset toward positivity.</p>

<h2>6. Movement</h2>
<p>Engage in at least 30 minutes of physical activity, whether it\'s walking, yoga, or dancing.</p>

<h2>7. Nourishing Meals</h2>
<p>Prepare and eat at least one mindful, nutritious meal without distractions.</p>

<h2>8. Self-Massage</h2>
<p>Spend 5 minutes massaging your hands, feet, or shoulders to release tension.</p>

<h2>9. Evening Wind-Down</h2>
<p>Create a relaxing bedtime routine that signals to your body it\'s time to sleep.</p>

<h2>10. Adequate Sleep</h2>
<p>Prioritize 7-9 hours of quality sleep each night for optimal health and recovery.</p>

<p>Remember, self-care looks different for everyone. Choose the rituals that resonate with you and build from there.</p>',
                'featured_image' => null,
                'category_id' => $selfCare->id,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
                'is_featured' => true,
                'views' => 412,
                'tags' => ['self-care', 'wellness', 'mindfulness'],
            ],
            [
                'title' => 'The Rise of Aromatherapy in Modern Massage Practices',
                'slug' => 'rise-aromatherapy-modern-massage-practices',
                'excerpt' => 'Aromatherapy has become an integral part of massage therapy. Explore how essential oils enhance the massage experience and their therapeutic benefits.',
                'content' => '<p>Aromatherapy massage combines the power of touch with the healing properties of essential oils, creating a deeply therapeutic experience that benefits both body and mind.</p>

<h2>What is Aromatherapy Massage?</h2>
<p>Aromatherapy massage incorporates essential oils extracted from plants, flowers, and herbs into the massage session. These oils are either diffused in the room or diluted in carrier oils and applied directly to the skin during the massage.</p>

<h2>Popular Essential Oils and Their Benefits</h2>
<ul>
<li><strong>Lavender:</strong> Promotes relaxation and reduces anxiety</li>
<li><strong>Eucalyptus:</strong> Clears respiratory passages and energizes</li>
<li><strong>Peppermint:</strong> Relieves headaches and muscle pain</li>
<li><strong>Tea Tree:</strong> Has antibacterial and immune-boosting properties</li>
<li><strong>Chamomile:</strong> Calms nerves and aids sleep</li>
<li><strong>Ylang Ylang:</strong> Reduces stress and uplifts mood</li>
</ul>

<h2>Why Aromatherapy Works</h2>
<p>When essential oils are inhaled, they stimulate the olfactory system, which connects to the limbic system—the brain\'s emotional center. This explains why certain scents can instantly evoke feelings of calm, energy, or happiness.</p>

<h2>Choosing the Right Oils</h2>
<p>Your massage therapist will typically help you select oils based on your needs and preferences. Whether you\'re seeking relaxation, energy, pain relief, or emotional balance, there\'s an essential oil blend that\'s perfect for you.</p>

<p>Experience the transformative power of aromatherapy in your next massage session.</p>',
                'featured_image' => null,
                'category_id' => $wellnessTips->id,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'is_featured' => false,
                'views' => 156,
                'tags' => ['aromatherapy', 'wellness', 'relaxation', 'spa'],
            ],
            [
                'title' => 'Massage Industry Trends: What\'s Shaping the Future of Wellness',
                'slug' => 'massage-industry-trends-shaping-future-wellness',
                'excerpt' => 'The massage and wellness industry is evolving rapidly. Discover the latest trends that are transforming how we approach health and relaxation.',
                'content' => '<p>The wellness industry continues to grow and evolve, with massage therapy at its core. Here are the key trends shaping the future of our industry.</p>

<h2>1. Technology Integration</h2>
<p>From online booking systems to AI-powered massage chairs, technology is making wellness more accessible. Mobile apps now help clients find therapists, book appointments, and even track their wellness journey.</p>

<h2>2. Personalized Treatments</h2>
<p>One-size-fits-all approaches are giving way to customized massage experiences. Therapists are increasingly tailoring sessions based on individual health profiles, preferences, and goals.</p>

<h2>3. Corporate Wellness Programs</h2>
<p>More companies are recognizing the value of employee wellness. On-site chair massages and wellness programs are becoming standard offerings in progressive workplaces.</p>

<h2>4. Holistic Approaches</h2>
<p>Clients are seeking comprehensive wellness solutions that address mind, body, and spirit. Massage therapists are expanding their offerings to include meditation guidance, breathwork, and nutritional advice.</p>

<h2>5. Sustainability Focus</h2>
<p>Eco-conscious consumers are driving demand for sustainable practices, from organic massage oils to environmentally friendly spa facilities.</p>

<h2>6. Mental Health Awareness</h2>
<p>The connection between physical touch and mental health is gaining recognition. Massage therapy is increasingly being recommended as a complementary treatment for anxiety, depression, and PTSD.</p>

<p>These trends reflect a broader shift toward preventive health care and holistic well-being.</p>',
                'featured_image' => null,
                'category_id' => $industryNews->id,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(14),
                'is_featured' => false,
                'views' => 98,
                'tags' => ['wellness', 'health', 'spa', 'therapy'],
            ],
        ];

        foreach ($posts as $postData) {
            $tags = $postData['tags'] ?? [];
            unset($postData['tags']);

            $post = BlogPost::updateOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );

            // Attach tags
            if (!empty($tags)) {
                $tagIds = BlogTag::whereIn('slug', $tags)->pluck('id');
                $post->tags()->sync($tagIds);
            }
        }

        $this->command->info('Blog categories, tags, and posts seeded successfully!');
    }
}
