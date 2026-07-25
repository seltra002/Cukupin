# Package Composer yang perlu ditambahkan

Jalankan di project Laravel fresh kamu:

```bash
composer require livewire/livewire
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require irazasyed/telegram-bot-sdk
```

- **livewire/livewire** — dipakai di semua modul (Dashboard, Items, Wallets, dst)
- **laravel/breeze** — scaffolding auth (login/register/password reset), lalu di-custom dikit di `RegisteredUserController` biar bikin Household otomatis (lihat `app/Http/Controllers/Auth/RegisteredUserController.php` di paket ini)
- **maatwebsite/excel** — buat export laporan ke Excel
- **barryvdh/laravel-dompdf** — buat export laporan ke PDF
- **irazasyed/telegram-bot-sdk** — opsional, sebenernya webhook controller di paket ini udah pakai `Http::post()` polos (nggak wajib SDK), tapi kalau mau command handling yang lebih rapi bisa pakai SDK ini.
