<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('social_links', [
            'telegram_channel' => 'https://t.me/proxworldhq',
            'whatsapp_channel' => 'https://whatsapp.com/channel/0029VbEIyPKFi8xhoHYXrk2i',
            'telegram_support' => 'https://t.me/proxworld_support',
            'tiktok' => 'https://www.tiktok.com/@proxworldhq',
            'instagram' => 'https://www.instagram.com/proxworldhq',
        ]);

        Setting::set('pricing_config', [
            'default_markup' => 30,
            'minimum_markup' => 10,
            'maximum_markup' => 200,
            'currency_buffer' => 3,
            'round_prices' => true,
            'service_type_markup' => [],
            'platform_markup' => [],
        ]);

        Setting::set('brand', [
            'name' => 'ProxWorld',
            'primary_color' => '#16a34a',
        ]);
    }
}
