<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneralSettings\Setting;
use Illuminate\Support\Facades\Cache;

class UniformSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'show_uniform_section'],
            ['value' => '1'] // 1 = show, 0 = hide
        );

        // Clear settings cache to ensure the new setting is loaded
        Cache::forget('app_settings');
    }
}
