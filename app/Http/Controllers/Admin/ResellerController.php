<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResellerController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $resellers = Reseller::query()
            ->with('owner:id,name,email')
            ->withCount('orders')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('panel_name', 'like', "%{$request->search}%")
                ->orWhere('subdomain', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $this->logActivity('viewed', auth('admin')->user()->name . ' viewed resellers list', 'Reseller', null);

        return view('admin.resellers.index', compact('resellers'));
    }

    public function show(Reseller $reseller)
    {
        $reseller->load('owner');

        $totalRevenue     = $reseller->orders()->sum('charge');
        $totalProfit      = $reseller->orders()->where('status', 'completed')->sum('profit');
        $totalOrders      = $reseller->orders()->count();
        $totalCustomers   = $reseller->customers()->count();
        $recentOrders     = $reseller->orders()->with('user')->latest()->take(5)->get();
        $ownerBalance     = $reseller->owner->balance;
        $detectedServerIp = $this->getServerIp();

        $this->logViewed('Reseller', $reseller->id, auth('admin')->user()->name . ' viewed reseller [' . $reseller->subdomain . ']');

        return view('admin.resellers.show', compact(
            'reseller', 'totalRevenue', 'totalProfit',
            'totalOrders', 'totalCustomers', 'recentOrders',
            'ownerBalance', 'detectedServerIp'
        ));
    }

    public function wallet(Reseller $reseller)
    {
        $owner = $reseller->owner;

        $transactions = $reseller->wallet()->latest()->paginate(20);

        $this->logActivity('viewed',
            auth('admin')->user()->name . ' viewed wallet for reseller [' . $reseller->subdomain . ']',
            'Reseller', $reseller->id
        );

        return view('admin.resellers.wallet', compact('reseller', 'transactions', 'owner'));
    }

    public function customers(Reseller $reseller)
    {
        $customers = $reseller->customers()
            ->withCount(['orders' => fn ($q) => $q->where('reseller_id', $reseller->id)])
            ->latest()
            ->paginate(30);

        $this->logActivity('viewed',
            auth('admin')->user()->name . ' viewed customers for reseller [' . $reseller->subdomain . ']',
            'Reseller', $reseller->id
        );

        return view('admin.resellers.customers', compact('reseller', 'customers'));
   }

    public function orders(Reseller $reseller)
    {
        $orders = $reseller->orders()->with('user')->latest()->paginate(30);

        $totalCharge = $reseller->orders()->sum('charge');
        $totalProfit = $reseller->orders()->sum('profit');

        $this->logActivity('viewed',
            auth('admin')->user()->name . ' viewed orders for reseller [' . $reseller->subdomain . ']',
            'Reseller', $reseller->id
        );

        return view('admin.resellers.orders', compact('reseller', 'orders', 'totalCharge', 'totalProfit'));
    }

    public function withdrawals(Reseller $reseller)
    {
        $withdrawals = $reseller->withdrawals()->latest()->paginate(30);

        $this->logActivity('viewed',
            auth('admin')->user()->name . ' viewed withdrawals for reseller [' . $reseller->subdomain . ']',
            'Reseller', $reseller->id
        );

        return view('admin.resellers.withdrawals', compact('reseller', 'withdrawals'));
    }

    public function create()
    {
        // Admin-initiated reseller creation (rare — most come through self-service applications).
        $users = User::whereDoesntHave('reseller')->orderBy('name')->limit(200)->get();

        $this->logActivity('viewed', auth('admin')->user()->name . ' opened the manual reseller creation form', 'Reseller', null);

        return view('admin.resellers.create', compact('users'));
    }

    public function approve(Reseller $reseller)
    {
        $serverIp  = $this->getServerIp();
        $oldStatus = $reseller->status;

        $reseller->update([
            'status'           => 'active',
            'server_ip'        => $serverIp,
            'approved_at'      => now(),
            'rejection_reason' => null,
        ]);

        if ($reseller->custom_domain) {
            $result = $this->addDomainToCpanel($reseller->custom_domain);
            if (!$result['success']) {
                Log::warning('cPanel addon failed on approve for ' . $reseller->custom_domain, $result);
            }
        }

        $this->logUpdated('Reseller', $reseller->id,
            auth('admin')->user()->name . ' approved reseller [' . $reseller->subdomain . ']',
            ['status' => ['old' => $oldStatus, 'new' => 'active'], 'server_ip' => $serverIp]
        );

        return redirect()->route('admin.resellers.show', $reseller)->with('alert', [
            'type'    => 'success',
            'message' => 'Panel approved! Server IP ' . $serverIp . ' assigned.',
        ]);
    }

    public function reject(Request $request, Reseller $reseller)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $oldStatus = $reseller->status;

        $reseller->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_at'      => null,
        ]);

        $this->logUpdated('Reseller', $reseller->id,
            auth('admin')->user()->name . ' rejected reseller [' . $reseller->subdomain . ']',
            [
                'status'           => ['old' => $oldStatus, 'new' => 'rejected'],
                'rejection_reason' => $request->rejection_reason,
            ]
        );

        return redirect()->route('admin.resellers.show', $reseller)->with('alert', [
            'type'    => 'warning',
            'message' => 'Panel rejected. Reason: ' . $request->rejection_reason,
        ]);
    }

    public function updateStatus(Request $request, Reseller $reseller)
    {
        $request->validate(['status' => 'required|in:active,suspended,pending']);

        $oldStatus = $reseller->status;

        if ($request->status === 'active' && !$reseller->server_ip) {
            $reseller->server_ip   = $this->getServerIp();
            $reseller->approved_at = now();

            if ($reseller->custom_domain) {
                $this->addDomainToCpanel($reseller->custom_domain);
            }
        }

        if ($request->status === 'suspended' && $reseller->custom_domain) {
            $this->removeDomainFromCpanel($reseller->custom_domain);
        }

        $reseller->status = $request->status;
        $reseller->save();

        $this->logUpdated('Reseller', $reseller->id,
            auth('admin')->user()->name . ' updated reseller [' . $reseller->subdomain . '] status',
            ['status' => ['old' => $oldStatus, 'new' => $request->status]]
        );

        return back()->with('alert', [
            'type'    => 'success',
            'message' => 'Status updated to ' . $request->status,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // cPanel API Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Add a domain as cPanel Addon Domain pointing to the Laravel public folder.
     */
    private function addDomainToCpanel(string $domain): array
    {
        $cpanelUser  = config('cpanel.username');
        $cpanelToken = config('cpanel.api_token');
        $cpanelHost  = config('cpanel.host');
        $docRoot     = config('cpanel.doc_root');

        if (!$cpanelUser || !$cpanelToken || !$cpanelHost) {
            Log::warning('cPanel credentials not configured.');
            return ['success' => false, 'error' => 'cPanel credentials not configured.'];
        }

        $subdomainLabel = str_replace(['.', '-'], '_', $domain);

        try {
            $response = Http::withHeaders([
                'Authorization' => "cpanel {$cpanelUser}:{$cpanelToken}",
            ])
            ->timeout(15)
            ->post("https://{$cpanelHost}:2083/execute/AddonDomain/add_addon_domain", [
                'newdomain' => $domain,
                'subdomain' => $subdomainLabel,
                'dir'       => $docRoot,
            ]);

            $body = $response->json();

            Log::info('cPanel add_addon_domain response', [
                'domain'   => $domain,
                'response' => $body,
            ]);

            if (!empty($body['errors'])) {
                $errors = implode(', ', $body['errors']);
                // "Already exists" is fine — domain is already registered
                if (str_contains(strtolower($errors), 'already exists')) {
                    $this->triggerAutoSSL($domain);
                    return ['success' => true, 'note' => 'Domain already existed in cPanel.'];
                }
                return ['success' => false, 'error' => $errors];
            }

            // Trigger AutoSSL to issue free SSL cert
            $this->triggerAutoSSL($domain);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('cPanel addDomainToCpanel exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Remove an addon domain from cPanel (e.g. when suspended or domain removed).
     */
    private function removeDomainFromCpanel(string $domain): void
    {
        $cpanelUser  = config('cpanel.username');
        $cpanelToken = config('cpanel.api_token');
        $cpanelHost  = config('cpanel.host');

        if (!$cpanelUser || !$cpanelToken || !$cpanelHost) return;

        $subdomainLabel = str_replace(['.', '-'], '_', $domain);

        try {
            $response = Http::withHeaders([
                'Authorization' => "cpanel {$cpanelUser}:{$cpanelToken}",
            ])
            ->timeout(15)
            ->post("https://{$cpanelHost}:2083/execute/AddonDomain/remove_addon_domain", [
                'domain'    => $domain,
                'subdomain' => $subdomainLabel,
            ]);

            Log::info('cPanel remove_addon_domain response', [
                'domain'   => $domain,
                'response' => $response->json(),
            ]);

        } catch (\Exception $e) {
            Log::error('cPanel removeDomainFromCpanel exception: ' . $e->getMessage());
        }
    }

    /**
     * Trigger cPanel AutoSSL to issue a free SSL cert for the domain.
     */
    private function triggerAutoSSL(string $domain): void
    {
        $cpanelUser  = config('cpanel.username');
        $cpanelToken = config('cpanel.api_token');
        $cpanelHost  = config('cpanel.host');

        if (!$cpanelUser || !$cpanelToken || !$cpanelHost) return;

        try {
            Http::withHeaders([
                'Authorization' => "cpanel {$cpanelUser}:{$cpanelToken}",
            ])
            ->timeout(15)
            ->post("https://{$cpanelHost}:2083/execute/SSL/start_autossl_check_for_domain", [
                'domain' => $domain,
            ]);

            Log::info('cPanel AutoSSL triggered for: ' . $domain);

        } catch (\Exception $e) {
            Log::error('cPanel AutoSSL trigger exception: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Server IP Detection
    // ─────────────────────────────────────────────────────────────────────────

    private function getServerIp(): string
    {
        // Method 1: Server variable (most reliable on dedicated/VPS)
        if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
            return $_SERVER['SERVER_ADDR'];
        }

        // Method 2: Hostname resolution
        $hostname = gethostname();
        $ip = gethostbyname($hostname);
        if ($ip && $ip !== $hostname && $ip !== '127.0.0.1') {
            return $ip;
        }

        // Method 3: External API — most reliable on shared/cPanel hosting
        try {
            $publicIp = trim(file_get_contents('https://api.ipify.org'));
            if (filter_var($publicIp, FILTER_VALIDATE_IP)) {
                return $publicIp;
            }
        } catch (\Exception $e) {
            //
        }

        // Method 4: config override — set SERVER_IP=x.x.x.x in .env if all else fails
        if (config('cpanel.server_ip')) {
            return config('cpanel.server_ip');
        }

        return 'Contact support for server IP';
    }
}