<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\ProxyProviders\ProxyProviderFactory;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    protected function driverOptions(): array
    {
        return [
            \App\ProxyProviders\Drivers\BearerTokenProviderDriver::class => 'Bearer Token Provider',
            \App\ProxyProviders\Drivers\HeaderTokenProviderDriver::class => 'Header Token Provider',
        ];
    }

    public function index()
    {
        return view('admin.providers.index', ['providers' => Provider::orderBy('priority')->get()]);
    }

    public function create()
    {
        return view('admin.providers.create', ['drivers' => $this->driverOptions()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'driver' => ['required', 'string'],
            'api_url' => ['required', 'url'],
            'api_key' => ['required', 'string'],
            'priority' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Provider::create(array_merge($data, ['is_active' => true]));

        return redirect()->route('admin.providers.index')->with('success', 'Provider added. It will start appearing in the catalog after the next sync.');
    }

    public function edit(Provider $provider)
    {
        return view('admin.providers.edit', ['provider' => $provider, 'drivers' => $this->driverOptions()]);
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'api_url' => ['required', 'url'],
            'api_key' => ['nullable', 'string'],
            'priority' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        if (empty($data['api_key'])) {
            unset($data['api_key']);
        }

        $provider->update(array_merge($data, ['is_active' => $request->boolean('is_active', $provider->is_active)]));

        return back()->with('success', 'Provider updated.');
    }

    public function destroy(Provider $provider)
    {
        // Orders using this provider are NOT deleted — only the provider row is removed.
        $provider->delete();

        return back()->with('success', 'Provider removed.');
    }

    public function toggle(Provider $provider)
    {
        $provider->update(['is_active' => ! $provider->is_active]);

        return back()->with('success', $provider->is_active ? 'Provider activated.' : 'Provider deactivated.');
    }

    public function refreshBalance(Provider $provider)
    {
        try {
            $driver = ProxyProviderFactory::make($provider);
            $balance = $driver->getBalance();

            $provider->update([
                'cached_balance' => $balance,
                'cached_balance_currency' => $driver->getBalanceCurrency(),
                'balance_checked_at' => now(),
            ]);

            return back()->with('success', "Balance refreshed: {$balance} {$driver->getBalanceCurrency()}");
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to refresh balance: {$e->getMessage()}");
        }
    }

    public function refreshAll()
    {
        \Illuminate\Support\Facades\Artisan::call('providers:sync-balances');

        return back()->with('success', 'All provider balances refreshed.');
    }
}
