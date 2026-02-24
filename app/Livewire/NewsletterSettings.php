<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\NewsletterSubscription;
use App\Models\NewsletterGender;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

class NewsletterSettings extends Component
{
    public $showModal = false;
    public $receiveNewsletter = false;
    public $selectedCities = [];
    public $citySearch = '';
    public $searchResults = [];
    public $selectedGenders = [];

    public function mount()
    {
        $this->loadUserSettings();
    }

    #[On('openNewsletterModal')]
    public function openModal()
    {
        $this->loadUserSettings();
        $this->showModal = true;
    }

    public function loadUserSettings()
    {
        $user = Auth::user();
        
        // Load subscribed cities
        $subscriptions = NewsletterSubscription::where('user_id', $user->id)->with('city')->get();
        $this->selectedCities = $subscriptions->map(function($sub) {
            return [
                'id' => $sub->city_id,
                'name' => $sub->city->name,
                'country' => $sub->city->country ?? '',
            ];
        })->toArray();

        // Load selected genders
        $this->selectedGenders = NewsletterGender::where('user_id', $user->id)
            ->pluck('gender')
            ->toArray();

        // Set receive newsletter flag
        $this->receiveNewsletter = count($this->selectedCities) > 0;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updatedCitySearch()
    {
        if (strlen($this->citySearch) >= 2) {
            $this->searchResults = City::where('name', 'like', '%' . $this->citySearch . '%')
                ->orWhere('country', 'like', '%' . $this->citySearch . '%')
                ->limit(10)
                ->get(['id', 'name', 'country'])
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function addCity($cityId)
    {
        $city = City::find($cityId);
        
        if ($city && !collect($this->selectedCities)->contains('id', $cityId)) {
            $this->selectedCities[] = [
                'id' => $city->id,
                'name' => $city->name,
                'country' => $city->country ?? '',
            ];
        }
        
        $this->citySearch = '';
        $this->searchResults = [];
    }

    public function removeCity($index)
    {
        unset($this->selectedCities[$index]);
        $this->selectedCities = array_values($this->selectedCities);
    }

    public function save()
    {
        $user = Auth::user();

        // Delete existing subscriptions
        NewsletterSubscription::where('user_id', $user->id)->delete();
        NewsletterGender::where('user_id', $user->id)->delete();

        // Save new subscriptions if newsletter is enabled
        if ($this->receiveNewsletter && count($this->selectedCities) > 0) {
            foreach ($this->selectedCities as $city) {
                NewsletterSubscription::create([
                    'user_id' => $user->id,
                    'city_id' => $city['id'],
                ]);
            }

            // Save selected genders
            foreach ($this->selectedGenders as $gender) {
                NewsletterGender::create([
                    'user_id' => $user->id,
                    'gender' => $gender,
                ]);
            }
        }

        $this->showModal = false;
        session()->flash('success', 'Newsletter settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.newsletter-settings');
    }
}
