<?php

namespace App\Livewire\Debts;

use App\Models\ActivityLog;
use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DebtList extends Component
{
    public $tab = 'debt'; // debt | receivable

    public $showForm = false;
    public $type = 'debt';
    public $party_name, $amount, $date, $due_date, $note;

    public $showPaymentForm = false;
    public $payingDebtId = null;
    public $paymentAmount, $paymentDate, $paymentNote;

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->paymentDate = now()->toDateString();
    }

    public function openForm()
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->reset(['party_name', 'amount', 'due_date', 'note']);
        $this->type = $this->tab;
        $this->date = now()->toDateString();
        $this->showForm = true;
    }

    public function save()
    {
        abort_unless(Auth::user()->canInput(), 403);
        $data = $this->validate([
            'type' => 'required|in:debt,receivable',
            'party_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);
        $data['household_id'] = Auth::user()->household_id;

        Debt::create($data);
        ActivityLog::record($data['household_id'], Auth::id(), 'debt_create', ucfirst($data['type'])." baru: {$data['party_name']}");

        $this->showForm = false;
    }

    public function openPaymentForm($debtId)
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->payingDebtId = $debtId;
        $this->paymentAmount = null;
        $this->paymentDate = now()->toDateString();
        $this->paymentNote = null;
        $this->showPaymentForm = true;
    }

    public function savePayment()
    {
        abort_unless(Auth::user()->canInput(), 403);
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentDate' => 'required|date',
        ]);

        $debt = Debt::findOrFail($this->payingDebtId);

        DebtPayment::create([
            'debt_id' => $debt->id,
            'amount' => $this->paymentAmount,
            'date' => $this->paymentDate,
            'note' => $this->paymentNote,
        ]);

        $debt->refreshStatus();
        ActivityLog::record($debt->household_id, Auth::id(), 'debt_payment', "Bayar cicilan {$debt->party_name}: Rp".number_format($this->paymentAmount, 0, ',', '.'));

        $this->showPaymentForm = false;
    }

    public function render()
    {
        $debts = Debt::where('household_id', Auth::user()->household_id)
            ->where('type', $this->tab)
            ->latest('date')
            ->get();

        return view('livewire.debts.debt-list', compact('debts'));
    }
}
