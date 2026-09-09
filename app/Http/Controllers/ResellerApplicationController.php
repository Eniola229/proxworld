<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Setting;
use App\Types\ResellerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\CloudinaryService;

class ResellerApplicationController extends Controller
{

    public function __construct(private CloudinaryService $cloudinary) {}
    
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        if (!$reseller) {
            return view('reseller-panel.index', [
                'reseller' => null,
                'totalCustomers' => 0,
                'totalOrders' => 0,
                'totalRevenue' => 0,
                'customers' => collect(),
                'recentOrders' => collect(),
                'recentTransactions' => collect(),
            ]);
        }

        return view('reseller-panel.index', [
            'reseller' => $reseller,
            'totalCustomers' => $reseller->customers()->count(),
            'totalOrders' => $reseller->orders()->count(),
            'totalRevenue' => $reseller->orders()->sum('charge'),
            'customers' => $reseller->customers()
                ->withCount(['orders' => fn ($q) => $q->where('reseller_id', $reseller->id)])
                ->withSum(['orders' => fn ($q) => $q->where('reseller_id', $reseller->id)], 'charge')
                ->latest()
                ->take(10)
                ->get(),
            'recentOrders' => $reseller->orders()
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
            'recentTransactions' => $reseller->wallet()
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function create(Request $request)
    {
        abort_if($request->user()->reseller, 302, 'You already have a reseller panel.');

        return view('reseller-panel.create');
    }

    public function store(Request $request)
    {
        abort_if($request->user()->reseller, 422, 'You already have a reseller panel.');

        $data = $request->validate([
            'panel_name' => ['required', 'string', 'max:100'],
            'subdomain' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:resellers,subdomain'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'support_email' => ['nullable', 'email'],
            'support_telegram' => ['nullable', 'string', 'max:255'],
            'support_whatsapp' => ['nullable', 'string', 'max:255'],
        ]);

        Reseller::create([
            'owner_id' => $request->user()->id,
            'panel_name' => $data['panel_name'],
            'subdomain' => strtolower($data['subdomain']),
            'primary_color' => $data['primary_color'] ?? null,
            'support_email' => $data['support_email'] ?? null,
            'support_telegram' => $data['support_telegram'] ?? null,
            'support_whatsapp' => $data['support_whatsapp'] ?? null,
            'default_markup_percent' => Setting::get('pricing_config')['default_markup'] ?? 30,
            'status' => ResellerStatus::PENDING,
        ]);

        $request->user()->forceFill([
            'is_reseller' => true,
            'reseller_status' => ResellerStatus::PENDING,
        ])->save();

        return redirect()->route('reseller-panel.index')->with('success', 'Your reseller application has been submitted for review.');
    }

    public function update(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller, 404);

        $data = $request->validate([
            'panel_name' => ['required', 'string', 'max:100'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'support_email' => ['nullable', 'email'],
            'support_telegram' => ['nullable', 'string', 'max:255'],
            'support_whatsapp' => ['nullable', 'string', 'max:255'],
        ]);

        $reseller->update($data);

        return back()->with('success', 'Panel settings updated.');
    }

    public function updateDomain(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller, 404);

        $data = $request->validate([
            'custom_domain' => [
                'nullable',
                'string',
                'regex:/^([a-z0-9-]+\.)+[a-z]{2,}$/i',
                'max:255',
                'unique:resellers,custom_domain,' . $reseller->id,
            ],
        ]);

        $domain = $data['custom_domain'] ?? null;

        if (! $domain) {
            if ($reseller->custom_domain) {
                $this->removeDomainFromCpanel($reseller->custom_domain);
            }

            $reseller->update([
                'custom_domain' => null,
                'custom_domain_status' => null,
                'custom_domain_verified_at' => null,
                'custom_domain_error' => null,
            ]);

            return back()->with('success', 'Custom domain removed. Your panel will use the subdomain.');
        }

        $verification = $this->verifyDomainDNS($domain, $reseller->server_ip);

        if (! $verification['verified']) {
            $reseller->update([
                'custom_domain' => $domain,
                'custom_domain_status' => 'failed',
                'custom_domain_error' => $verification['error'],
            ]);

            return back()->with('error', 'Domain saved, but DNS isn\'t pointing at us yet: ' . $verification['error'] . ' Changes can take a few hours to propagate — click Verify again once you\'ve updated your DNS.');
        }

        $cpanelResult = $this->addDomainToCpanel($domain);

        $reseller->update([
            'custom_domain' => $domain,
            'custom_domain_status' => 'active',
            'custom_domain_verified_at' => now(),
            'custom_domain_error' => null,
        ]);

        $message = 'Domain verified and saved! Your storefront is now live on your custom domain.';

        if (! $cpanelResult['success']) {
            Log::warning('cPanel addon domain failed for ' . $domain, $cpanelResult);
            $message .= ' (SSL setup may take a little longer — our team has been notified.)';
        }

        return back()->with('success', $message);
    }

    public function verifyDomain(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller && $reseller->custom_domain, 404);

        $verification = $this->verifyDomainDNS($reseller->custom_domain, $reseller->server_ip);

        if ($verification['verified']) {
            $reseller->update([
                'custom_domain_status' => 'active',
                'custom_domain_verified_at' => now(),
                'custom_domain_error' => null,
            ]);

            $this->addDomainToCpanel($reseller->custom_domain);

            return back()->with('success', 'Domain verified! Your storefront is now live on your custom domain.');
        }

        $reseller->update([
            'custom_domain_status' => 'failed',
            'custom_domain_error' => $verification['error'],
        ]);

        return back()->with('error', 'DNS not verified yet: ' . $verification['error'] . ' Changes can take a few hours to propagate — try again shortly.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DNS Verification
    // ─────────────────────────────────────────────────────────────────────────

    private function verifyDomainDNS(string $domain, ?string $serverIp): array
    {
        $domain = preg_replace('#^https?://#', '', $domain);
        $serverIp = $serverIp ?: ($_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()));

        try {
            $dnsRecords = dns_get_record($domain, DNS_A) ?: [];

            if (empty($dnsRecords)) {
                return [
                    'verified' => false,
                    'error' => 'No A record found for this domain. Please add an A record pointing to ' . $serverIp,
                ];
            }

            $found = false;
            foreach ($dnsRecords as $record) {
                if (($record['ip'] ?? null) === $serverIp) {
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                $currentIp = $dnsRecords[0]['ip'] ?? 'unknown';

                return [
                    'verified' => false,
                    'error' => "A record points to {$currentIp}, but should point to {$serverIp}",
                ];
            }

            return ['verified' => true, 'error' => null];

        } catch (\Exception $e) {
            return ['verified' => false, 'error' => 'DNS lookup failed'];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // cPanel API Methods
    // ─────────────────────────────────────────────────────────────────────────

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
                if (str_contains(strtolower($errors), 'already exists')) {
                    $this->triggerAutoSSL($domain);
                    return ['success' => true, 'note' => 'Domain already existed in cPanel.'];
                }
                return ['success' => false, 'error' => $errors];
            }

            $this->triggerAutoSSL($domain);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('cPanel addon domain exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

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
            Log::error('cPanel remove addon domain exception: ' . $e->getMessage());
        }
    }

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

     public function updateLogo(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller, 404);

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        // delete old logo from Cloudinary first, if one exists
        if ($reseller->logo_public_id) {
            $this->cloudinary->delete($reseller->logo_public_id, 'image');
        }

        $upload = $this->cloudinary->uploadImage($request->file('logo'), 'resellers/logos');

        $reseller->update([
            'logo_path' => $upload['url'],
            'logo_public_id' => $upload['public_id'],
        ]);

        return back()->with('success', 'Logo updated.');
    }
}