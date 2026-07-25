# Package Composer

Semua package udah didaftarin di `composer.json` — cukup `composer install`.

- **laravel/framework** ^12.0 — Laravel 11 udah EOL (nggak dapet security patch lagi sejak 12 Maret 2026), jadi paket ini pakai Laravel 12 yang masih aktif disupport sampai Feb 2027.
- **livewire/livewire** ^3.5
- **barryvdh/laravel-dompdf** — buat export laporan ke PDF (implementasi export-nya belum ditulis, tinggal bikin class-nya)

## Soal export Excel

`maatwebsite/excel` **sengaja belum dimasukin** ke composer.json. Versi yang aman dari security-block butuh extension `ext-gd`, yang defaultnya nggak ke-install otomatis di Railway (Railpack cuma install extension yang kedetect dibutuhin dari composer.json). Karena fitur export Excel-nya juga belum ada implementasinya di paket ini, ini paling aman dilepas dulu daripada bikin build gagal buat dependency yang belum kepake.

Kalau nanti mau nambahin export Excel:
1. `composer require maatwebsite/excel`
2. Tambahin `"ext-gd": "*"` ke `require` di composer.json (biar Railpack auto-install extension gd-nya)
3. Baru bikin Export class-nya
