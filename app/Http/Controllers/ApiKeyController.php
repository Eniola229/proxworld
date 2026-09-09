<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        return view('api.index', ['apiKeys' => $request->user()->apiKeys()->latest()->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $plainKey = \App\Models\ApiKey::generatePlainKey();

        $apiKey = \App\Models\ApiKey::create([
            'user_id'       => $request->user()->id,
            'name'          => $request->name,
            'status'        => 'active',
            'key_hash'      => \App\Models\ApiKey::hashKey($plainKey),
            'key_encrypted' => $plainKey,
            'key_preview'   => substr($plainKey, -8),
        ]);

        return back()->with([
            'success'   => 'API key created. Copy it now — you won\'t be able to see it again.',
            'plain_key' => $plainKey,
        ]);
    }

    public function destroy(Request $request, ApiKey $apiKey)
    {
        abort_unless($apiKey->user_id === $request->user()->id, 403);
        $apiKey->delete();

        return back()->with('success', 'API key revoked.');
    }

    public function toggle(Request $request, ApiKey $apiKey)
    {
        abort_unless($apiKey->user_id === $request->user()->id, 403);
        $apiKey->update(['status' => $apiKey->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', 'API key status updated.');
    }

    /** Quick sanity check the key works — hits our own /api/v1/balance using it. */
    public function test(Request $request, ApiKey $apiKey)
    {
        abort_unless($apiKey->user_id === $request->user()->id, 403);

        $response = \Illuminate\Support\Facades\Http::withToken($apiKey->key_encrypted)
            ->get(url('/api/v1/balance'));

        return response()->json(['status' => $response->status(), 'body' => $response->json()]);
    }

    public function docs()
    {
        return view('api.docs');
    }
}
