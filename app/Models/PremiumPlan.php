<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_days',
        'period_label',
        'description',
        'cta_label',
        'tag',
        'tag_color',
        'variant',
        'features',
        'is_free',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'duration_days' => 'integer',
        'features'      => 'array',
        'is_free'       => 'boolean',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
    ];

    /**
     * Normalise `features` so views always get a safe array of
     * `['label' => string, 'included' => bool]` even if a row has
     * null or malformed JSON.
     */
    public function getFeaturesListAttribute(): array
    {
        $raw = $this->features ?? [];
        return array_values(array_map(function ($f) {
            return [
                'label'    => (string) ($f['label'] ?? ''),
                'included' => (bool)   ($f['included'] ?? false),
            ];
        }, $raw));
    }
}
