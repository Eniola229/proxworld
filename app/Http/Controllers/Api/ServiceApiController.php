<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderServiceCache;

class ServiceApiController extends Controller
{
    public function index()
    {
        $services = ProviderServiceCache::where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->get(['id', 'name', 'type', 'unit', 'raw_rate', 'raw_currency']);

        return response()->json(['data' => $services]);
    }
}
