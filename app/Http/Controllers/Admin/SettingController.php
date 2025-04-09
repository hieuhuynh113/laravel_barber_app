<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Lấy tất cả cài đặt và nhóm chúng theo nhóm
        $generalSettings = Setting::where('group', 'general')->get()->pluck('value', 'key');
        $contactSettings = Setting::where('group', 'contact')->get()->pluck('value', 'key');
        $socialSettings = Setting::where('group', 'social')->get()->pluck('value', 'key');
        $seoSettings = Setting::where('group', 'seo')->get()->pluck('value', 'key');
        
        return view('admin.settings.index', compact(
            'generalSettings',
            'contactSettings',
            'socialSettings',
            'seoSettings'
        ));
    }
    
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'admin_email' => 'required|email',
            'working_hours' => 'required|string|max:255',
        ]);
        
        $this->updateSettings([
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'admin_email' => $request->admin_email,
            'working_hours' => $request->working_hours,
        ], 'general');
        
        if ($request->hasFile('logo')) {
            $logoSetting = Setting::where('key', 'logo')->first();
            
            if ($logoSetting && $logoSetting->value) {
                Storage::disk('public')->delete($logoSetting->value);
            }
            
            $logoPath = $request->file('logo')->store('settings', 'public');
            $this->updateSetting('logo', $logoPath, 'general');
        }
        
        if ($request->hasFile('favicon')) {
            $faviconSetting = Setting::where('key', 'favicon')->first();
            
            if ($faviconSetting && $faviconSetting->value) {
                Storage::disk('public')->delete($faviconSetting->value);
            }
            
            $faviconPath = $request->file('favicon')->store('settings', 'public');
            $this->updateSetting('favicon', $faviconPath, 'general');
        }
        
        // Xóa cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cài đặt chung đã được cập nhật thành công.');
    }
    
    public function updateContact(Request $request)
    {
        $request->validate([
            'contact_address' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email',
            'google_map' => 'nullable|string',
        ]);
        
        $this->updateSettings([
            'contact_address' => $request->contact_address,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'google_map' => $request->google_map,
        ], 'contact');
        
        // Xóa cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cài đặt liên hệ đã được cập nhật thành công.');
    }
    
    public function updateSocial(Request $request)
    {
        $request->validate([
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'linkedin' => 'nullable|url',
        ]);
        
        $this->updateSettings([
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'tiktok' => $request->tiktok,
            'linkedin' => $request->linkedin,
        ], 'social');
        
        // Xóa cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cài đặt mạng xã hội đã được cập nhật thành công.');
    }
    
    public function updateSeo(Request $request)
    {
        $request->validate([
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'meta_keywords' => 'nullable|string',
            'google_analytics' => 'nullable|string',
            'facebook_pixel' => 'nullable|string',
        ]);
        
        $this->updateSettings([
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'google_analytics' => $request->google_analytics,
            'facebook_pixel' => $request->facebook_pixel,
        ], 'seo');
        
        // Xóa cache
        $this->clearSettingsCache();
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cài đặt SEO đã được cập nhật thành công.');
    }
    
    /**
     * Cập nhật nhiều cài đặt một lúc
     */
    private function updateSettings(array $settings, string $group)
    {
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value, $group);
        }
    }
    
    /**
     * Cập nhật một cài đặt
     */
    private function updateSetting(string $key, $value, string $group)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
    
    /**
     * Xóa cache cài đặt
     */
    private function clearSettingsCache()
    {
        Cache::forget('settings');
    }
} 