<?php

return [
    'admin_username' => env('ARTISAN_ADMIN_USERNAME', 'admin'),
    'admin_table' => env('ARTISAN_ADMIN_TABLENAME', 'users'),
    'admin_column' => env('ARTISAN_ADMIN_COLUMN', 'email'),
    'admin_default_pass' => env('ARTISAN_ADMIN_DEFAULT_PASS', 'admin123'),
    'terminal_name' => env('ARTISAN_TERMINAL_NAME','onlineArtisan'),
    'guest_user_name' => env('ARTISAN_GUEST_NAME','guest@artisan'),
];