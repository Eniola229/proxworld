@php
    $social = \App\Models\Setting::get('social_links', []);
    $shouldShow = auth()->check() && ! auth()->user()->welcome_modal_seen_at;
@endphp

@if($shouldShow)
<div id="welcome-channels-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 relative shadow-xl">
        <button onclick="dismissWelcomeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" aria-label="Close">
            &times;
        </button>

        <h3 class="text-lg font-bold text-gray-900 mb-1">Welcome to ProxWorld 👋</h3>
        <p class="text-sm text-gray-500 mb-5">Join our community channels for updates, tips, and support.</p>

        <div class="space-y-3">
            @if(!empty($social['telegram_channel']))
            <a href="{{ $social['telegram_channel'] }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 hover:border-[#16a34a] transition">
                <span class="w-9 h-9 rounded-full bg-[#e6f4ea] flex items-center justify-center text-[#16a34a] font-bold">T</span>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Telegram Channel</p>
                    <p class="text-xs text-gray-500">News & updates</p>
                </div>
            </a>
            @endif

            @if(!empty($social['whatsapp_channel']))
            <a href="{{ $social['whatsapp_channel'] }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 hover:border-[#16a34a] transition">
                <span class="w-9 h-9 rounded-full bg-[#e6f4ea] flex items-center justify-center text-[#16a34a] font-bold">W</span>
                <div>
                    <p class="text-sm font-semibold text-gray-900">WhatsApp Channel</p>
                    <p class="text-xs text-gray-500">News & updates</p>
                </div>
            </a>
            @endif

            @if(!empty($social['telegram_support']))
            <a href="{{ $social['telegram_support'] }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 hover:border-[#16a34a] transition">
                <span class="w-9 h-9 rounded-full bg-[#e6f4ea] flex items-center justify-center text-[#16a34a] font-bold">S</span>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Telegram Support</p>
                    <p class="text-xs text-gray-500">Chat with our team directly</p>
                </div>
            </a>
            @endif
        </div>

        @if(!empty($social['tiktok']) || !empty($social['instagram']))
        <div class="flex items-center justify-center gap-4 mt-5 pt-4 border-t border-gray-100">
            @if(!empty($social['tiktok']))
                <a href="{{ $social['tiktok'] }}" target="_blank" rel="noopener" class="text-xs text-gray-500 hover:text-[#16a34a]">TikTok @proxworldhq</a>
            @endif
            @if(!empty($social['instagram']))
                <a href="{{ $social['instagram'] }}" target="_blank" rel="noopener" class="text-xs text-gray-500 hover:text-[#16a34a]">Instagram @proxworldhq</a>
            @endif
        </div>
        @endif

        <button onclick="dismissWelcomeModal()" class="mt-5 w-full bg-[#16a34a] text-white rounded-lg py-2.5 text-sm font-semibold hover:bg-[#15803d] transition">
            Got it, thanks!
        </button>
    </div>
</div>

<script>
    function dismissWelcomeModal() {
        const modal = document.getElementById('welcome-channels-modal');
        if (modal) modal.remove();

        fetch("{{ route('welcome-modal.dismiss') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
        }).catch(() => {}); // non-critical — worst case it shows again next visit
    }
</script>
@endif
