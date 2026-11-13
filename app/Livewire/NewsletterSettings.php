<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\NewsletterGender;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * My Account → Newsletter (/my-account/newsletter, and /my-account/newsletter/edit
 * which the newsletter emails link to). Shows what the user is subscribed to
 * and lets them change or cancel it. The weekly email itself is
 * SendWeeklyNewsletter: Mondays, verified email addresses only.
 */
#[Layout('components.layouts.app-evoory')]
class NewsletterSettings extends Component
{
    /** The newsletter's categories, as stored in newsletter_genders. */
    public const CATEGORIES = [
        'female' => 'Escorts',
        'male' => 'Male Escorts',
        'shemale' => 'Shemale Escorts',
    ];

    public bool $receiveNewsletter = false;
    public array $selectedCities = [];
    public string $citySearch = '';
    public array $searchResults = [];
    public array $selectedGenders = [];
    public bool $saved = false;

    public function mount(): void
    {
        $this->loadUserSettings();
    }

    public function loadUserSettings(): void
    {
        $user = Auth::user();

        $this->selectedCities = NewsletterSubscription::where('user_id', $user->id)
            ->with('city')
            ->get()
            ->filter(fn ($sub) => $sub->city)
            ->map(fn ($sub) => [
                'id' => $sub->city_id,
                'name' => $sub->city->name,
                'country' => $sub->city->country ?? '',
            ])
            ->values()
            ->toArray();
        $this->receiveNewsletter = count($this->selectedCities) > 0;

        $this->selectedGenders = NewsletterGender::where('user_id', $user->id)->pluck('gender')->toArray();
        // The weekly email falls back to Escorts when none is picked; show that.
        if (!$this->selectedGenders) $this->selectedGenders = ['female'];
    }

    // Any change after a save hides the "Saved" note.
    public function updated($property): void
    {
        if ($property !== 'saved') $this->saved = false;
    }

    public function updatedCitySearch(): void
    {
        $term = trim($this->citySearch);
        $this->searchResults = mb_strlen($term) >= 2
            ? City::where('name', 'like', "%{$term}%")
                ->orWhere('country', 'like', "%{$term}%")
                // "Dub" → Dubai before Audubon.
                ->orderByRaw('name LIKE ? DESC', ["{$term}%"])
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'country'])
                ->toArray()
            : [];
    }

    public function addCity(int $cityId): void
    {
        $city = City::find($cityId);
        if ($city && !collect($this->selectedCities)->contains('id', $cityId)) {
            $this->selectedCities[] = ['id' => $city->id, 'name' => $city->name, 'country' => $city->country ?? ''];
        }
        $this->citySearch = '';
        $this->searchResults = [];
        $this->saved = false;
        $this->resetErrorBag('selectedCities');
    }

    public function removeCity(int $index): void
    {
        unset($this->selectedCities[$index]);
        $this->selectedCities = array_values($this->selectedCities);
        $this->saved = false;
    }

    public function save(): void
    {
        if ($this->receiveNewsletter) {
            $this->validate([
                'selectedCities' => 'required|array|min:1',
                'selectedGenders' => 'required|array|min:1',
                'selectedGenders.*' => 'in:' . implode(',', array_keys(self::CATEGORIES)),
            ], [
                'selectedCities.required' => 'Add at least one city.',
                'selectedCities.min' => 'Add at least one city.',
                'selectedGenders.required' => 'Choose at least one category.',
                'selectedGenders.min' => 'Choose at least one category.',
            ]);
        }

        $userId = Auth::id();
        DB::transaction(function () use ($userId) {
            NewsletterSubscription::where('user_id', $userId)->delete();
            NewsletterGender::where('user_id', $userId)->delete();
            if (!$this->receiveNewsletter) return;

            foreach (collect($this->selectedCities)->pluck('id')->unique() as $cityId) {
                NewsletterSubscription::create(['user_id' => $userId, 'city_id' => $cityId]);
            }
            foreach (array_unique($this->selectedGenders) as $gender) {
                NewsletterGender::create(['user_id' => $userId, 'gender' => $gender]);
            }
        });

        $this->loadUserSettings();
        $this->saved = true;
    }

    public function unsubscribe(): void
    {
        $this->receiveNewsletter = false;
        $this->save();
    }

    public function render()
    {
        $user = Auth::user();
        // The summary shows what's saved, not what's being edited below it.
        $savedCities = NewsletterSubscription::where('user_id', $user->id)->with('city')->get()
            ->filter(fn ($sub) => $sub->city)->map(fn ($sub) => $sub->city)->values();
        $savedGenders = NewsletterGender::where('user_id', $user->id)->pluck('gender')->toArray() ?: ['female'];

        return view('livewire.newsletter-settings', [
            'savedCities' => $savedCities,
            'savedCategories' => array_values(array_intersect_key(self::CATEGORIES, array_flip($savedGenders))),
            'email' => $user->email,
            'emailVerified' => (bool) $user->email_verified_at,
        ]);
    }
}
