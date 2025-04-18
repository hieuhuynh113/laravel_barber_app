<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Lấy giá trị từ bảng settings
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            return $setting->value;
        }
        
        return $default;
    }
}
