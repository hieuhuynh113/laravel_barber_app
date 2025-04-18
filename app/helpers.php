<?php

use App\Models\User;

if (!function_exists('get_user_avatar')) {
    /**
     * Lấy đường dẫn đến avatar của người dùng
     *
     * @param mixed $user User object hoặc user_id
     * @param string $size Kích thước avatar (small, medium, large)
     * @return string Đường dẫn đến avatar
     */
    function get_user_avatar($user, $size = 'medium')
    {
        // Nếu $user là ID, lấy đối tượng User
        if (is_numeric($user)) {
            $user = User::find($user);
        }

        // Nếu không tìm thấy user hoặc user không có avatar
        if (!$user || empty($user->avatar)) {
            return get_default_avatar($size);
        }

        // Kiểm tra nếu avatar được lưu trong storage/public
        if (strpos($user->avatar, 'avatars/') === 0) {
            return asset('storage/' . $user->avatar);
        }

        // Kiểm tra nếu avatar được lưu trong public/images/avatars
        if (strpos($user->avatar, 'images/avatars/') === 0) {
            return asset($user->avatar);
        }

        // Trường hợp khác, giả sử đường dẫn đầy đủ được cung cấp
        return asset($user->avatar);
    }
}

if (!function_exists('get_default_avatar')) {
    /**
     * Lấy avatar mặc định dựa trên kích thước
     *
     * @param string $size Kích thước avatar (small, medium, large)
     * @return string Đường dẫn đến avatar mặc định
     */
    function get_default_avatar($size = 'medium')
    {
        return asset('images/default-avatar.svg');
    }
}
