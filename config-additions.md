# Tambahan di config/services.php

Tambahkan array berikut ke `config/services.php` (bawaan Laravel):

```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
],
```

# Tambahan di bootstrap/app.php (Laravel 11)

Daftarkan middleware alias `owner` dan `can_input`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'owner' => \App\Http\Middleware\EnsureOwner::class,
        'can_input' => \App\Http\Middleware\EnsureCanInput::class,
    ]);
})
```

# Tambahan di config/livewire.php

Publish config-nya dulu: `php artisan livewire:publish --config`, lalu set:

```php
'layout' => 'components.layouts.app',
```

Ini bikin semua full-page Livewire component (Dashboard, ItemList, dst) otomatis pakai layout sidebar yang udah dibikin (`resources/views/components/layouts/app.blade.php`), tanpa perlu nulis `->layout()` di tiap component. File layout ini sengaja ditaruh di `resources/views/components/` (bukan `resources/views/layouts/`) biar bisa dipanggil juga sebagai `<x-layouts.app>` di view biasa (dipakai di `resources/views/profile/telegram-link.blade.php`).

# Override RegisteredUserController

Breeze generate route register ke `App\Http\Controllers\Auth\RegisteredUserController`. File custom-nya udah ada di paket ini (`app/Http/Controllers/Auth/RegisteredUserController.php`) — cukup **timpa file bawaan Breeze** dengan file ini. Jangan lupa tambah field `household_name` di form register (`resources/views/auth/register.blade.php`) yang di-generate Breeze.
