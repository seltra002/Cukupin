<?php

use App\Http\Controllers\TelegramWebhookController;
use App\Livewire\Dashboard;
use App\Livewire\Debts\DebtList;
use App\Livewire\Items\ItemList;
use App\Livewire\Savings\GoalList;
use App\Livewire\Users\Manage as UserManage;
use App\Livewire\Wallets\WalletList;
use Illuminate\Support\Facades\Route;

// Webhook Telegram — publik, jangan pakai middleware auth
Route::post('/telegram/webhook', TelegramWebhookController::class);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/kebutuhan', ItemList::class)->name('items.index');
    Route::get('/dompet', WalletList::class)->name('wallets.index');
    Route::get('/utang-piutang', DebtList::class)->name('debts.index');
    Route::get('/tabungan', GoalList::class)->name('savings.index');

    Route::middleware('owner')->group(function () {
        Route::get('/pengguna', UserManage::class)->name('users.index');
    });

    Route::get('/profile', function () {
        return view('profile.telegram-link');
    })->name('profile.edit');
});

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

require __DIR__.'/auth.php';
