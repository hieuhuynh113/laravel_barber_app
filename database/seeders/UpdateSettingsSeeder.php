<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Các cài đặt thuộc nhóm 'general'
        $generalSettings = [
            'shop_name', 'site_name', 'site_description', 'admin_email', 
            'working_hours', 'logo', 'favicon'
        ];
        
        // Các cài đặt thuộc nhóm 'contact'
        $contactSettings = [
            'shop_address', 'shop_phone', 'shop_email', 'contact_address',
            'contact_phone', 'contact_email', 'google_map'
        ];
        
        // Các cài đặt thuộc nhóm 'social'
        $socialSettings = [
            'facebook', 'twitter', 'instagram', 'youtube', 'tiktok', 'linkedin'
        ];
        
        // Các cài đặt thuộc nhóm 'seo'
        $seoSettings = [
            'meta_title', 'meta_description', 'meta_keywords', 
            'google_analytics', 'facebook_pixel'
        ];
        
        // Cập nhật nhóm cho các cài đặt
        foreach ($generalSettings as $key) {
            $this->updateSettingGroup($key, 'general');
        }
        
        foreach ($contactSettings as $key) {
            $this->updateSettingGroup($key, 'contact');
        }
        
        foreach ($socialSettings as $key) {
            $this->updateSettingGroup($key, 'social');
        }
        
        foreach ($seoSettings as $key) {
            $this->updateSettingGroup($key, 'seo');
        }
        
        $this->command->info('Đã cập nhật thành công nhóm cho các cài đặt!');
    }
    
    /**
     * Cập nhật nhóm cho cài đặt theo key
     */
    private function updateSettingGroup($key, $group)
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            $setting->group = $group;
            $setting->save();
            $this->command->line("Đã cập nhật cài đặt '{$key}' thuộc nhóm '{$group}'");
        }
    }
}
