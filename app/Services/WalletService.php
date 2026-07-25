<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Wallet;
use App\Models\WalletMutation;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Catat mutasi masuk/keluar di satu wallet.
     *
     * @throws Exception kalau saldo bakal minus dan wallet nggak allow_negative
     */
    public function mutate(Wallet $wallet, string $type, float $amount, string $date, ?string $note, ?int $userId): WalletMutation
    {
        return DB::transaction(function () use ($wallet, $type, $amount, $date, $note, $userId) {
            $wallet->refresh();

            if (! $wallet->applyMutation($type, $amount)) {
                throw new Exception("Saldo dompet \"{$wallet->name}\" nggak boleh minus.");
            }

            $mutation = WalletMutation::create([
                'wallet_id' => $wallet->id,
                'user_id' => $userId,
                'type' => $type,
                'amount' => $amount,
                'date' => $date,
                'note' => $note,
            ]);

            ActivityLog::record(
                $wallet->household_id,
                $userId,
                'wallet_mutation',
                ucfirst($type)." Rp".number_format($amount, 0, ',', '.')." di dompet {$wallet->name}"
            );

            return $mutation;
        });
    }

    /**
     * Transfer antar dompet: bikin 2 entri mutasi (keluar dari asal, masuk ke tujuan).
     *
     * @throws Exception
     */
    public function transfer(Wallet $from, Wallet $to, float $amount, string $date, ?string $note, ?int $userId): void
    {
        DB::transaction(function () use ($from, $to, $amount, $date, $note, $userId) {
            $from->refresh();
            $to->refresh();

            if (! $from->applyMutation('out', $amount)) {
                throw new Exception("Saldo dompet \"{$from->name}\" nggak cukup / nggak boleh minus.");
            }

            $to->applyMutation('in', $amount);

            WalletMutation::create([
                'wallet_id' => $from->id,
                'target_wallet_id' => $to->id,
                'user_id' => $userId,
                'type' => 'transfer',
                'amount' => $amount,
                'date' => $date,
                'note' => $note,
            ]);

            ActivityLog::record(
                $from->household_id,
                $userId,
                'wallet_transfer',
                "Transfer Rp".number_format($amount, 0, ',', '.')." dari {$from->name} ke {$to->name}"
            );
        });
    }

    /**
     * Setor ke target tabungan. Kalau target punya wallet sumber, saldo wallet
     * otomatis kepotong (dicatat sebagai mutasi 'out') supaya nggak dobel-catat manual.
     *
     * @throws Exception
     */
    public function contributeToSavings(SavingsGoal $goal, float $amount, string $date, ?int $sourceWalletId, ?int $userId): SavingsContribution
    {
        return DB::transaction(function () use ($goal, $amount, $date, $sourceWalletId, $userId) {
            if ($sourceWalletId) {
                $wallet = Wallet::findOrFail($sourceWalletId);
                $this->mutate($wallet, 'out', $amount, $date, "Setor ke target tabungan: {$goal->name}", $userId);
            }

            $contribution = SavingsContribution::create([
                'savings_goal_id' => $goal->id,
                'wallet_id' => $sourceWalletId,
                'amount' => $amount,
                'date' => $date,
            ]);

            if ($goal->progress_percent >= 100 && $goal->status === 'active') {
                $goal->status = 'achieved';
                $goal->save();
            }

            ActivityLog::record(
                $goal->household_id,
                $userId,
                'savings_contribution',
                "Setor Rp".number_format($amount, 0, ',', '.')." ke target {$goal->name}"
            );

            return $contribution;
        });
    }
}
