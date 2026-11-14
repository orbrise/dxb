<?php

namespace App\Livewire\Auctions;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Auction;
use App\Models\City;
use Carbon\Carbon;

/**
 * Top Spot auctions. /auctions asks for a city first; picking one opens
 * /auctions/{gender}-escorts-in-{city}, which lists that city's open spots,
 * or says none are open.
 */
#[Layout('components.layouts.app-evoory')]
class AuctionPage extends Component
{
    public const GENDERS = ['female' => 'Escorts', 'male' => 'Male Escorts', 'shemale' => 'Shemale Escorts'];

    public $city;
    public $gender = 'female';
    public $auctions = [];
    public $cityName;
    public $cityId;
    public $citySlug;
    public bool $cityNotFound = false;
    public $canBid = false;
    public string $citySearch = '';
    public array $searchResults = [];

    public function mount($city = null, $gender = 'female')
    {
        $this->city = $city;
        $this->gender = array_key_exists($gender, self::GENDERS) ? $gender : 'female';

        if ($city) {
            // City slug, as listing URLs use; older links used the lower-cased name.
            $cityObj = City::where('slug', $city)->first()
                ?? City::where('name', str_replace('-', ' ', $city))->first();
            if ($cityObj) {
                $this->cityId = $cityObj->id;
                $this->cityName = $cityObj->name;
                $this->citySlug = $cityObj->slug;
            } else {
                $this->cityNotFound = true;
            }
        }

        // Check if user can bid (has spent at least 300 credits)
        if (auth()->check()) {
            $this->canBid = auth()->user()->spent_credits >= 300;
        }
    }

    public function updatedCitySearch(): void
    {
        $term = trim($this->citySearch);
        $this->searchResults = mb_strlen($term) >= 2
            ? City::where('name', 'like', "%{$term}%")
                ->orderByRaw('name LIKE ? DESC', ["{$term}%"])
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'country'])
                ->toArray()
            : [];
    }

    public function selectCity(int $cityId)
    {
        $city = City::find($cityId);
        if (!$city) return;

        return $this->redirect(url("/auctions/{$this->gender}-escorts-in-{$city->slug}"), navigate: true);
    }

    public function setGender(string $gender)
    {
        if (!array_key_exists($gender, self::GENDERS)) return;
        if (!$this->citySlug) {
            $this->gender = $gender;
            return;
        }

        return $this->redirect(url("/auctions/{$gender}-escorts-in-{$this->citySlug}"), navigate: true);
    }

    public function render()
    {
        // Only show truly-biddable spots: active status AND no winner yet.
        // Once a winner_profile_id is set, the spot is claimed (whether the
        // auction was awarded manually or ended naturally) and should not
        // appear in the "Make Offer" auction listing.
        $openSpots = fn () => Auction::where('gender', $this->gender)
            ->where('status', 'active')
            ->whereNull('winner_profile_id');

        $this->auctions = $this->cityId
            ? $openSpots()->where('city_id', $this->cityId)
                ->orderBy('spot_number')
                ->with('city')
                ->get()
            : collect();

        // Each card shows the slot's own image (the admin's upload, or the
        // default: Auction::background_image_url).
        foreach ($this->auctions as $auction) {
            $auction->timeLeft = Carbon::now()->diffForHumans($auction->end_date, ['parts' => 1, 'short' => true]);
            $auction->daysLeft = Carbon::now()->diffInDays($auction->end_date);
        }

        // Quick picks for the city chooser (also behind "Change city"): cities
        // with open spots first, then this site's featured cities.
        $openCityIds = $openSpots()->distinct()->pluck('city_id');
        $country = getCurrentCountry();
        $featured = City::where('is_featured', 1)
            ->when($country, fn ($q) => $q->where('country', $country->nicename))
            ->orderBy('feature_priority')->orderBy('name')
            ->limit(8)->get(['id', 'name', 'country']);
        $suggestedCities = City::whereIn('id', $openCityIds)->orderBy('name')->get(['id', 'name', 'country'])
            ->each(fn ($c) => $c->hasOpenSpots = true)
            ->concat($featured->reject(fn ($c) => $openCityIds->contains($c->id)))
            ->reject(fn ($c) => $c->id === $this->cityId)
            ->take(10);

        return view('livewire.auctions.auction-page', [
            'auctions' => $this->auctions,
            'canBid' => $this->canBid,
            'suggestedCities' => $suggestedCities,
            'hasOpenSpots' => $openCityIds->isNotEmpty(),
        ]);
    }
}
