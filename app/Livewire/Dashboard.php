<?php

namespace App\Livewire;

use App\Models\Debt;
use App\Models\Item;
use App\Models\SavingsGoal;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $household = Auth::user()->household;

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $spentThisMonth = Item::where('household_id', $household->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get()
            ->sum(fn ($item) => $item->qty * $item->price);

        $budget = (float) $household->monthly_budget;
        $budgetPercent = $budget > 0 ? min(100, round(($spentThisMonth / $budget) * 100, 1)) : 0;

        $lowStockItems = Item::where('household_id', $household->id)
            ->whereIn('stock_status', ['menipis', 'habis'])
            ->latest('date')
            ->take(5)
            ->get();

        $wallets = Wallet::where('household_id', $household->id)->get();
        $totalBalance = $wallets->where('is_cash_flow', true)->sum('current_balance');

        $unpaidDebts = Debt::where('household_id', $household->id)
            ->where('type', 'debt')->where('status', '!=', 'paid')->get();
        $unpaidReceivables = Debt::where('household_id', $household->id)
            ->where('type', 'receivable')->where('status', '!=', 'paid')->get();

        $activeSavings = SavingsGoal::where('household_id', $household->id)
            ->where('status', 'active')->latest()->take(3)->get();

        return view('livewire.dashboard', [
            'spentThisMonth' => $spentThisMonth,
            'budget' => $budget,
            'budgetPercent' => $budgetPercent,
            'lowStockItems' => $lowStockItems,
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
            'unpaidDebtsTotal' => $unpaidDebts->sum(fn ($d) => $d->remaining_amount),
            'unpaidReceivablesTotal' => $unpaidReceivables->sum(fn ($d) => $d->remaining_amount),
            'activeSavings' => $activeSavings,
        ]);
    }
}
