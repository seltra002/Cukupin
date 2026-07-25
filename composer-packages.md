# Package Composer

Semua package udah didaftarin di `composer.json` paket ini — nggak perlu `composer require` manual lagi. Cukup jalanin:

```bash
composer install
```

Package yang kepake:
- **laravel/framework** ^11.31
- **livewire/livewire** ^3.5 — dipakai di semua modul (Dashboard, Items, Wallets, dst)
- **maatwebsite/excel** — buat export laporan ke Excel (implementasi export-nya belum ditulis di paket ini, tinggal bikin export class kalau udah siap)
- **barryvdh/laravel-dompdf** — buat export laporan ke PDF (sama, tinggal diimplementasi)

Auth (login/register) dibikin **custom** tanpa Laravel Breeze, jadi nggak ada dependency tambahan buat itu — lihat `app/Http/Controllers/Auth/` dan `resources/views/auth/`.
