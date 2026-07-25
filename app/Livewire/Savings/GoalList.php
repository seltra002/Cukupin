<?php

namespace App\Livewire\Savings;

use App\Models\SavingsGoal;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GoalList extends Component
{
    public $showForm = false;
    public $name, $target_amount, $deadline, $wallet_id;

    public $showContributeForm = false;
    public $contributingGoalId = null;
    public $contributeAmount, $contributeDate, $contributeWalletId;

    public function mount()
    {
        $this->contributeDate = now()->toDateString();
    }

    public function openForm()
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->reset(['name', 'target_amount', 'deadline', 'wallet_id']);
        $this->showForm = true;
    }

    public function save()
    {
        abort_unless(Auth::user()->canInput(), 403);
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'nullable|date',
            'wallet_id' => 'nullable|exists:wallets,id',
        ]);
        $data['household_id'] = Auth::user()->household_id;

        SavingsGoal::create($data);
        $this->showForm = false;
    }

    public function openContributeForm($goalId)
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->contributingGoalId = $goalId;
        $this->contributeAmount = null;
        $this->contributeDate = now()->toDateString();
        $this->contributeWalletId = null;
        $this->showContributeForm = true;
    }

    public function saveContribution(WalletService $service)
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->validate([
            'contributeAmount' => 'required|numeric|min:0.01',
            'contributeDate' => 'required|date',
        ]);

        $goal = SavingsGoal::findOrFail($this->contributingGoalId);

        try {
            $service->contributeToSavings($goal, (float) $this->contributeAmount, $this->contributeDate, $this->contributeWalletId, Auth::id());
        } catch (\Exception $e) {
            $this->addError('contributeAmount', $e->getMessage());
            return;
        }

        $this->showContributeForm = false;
    }

    public function render()
    {
        $goals = SavingsGoal::where('household_id', Auth::user()->household_id)->latest()->get();
        $wallets = Wallet::where('household_id', Auth::user()->household_id)->get();

        return view('livewire.savings.goal-list', compact('goals', 'wallets'));
    }
}
