<?php

namespace App\Livewire;

use App\Models\PremiumPlan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class PremiumAccount extends Component
{
    public function render()
    {
        $plans = PremiumPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (PremiumPlan $p) {
                return [
                    'id'          => $p->slug,
                    'tag'         => $p->tag,
                    'tagColor'    => $p->tag_color,
                    'badge'       => $p->is_free ? 'Current Plan' : null,
                    'name'        => $p->name,
                    'price'       => '$' . number_format((float) $p->price, (floor($p->price) == $p->price ? 0 : 2)),
                    'period'      => $p->period_label,
                    'description' => (string) $p->description,
                    'features'    => $p->features_list,
                    'cta'         => $p->cta_label,
                    'ctaDisabled' => $p->is_free,
                    'variant'     => $p->variant,
                    'current'     => $p->is_free,
                ];
            })
            ->all();

        return view('livewire.premium-account', [
            'plans' => $plans,
        ]);
    }
}
