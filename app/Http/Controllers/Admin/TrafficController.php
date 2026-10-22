<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingHit;
use App\Models\TrackingLink;
use App\Models\TrackingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrafficController extends Controller
{
    public function index(Request $request)
    {
        $filtered = $this->filtered($request);

        $sessionsCount = (clone $filtered)->count();
        $registered    = (clone $filtered)->whereNotNull('user_id')->count();

        $stats = [
            'sessions'   => $sessionsCount,
            'registered' => $registered,
            'hits'       => (int) (clone $filtered)->sum('hits_count'),
            'conversion' => $sessionsCount > 0 ? round($registered / $sessionsCount * 100, 1) : 0,
        ];

        $topSources = TrackingHit::select(
                'source',
                DB::raw('count(*) as hits'),
                DB::raw('count(distinct tracking_session_id) as sessions')
            )
            ->whereIn('tracking_session_id', (clone $filtered)->select('tracking_sessions.id'))
            ->groupBy('source')
            ->orderByDesc('hits')
            ->limit(8)
            ->get();

        $topCountries = (clone $filtered)
            ->select('country_code', 'country_name', DB::raw('count(*) as total'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $sessions = (clone $filtered)
            ->with('user:id,name,email')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $sources = TrackingHit::query()->distinct()->pluck('source')
            ->merge(TrackingLink::pluck('source'))
            ->unique()->sort()->values();

        $countries = TrackingSession::whereNotNull('country_code')
            ->select('country_code', 'country_name')
            ->distinct()
            ->orderBy('country_name')
            ->get();

        return view('admin.tracking.index', compact(
            'sessions', 'stats', 'topSources', 'topCountries', 'sources', 'countries'
        ));
    }

    public function show(TrackingSession $trackingSession)
    {
        $trackingSession->load('user');

        $hits = $trackingSession->hits()->orderByDesc('id')->paginate(25);

        return view('admin.tracking.show', [
            'trackSession' => $trackingSession,
            'hits'         => $hits,
        ]);
    }

    private function filtered(Request $request)
    {
        return TrackingSession::query()
            ->when($request->filled('source'), fn ($q) => $q->whereHas(
                'hits', fn ($h) => $h->where('source', $request->input('source'))
            ))
            ->when($request->filled('country'), fn ($q) => $q->where('country_code', $request->input('country')))
            ->when($request->filled('device_type'), fn ($q) => $q->where('device_type', $request->input('device_type')))
            ->when($request->input('registered') === 'yes', fn ($q) => $q->whereNotNull('user_id'))
            ->when($request->input('registered') === 'no', fn ($q) => $q->whereNull('user_id'))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('date_to')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = trim($request->input('search'));

                $q->where(function ($w) use ($s) {
                    $w->where('ip_address', 'like', "%{$s}%")
                      ->orWhere('visitor_token', 'like', "%{$s}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")
                          ->orWhere('email', 'like', "%{$s}%"));
                });
            });
    }
}