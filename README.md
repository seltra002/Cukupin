# Cukupin — Sistem Pencatatan Kebutuhan Rumah Tangga (MVP)

Paket ini berisi **source code aplikasi** (models, migrations, Livewire components, views, routes) yang siap ditaruh di atas instalasi Laravel 11 fresh. Karena environment yang dipakai buat nulis kode ini nggak punya akses ke Packagist, kamu perlu jalanin `composer install` di mesin/CI kamu sendiri — semua source di sini murni PHP/Blade, nggak butuh proses "build" tambahan di luar itu.

## Isi Paket

```
app/Models/            → 13 model (Household, User, Item, Wallet, Debt, SavingsGoal, dst)
app/Livewire/          → 6 komponen full-page (Dashboard, Items, Wallets, Debts, Savings, Users)
app/Services/          → WalletService (logika mutasi dompet, transfer, koneksi ke tabungan)
app/Http/Middleware/   → EnsureOwner, EnsureCanInput
app/Http/Controllers/  → TelegramWebhookController, RegisteredUserController (override Breeze)
database/migrations/   → 14 migration (lengkap semua tabel)
database/seeders/      → Demo data end-to-end
resources/views/       → Layout + semua Blade view Livewire, styling brand Cukupin
routes/web.php         → Semua route + middleware
tailwind.config.js     → Design token brand Cukupin (warna, font)
nixpacks.toml          → Config build & start buat Railway
.env.example            → Template environment variable
composer-packages.md   → Daftar package Composer yang perlu ditambahkan
config-additions.md    → Potongan config yang perlu ditempel manual (services.php, bootstrap/app.php, dst)
```

## 1. Setup Project Lokal

```bash
# 1. Bikin project Laravel baru
composer create-project laravel/laravel cukupin
cd cukupin

# 2. Install package yang dibutuhkan (detail di composer-packages.md)
composer require livewire/livewire
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require maatwebsite/excel barryvdh/laravel-dompdf

# 3. Copy semua isi paket ini ke folder project (timpa yang bentrok, misal RegisteredUserController)
#    - app/, database/migrations/, database/seeders/, resources/views/, routes/web.php,
#      tailwind.config.js, resources/css/app.css, package.json, vite.config.js, nixpacks.toml,
#      .env.example, .gitignore

# 4. Terapkan potongan config manual — ikuti config-additions.md:
#    - config/services.php (tambah blok telegram)
#    - bootstrap/app.php (daftar middleware alias owner & can_input)
#    - config/livewire.php (set default layout)
#    - resources/views/auth/register.blade.php (tambah field household_name)

# 5. Install dependency JS & build asset
npm install
npm run build

# 6. Setup environment
cp .env.example .env
php artisan key:generate

# 7. Migrasi & seed database (pastikan DB lokal udah jalan & disetting di .env)
php artisan migrate --seed

# 8. Jalanin lokal
php artisan serve
```

Login demo (dari seeder):
- **Owner:** owner@cukupin.test / password
- **User (bisa input):** user@cukupin.test / password

## 2. Push ke GitHub

```bash
git init
git add .
git commit -m "Initial commit: Cukupin MVP"
gh repo create cukupin --private --source=. --push
# atau manual: bikin repo kosong di github.com, lalu
# git remote add origin https://github.com/<username>/cukupin.git
# git push -u origin main
```

## 3. Deploy ke Railway

1. **Bikin project baru di Railway** → "Deploy from GitHub repo" → pilih repo `cukupin`.
2. **Tambah plugin MySQL** di project yang sama (Railway → "+ New" → Database → MySQL).
3. Railway otomatis kasih variabel `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD` ke service MySQL-nya. Di service **web** kamu, buka tab Variables dan **map** ke nama yang dipakai Laravel:
   ```
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   ```
4. Generate `APP_KEY` di lokal (`php artisan key:generate --show`) lalu paste hasilnya sebagai variabel `APP_KEY` di Railway — **jangan** biarkan Railway generate ulang tiap deploy, nanti data terenkripsi kebaca beda tiap restart.
5. Set variabel lain sesuai `.env.example` (`APP_URL` diisi domain Railway kamu, `TELEGRAM_BOT_TOKEN`, dll).
6. Railway otomatis detect `nixpacks.toml` di root repo dan jalanin build + migrate + start sesuai isinya.
7. Setelah deploy sukses, jalanin seed sekali lewat Railway CLI kalau perlu demo data:
   ```bash
   railway run php artisan db:seed
   ```

## 4. Setup Bot Telegram (Gratis)

1. Chat **@BotFather** di Telegram → `/newbot` → ikuti instruksi → catat token yang dikasih.
2. Masukin token itu ke variabel `TELEGRAM_BOT_TOKEN` di Railway.
3. Daftarkan webhook (jalankan sekali dari browser/Postman, ganti `<TOKEN>` dan `<APP_URL>`):
   ```
   https://api.telegram.org/bot<TOKEN>/setWebhook?url=<APP_URL>/telegram/webhook
   ```
4. Buka bot di Telegram, `/start`, lalu ambil kode verifikasi dari halaman **Profil & Telegram** di web, kirim ke bot buat nyambungin akun.

## Catatan Penting

- **Nggak ada payment gateway/QRIS** — semua nominal di modul Dompet/Utang-Piutang/Tabungan itu pencatatan manual, bukan transaksi uang riil.
- **WalletService** (`app/Services/WalletService.php`) yang jaga konsistensi saldo: validasi `allow_negative`, catat mutasi transfer sebagai 2 entri, dan otomatis motong saldo dompet kalau setoran tabungan pilih sumber dompet.
- Kolom `stock_status` di modul Kebutuhan masih manual (owner/user pilih aman/menipis/habis pas input). Kalau mau full-otomatis (misal berdasarkan pola konsumsi), itu logika tambahan buat fase berikutnya.
- Export Excel/PDF di modul Laporan **belum ditulis di paket ini** — perlu ditambah pakai `maatwebsite/excel` dan `barryvdh/laravel-dompdf` yang udah di-list di dependency. Kasih tau kalau mau gw susulin komponen Laporan + export-nya.
