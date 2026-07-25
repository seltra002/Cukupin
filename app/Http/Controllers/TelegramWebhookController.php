<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Debt;
use App\Models\Item;
use App\Models\TelegramLink;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramWebhookController extends Controller
{
    /**
     * Endpoint webhook Telegram. Daftarkan URL ini ke Telegram lewat:
     * https://api.telegram.org/bot<TOKEN>/setWebhook?url=<APP_URL>/telegram/webhook
     */
    public function handle(Request $request)
    {
        $message = $request->input('message');
        if (! $message) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) $message['chat']['id'];
        $text = trim($message['text'] ?? '');

        if (Str::startsWith($text, '/start')) {
            $this->reply($chatId, "Halo! Kirim kode verifikasi dari halaman Profil web Cukupin buat hubungin akun kamu.\n\nSetelah terhubung, kamu bisa pakai:\n/catat [nama] [jumlah] [harga]\n/stok\n/laporan\n/saldo\n/utang");
            return response()->json(['ok' => true]);
        }

        $link = TelegramLink::where('telegram_chat_id', $chatId)->first();

        // Belum terhubung: anggap teks yang masuk adalah kode verifikasi
        if (! $link) {
            $pending = TelegramLink::where('verification_code', $text)->whereNull('telegram_chat_id')->first();

            if ($pending) {
                $pending->update(['telegram_chat_id' => $chatId, 'verified_at' => now()]);
                $this->reply($chatId, "Akun kamu berhasil terhubung ✅ Sekarang kamu bisa pakai command bot.");
            } else {
                $this->reply($chatId, "Kode nggak ketemu. Ambil kode verifikasi dari halaman Profil di web Cukupin, terus kirim ke sini ya.");
            }

            return response()->json(['ok' => true]);
        }

        $user = $link->user;
        $householdId = $user->household_id;

        [$command, $args] = array_pad(explode(' ', $text, 2), 2, '');

        match (strtolower($command)) {
            '/catat' => $this->handleCatat($chatId, $householdId, $user->id, $args),
            '/stok' => $this->handleStok($chatId, $householdId),
            '/laporan' => $this->handleLaporan($chatId, $householdId),
            '/saldo' => $this->handleSaldo($chatId, $householdId),
            '/utang' => $this->handleUtang($chatId, $householdId),
            default => $this->reply($chatId, "Command nggak dikenali. Coba /catat, /stok, /laporan, /saldo, atau /utang."),
        };

        return response()->json(['ok' => true]);
    }

    protected function handleCatat(string $chatId, int $householdId, int $userId, string $args): void
    {
        // format: /catat Beras 2 25000
        $parts = preg_split('/\s+/', trim($args));

        if (count($parts) < 3) {
            $this->reply($chatId, "Format: /catat [nama] [jumlah] [harga]\nContoh: /catat Beras 2 25000");
            return;
        }

        $price = (float) array_pop($parts);
        $qty = (float) array_pop($parts);
        $name = implode(' ', $parts);

        Item::create([
            'household_id' => $householdId,
            'user_id' => $userId,
            'name' => $name,
            'qty' => $qty,
            'price' => $price,
            'stock_status' => 'aman',
            'date' => now()->toDateString(),
        ]);

        ActivityLog::record($householdId, $userId, 'item_create_bot', "Catat via Telegram: {$name}");

        $this->reply($chatId, "Tercatat ✅ {$name} x{$qty} — Rp".number_format($qty * $price, 0, ',', '.'));
    }

    protected function handleStok(string $chatId, int $householdId): void
    {
        $items = Item::where('household_id', $householdId)->whereIn('stock_status', ['menipis', 'habis'])->get();

        if ($items->isEmpty()) {
            $this->reply($chatId, "Stok aman semua 👌");
            return;
        }

        $lines = $items->map(fn ($i) => "- {$i->name} ({$i->stock_status})")->implode("\n");
        $this->reply($chatId, "Stok perlu diperhatikan:\n{$lines}");
    }

    protected function handleLaporan(string $chatId, int $householdId): void
    {
        $total = Item::where('household_id', $householdId)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get()->sum(fn ($i) => $i->qty * $i->price);

        $this->reply($chatId, "Total pengeluaran bulan ini: Rp".number_format($total, 0, ',', '.'));
    }

    protected function handleSaldo(string $chatId, int $householdId): void
    {
        $wallets = Wallet::where('household_id', $householdId)->get();
        $lines = $wallets->map(fn ($w) => "- {$w->name}: Rp".number_format($w->current_balance, 0, ',', '.'))->implode("\n");
        $this->reply($chatId, "Saldo dompet:\n{$lines}");
    }

    protected function handleUtang(string $chatId, int $householdId): void
    {
        $debts = Debt::where('household_id', $householdId)->where('type', 'debt')->where('status', '!=', 'paid')->get();
        $receivables = Debt::where('household_id', $householdId)->where('type', 'receivable')->where('status', '!=', 'paid')->get();

        $this->reply($chatId,
            "Utang belum lunas: Rp".number_format($debts->sum('remaining_amount'), 0, ',', '.')."\n".
            "Piutang belum lunas: Rp".number_format($receivables->sum('remaining_amount'), 0, ',', '.')
        );
    }

    protected function reply(string $chatId, string $text): void
    {
        Http::post('https://api.telegram.org/bot'.config('services.telegram.bot_token').'/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
}
