<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::query()
            ->when($request->filled('causer_guard'), fn ($q) => $q->where('causer_guard', $request->causer_guard))
            ->when($request->filled('event'), fn ($q) => $q->where('event', 'like', "%{$request->event}%"))
            ->when($request->filled('search'), fn ($q) => $q->where('description', 'like', "%{$request->search}%"))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.activity-logs.index', ['logs' => $logs]);
    }
}
