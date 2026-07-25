<?php

namespace App\Livewire\Wallets;

use App\Models\ActivityLog;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WalletList extends Component
{
    public $showWalletForm = false;
    public $editingId = null;
    public $name, $opening_balance = 0, $is_cash_flow = true, $allow_negative = false, $note;

    public $showMutationForm = false;
    public $mutationWalletId = null;
    public $mutationType = 'in';
    public $mutationAmount = 0;
    public $mutationDate;
    public $mutationNote;
    public $mutationTargetWalletId = null;

    protected function walletRules()
    {
        return [
            'name' => 'required|string|max:255',
            'opening_balance' => 'required|numeric',
            'note' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->mutationDate = now()->toDateString();
    }

    public function openWalletForm($id = null)
    {
        abort_unless(Auth::user()->isOwner(), 403, 'Cuma Owner yang bisa kelola dompet.');
        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $w = Wallet::where('household_id', Auth::user()->household_id)->findOrFail($id);
            $this->name = $w->name;
            $this->opening_balance = $w->opening_balance;
            $this->is_cash_flow = $w->is_cash_flow;
            $this->allow_negative = $w->allow_negative;
            $this->note = $w->note;
        } else {
            $this->reset(['name', 'note']);
            $this->opening_balance = 0;
            $this->is_cash_flow = true;
            $this->allow_negative = false;
        }

        $this->showWalletForm = true;
    }

    public function saveWallet()
    {
        abort_unless(Auth::user()->isOwner(), 403);
        $data = $this->validate($this->walletRules());
        $data['household_id'] = Auth::user()->household_id;
        $data['is_cash_flow'] = $this->is_cash_flow;
        $data['allow_negative'] = $this->allow_negative;

        if ($this->editingId) {
            Wallet::findOrFail($this->editingId)->update($data);
        } else {
            $data['current_balance'] = $data['opening_balance'];
            Wallet::create($data);
        }

        ActivityLog::record($data['household_id'], Auth::id(), 'wallet_save', "Simpan dompet: {$data['name']}");
        $this->showWalletForm = false;
    }

    public function openMutationForm($walletId)
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->mutationWalletId = $walletId;
        $this->mutationType = 'in';
        $this->mutationAmount = 0;
        $this->mutationDate = now()->toDateString();
        $this->mutationNote = null;
        $this->mutationTargetWalletId = null;
        $this->showMutationForm = true;
    }

    public function saveMutation(WalletService $service)
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->validate([
            'mutationAmount' => 'required|numeric|min:0.01',
            'mutationDate' => 'required|date',
            'mutationType' => 'required|in:in,out,transfer',
        ]);

        $wallet = Wallet::findOrFail($this->mutationWalletId);

        try {
            if ($this->mutationType === 'transfer') {
                $this->validate(['mutationTargetWalletId' => 'required|exists:wallets,id']);
                $target = Wallet::findOrFail($this->mutationTargetWalletId);
                $service->transfer($wallet, $target, (float) $this->mutationAmount, $this->mutationDate, $this->mutationNote, Auth::id());
            } else {
                $service->mutate($wallet, $this->mutationType, (float) $this->mutationAmount, $this->mutationDate, $this->mutationNote, Auth::id());
            }
        } catch (\Exception $e) {
            $this->addError('mutationAmount', $e->getMessage());
            return;
        }

        $this->showMutationForm = false;
    }

    public function render()
    {
        $wallets = Wallet::where('household_id', Auth::user()->household_id)->get();

        return view('livewire.wallets.wallet-list', compact('wallets'));
    }
}
