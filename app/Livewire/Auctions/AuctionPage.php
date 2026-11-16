<?php

namespace App\Livewire\Auctions;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Auction;
use App\Models\City;
use Carbon\Carbon;

/**
 * Top Spot auctions. /auctions asks for a city first; picking one opens
 * /auctions/{gender}-escorts-in-{city} (escorts) or /auctions/{category}-in-{city}
 * (other listing categories), which lists that city's open spots, or says
 * none are open. Tabs come from Auction::categories().
 */
#[Layout('components.layouts.app-evoory')]
class AuctionPage extends Component
{
    public const GENDERS = Auction::GENDERS;

    public $city;
    // Auction::categories() key: female | male | shemale | a listing slug.
    public $category = 'female';
    public $auctions = [];
    public $cityName;
    public $cityId;
    public $citySlug;
    public bool $cityNotFound = false;
    public $canBid = false;
    public string $citySearch = '';
    public array $searchResults = [];

    public function mount($city = null, $gender = null, $category = null)
    {
        $this->city = $city;
        // The category comes from the route segment on city URLs, or from
        // ?category= (older links: ?gender=) on the clean landing URL (the
        // empty-city segment 404s, so the chooser links use the query instead).
        $key = Auction::resolveCategory(
            $gender ?: (string) request()->query('gender', ''),
            $category ?: (string) request()->query('category', '')
        );
        if (!$key && $city) abort(404);
        $this->category = $key ?: 'female';

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

        // Same rule the bid page applies. (This read users.spent_credits,
        // which doesn't exist, so everyone saw the 300-credit warning.)
        $this->canBid = Auction::userQualifies(auth()->user());

        // Server-side search (?q=) so the city chooser still works when the
        // Livewire/Alpine JS bundle fails to boot (e.g. production CDN/JS
        // issues). The GET form submits q; we hydrate results here.
        $this->citySearch = trim((string) request()->query('q', ''));
        $this->updatedCitySearch();
    }

    public function updatedCitySearch(): void
    {
        $term = trim($this->citySearch);
        $this->searchResults = mb_strlen($term) >= 2
            ? City::searchByName($term)->toArray()
            : [];
    }

    public function render()
    {
        // Only show truly-biddable spots: active status AND no winner yet.
        // Once a winner_profile_id is set, the spot is claimed (whether the
        // auction was awarded manually or ended naturally) and should not
        // appear in the "Make Offer" auction listing.
        $openSpots = fn () => Auction::inCategory($this->category)
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
            ->limit(8)->get(['id', 'name', 'country', 'slug']);
        $suggestedCities = City::whereIn('id', $openCityIds)->orderBy('name')->get(['id', 'name', 'country', 'slug'])
            ->each(fn ($c) => $c->hasOpenSpots = true)
            ->concat($featured->reject(fn ($c) => $openCityIds->contains($c->id)))
            ->reject(fn ($c) => $c->id === $this->cityId)
            ->take(10);

        return view('livewire.auctions.auction-page', [
            'auctions' => $this->auctions,
            'canBid' => $this->canBid,
            'suggestedCities' => $suggestedCities,
            'hasOpenSpots' => $openCityIds->isNotEmpty(),
            'categories' => Auction::categories(),
            'prefix' => Auction::categories()[$this->category]['prefix'],
        ]);
    }
}
