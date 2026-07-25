# Cukupin — Sistem Pencatatan Kebutuhan Rumah Tangga (MVP)

Project Laravel 11 + Livewire 3 **lengkap**, siap langsung di-`composer install` lalu di-deploy ke Railway. Nggak perlu `composer create-project` lagi — semua file skeleton (composer.json, artisan, bootstrap, public/index.php, config/*, migration default) udah ada di paket ini.

## Isi Paket

```
app/                    → Models, Livewire components, Controllers, Middleware, Services
bootstrap/app.php       → Konfigurasi routing & middleware alias (owner, can_input) - udah baked in
config/                 → Semua config Laravel (services.php udah ada Telegram, livewire.php udah ada layout)
database/migrations/    → Migration default Laravel + 14 migration modul Cukupin
database/seeders/       → Demo data end-to-end
public/index.php        → Entry point
resources/views/        → Layout, view auth (login/register custom), view Livewire
routes/web.php          → Semua route (auth + modul), udah lengkap
composer.json           → Semua dependency PHP
package.json            → Tailwind, Vite
```

## 1. Setup Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Setting koneksi database di `.env` (MySQL lokal, atau pakai SQLite: `DB_CONNECTION=sqlite` + `touch database/database.sqlite`).

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Login demo (dari seeder):
- **Owner:** owner@cukupin.test / password
- **User (bisa input):** user@cukupin.test / password

Coba klik-klik semua menu dulu di lokal sebelum push, pastiin nggak ada error 500.

## 2. Push ke GitHub

```bash
git init
git add .
git commit -m "Initial commit: Cukupin MVP"
gh repo create cukupin --private --source=. --push
```

Pastiin struktur repo di GitHub kelihatan kayak gini (root repo harus ada `composer.json` dan `artisan`):
```
app/  bootstrap/  config/  database/  public/  resources/  routes/
composer.json  artisan  package.json  vite.config.js  tailwind.config.js  .env.example
```

## 3. Deploy ke Railway

Railway pakai builder **Railpack** (auto-detect, zero-config) — begitu `composer.json` & `artisan` kedetect di root repo, dia otomatis ngerti ini project Laravel dan jalanin lewat FrankenPHP + Caddy, document root otomatis di-set ke `public/`.

1. **New Project** → **Deploy from GitHub repo** → pilih repo `cukupin`.
2. **Tambah plugin MySQL** di project yang sama (`+ New` → Database → MySQL).
3. Di service **web**, buka tab **Variables**, tambahin (klik Raw Editor biar cepat):
   ```
   APP_NAME=Cukupin
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=                      # generate dari lokal: php artisan key:generate --show
   APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   SESSION_DRIVER=database
   QUEUE_CONNECTION=database
   CACHE_STORE=database
   TELEGRAM_BOT_TOKEN=
   ```
   **Penting:** `APP_KEY` generate sekali dari lokal terus paste hasilnya — jangan biarin kosong/regenerate tiap deploy.
4. Di tab **Settings** service web, bagian **Deploy**, set **Custom Start Command**:
   ```
   php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
   ```
   (Railpack biasanya udah otomatis jalanin start command yang sesuai buat Laravel via FrankenPHP, tapi kalau servingnya nggak otomatis migrate, custom start command ini yang mastiin migration jalan tiap deploy.)
5. **Generate Domain** di tab Networking biar dapet URL publik.
6. Push ulang / trigger redeploy. Build sekarang harusnya kebaca sebagai PHP/Laravel, bukan static Vite site lagi.
7. Kalau perlu demo data: `railway run php artisan db:seed`.

## 4. Setup Bot Telegram (Gratis)

1. Chat **@BotFather** di Telegram → `/newbot` → catat token.
2. Masukin ke variabel `TELEGRAM_BOT_TOKEN` di Railway.
3. Daftarkan webhook (buka URL ini sekali di browser, ganti `<TOKEN>` & `<APP_URL>`):
   ```
   https://api.telegram.org/bot<TOKEN>/setWebhook?url=<APP_URL>/telegram/webhook
   ```
4. Buka bot, `/start`, ambil kode verifikasi dari halaman **Profil & Telegram** di web, kirim ke bot.

## Kenapa Kemarin Error

Build sebelumnya gagal karena repo yang di-push cuma berisi folder overlay (app/database/resources/routes doang), tanpa `composer.json`/`artisan`/`public/`/`bootstrap/` — jadi Railway (Railpack) nggak ngedetect ini sebagai project PHP, malah dianggep static Vite site. Paket ini udah lengkap semua file skeleton-nya, jadi tinggal `composer install` lokal sekali buat generate `vendor/` (dan `composer.lock`), lalu push.

## Kenapa Ada Error Kedua (Security Advisory)

Laravel 11 udah **EOL (habis masa dukungan keamanan) sejak 12 Maret 2026** — nggak dapet patch keamanan lagi selamanya. Composer versi baru (2.9+) otomatis nge-block instalasi package yang punya advisory aktif tanpa patch, makanya `laravel/framework: ^11.31` ke-reject total. Paket ini udah di-upgrade ke **Laravel 12** (masih aktif disupport sampai Feb 2027). `maatwebsite/excel` juga sementara dilepas (butuh `ext-gd` yang belum ke-setup, dan fiturnya belum diimplementasi) — detail di `composer-packages.md`.

## Catatan Penting

- **Nggak ada payment gateway/QRIS** — semua modul Dompet/Utang-Piutang/Tabungan itu pencatatan manual.
- **Auth dibikin custom** (bukan Breeze) — cukup login/register, tanpa email verification/password reset dulu (bisa nyusul di fase berikutnya kalau perlu).
- **WalletService** (`app/Services/WalletService.php`) jaga konsistensi saldo dompet, transfer, dan setoran tabungan.
- **Export PDF** (`barryvdh/laravel-dompdf`) dependency-nya udah ada, tinggal diimplementasi. **Export Excel** sementara dilepas — lihat `composer-packages.md` buat cara nambahnya lagi.
- Karena nggak bisa tes lokal, jalur testing lo adalah push → Railway build. Kalau ada error build lagi, **paste full log-nya** — kebanyakan error di tahap ini muncul di log build (bukan runtime), jadi gampang di-diagnosis dari situ.
