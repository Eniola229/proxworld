<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingHit;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingLinkController extends Controller
{
    public function index()
    {
        $links = TrackingLink::latest('id')->paginate(20);

        $stats = TrackingHit::select(
                'source', 'medium', 'campaign',
                DB::raw('count(*) as hits'),
                DB::raw('count(distinct tracking_session_id) as sessions')
            )
            ->groupBy('source', 'medium', 'campaign')
            ->get()
            ->keyBy(fn ($r) => $r->source . '|' . $r->medium . '|' . $r->campaign);

        return view('admin.tracking.links', compact('links', 'stats'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'source'   => $this->normalizeSource($request->input('source')),
            'medium'   => $this->lower($request->input('medium')),
            'campaign' => $this->lower($request->input('campaign')),
        ]);

        $data = $request->validate([
            'label'        => ['nullable', 'string', 'max:120'],
            'source'       => ['required', 'string', 'max:120', 'regex:/^[a-z0-9][a-z0-9._\-]*$/'],
            'medium'       => ['nullable', 'string', 'max:120'],
            'campaign'     => ['nullable', 'string', 'max:120'],
            'content'      => ['nullable', 'string', 'max:150'],
            'term'         => ['nullable', 'string', 'max:150'],
            'landing_path' => ['nullable', 'string', 'max:255', 'regex:/^\/[^\s]*$/'],
        ], [
            'source.regex'       => 'Source can only contain letters, numbers, dots, dashes and underscores (e.g. x.com or x).',
            'landing_path.regex' => 'Landing path must start with / and contain no spaces (e.g. /register).',
        ]);

        $data['landing_path'] = $data['landing_path'] ?? '/';

        $exists = TrackingLink::where('source', $data['source'])
            ->where('medium', $data['medium'] ?? null)
            ->where('campaign', $data['campaign'] ?? null)
            ->where('landing_path', $data['landing_path'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'A link with the same source, medium, campaign and landing path already exists.');
        }

        TrackingLink::create($data);

        return redirect()->route('admin.tracking-links.index')->with('success', 'Tracking link created.');
    }

    public function destroy(TrackingLink $trackingLink)
    {
        $trackingLink->delete();

        return redirect()->route('admin.tracking-links.index')
            ->with('success', 'Tracking link deleted. Recorded traffic was kept.');
    }

    private function normalizeSource(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('#^https?://#', '', $value);
        $value = preg_replace('#^www\.#', '', $value);

        return explode('/', $value)[0];
    }

    private function lower(?string $value): ?string
    {
        $value = mb_strtolower(trim((string) $value));

        return $value === '' ? null : $value;
    }
}