<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin → User Locations: every registration, login, profile create and
 * profile edit with the IP and country it came from (user_location_logs,
 * filled by App\Support\IpLocation).
 */
class UserLocationController extends Controller
{
    public const EVENTS = [
        'register'       => 'Registered',
        'login'          => 'Login',
        'profile_create' => 'Profile created',
        'profile_update' => 'Profile edited',
    ];

    public function index(Request $request)
    {
        $filters = [
            'q'       => trim((string) $request->query('q', '')),
            'event'   => array_key_exists($request->query('event'), self::EVENTS) ? $request->query('event') : '',
            'country' => trim((string) $request->query('country', '')),
            'ip'      => trim((string) $request->query('ip', '')),
            'user_id' => (int) $request->query('user_id', 0),
        ];

        $query = DB::table('user_location_logs as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->leftJoin('users_profiles as p', 'p.id', '=', 'l.profile_id')
            ->select('l.*', 'u.name as user_name', 'u.email as user_email', 'p.name as profile_name')
            ->when($filters['user_id'], fn ($q, $id) => $q->where('l.user_id', $id))
            ->when($filters['event'], fn ($q, $e) => $q->where('l.event', $e))
            ->when($filters['country'], fn ($q, $c) => $q->where('l.country', $c))
            ->when($filters['ip'], fn ($q, $ip) => $q->where('l.ip', $ip))
            ->when($filters['q'], function ($q, $term) {
                $like = '%' . $term . '%';
                $q->where(fn ($w) => $w->where('u.name', 'like', $like)
                    ->orWhere('u.email', 'like', $like)
                    ->orWhere('l.ip', 'like', $like)
                    ->orWhere('p.name', 'like', $like));
            });

        // Totals for the strip above the table, over the same filters.
        $stats = (clone $query)->reorder()->selectRaw(
            'COUNT(*) as events, COUNT(DISTINCT l.user_id) as users, COUNT(DISTINCT l.ip) as ips, COUNT(DISTINCT l.country) as countries'
        )->first();

        $logs = $query->orderByDesc('l.created_at')->orderByDesc('l.id')
            ->paginate(50)->withQueryString();

        // A single user's view: who they are + every country they've been seen in.
        $user = $filters['user_id'] ? User::find($filters['user_id']) : null;
        $userCountries = $user
            ? DB::table('user_location_logs')->where('user_id', $user->id)->whereNotNull('country')
                ->selectRaw('country, COUNT(*) as times, MAX(created_at) as last_seen')
                ->groupBy('country')->orderByDesc('last_seen')->get()
            : collect();

        $countries = DB::table('user_location_logs')->whereNotNull('country')
            ->distinct()->orderBy('country')->pluck('country');

        return view('admin.user-locations.index', [
            'logs'          => $logs,
            'stats'         => $stats,
            'filters'       => $filters,
            'events'        => self::EVENTS,
            'countries'     => $countries,
            'user'          => $user,
            'userCountries' => $userCountries,
        ]);
    }
}
