<?php

namespace App\Livewire\Items;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ItemList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    public $showForm = false;
    public $editingId = null;
    public $name, $category_id, $qty = 1, $unit, $price = 0, $stock_status = 'aman', $note, $date;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock_status' => 'required|in:aman,menipis,habis',
            'note' => 'nullable|string',
            'date' => 'required|date',
        ];
    }

    public function mount()
    {
        $this->date = now()->toDateString();
    }

    public function openForm($id = null)
    {
        abort_unless(Auth::user()->canInput(), 403);

        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $item = Item::where('household_id', Auth::user()->household_id)->findOrFail($id);
            $this->name = $item->name;
            $this->category_id = $item->category_id;
            $this->qty = $item->qty;
            $this->unit = $item->unit;
            $this->price = $item->price;
            $this->stock_status = $item->stock_status;
            $this->note = $item->note;
            $this->date = $item->date->toDateString();
        } else {
            $this->reset(['name', 'category_id', 'unit', 'note']);
            $this->qty = 1;
            $this->price = 0;
            $this->stock_status = 'aman';
            $this->date = now()->toDateString();
        }

        $this->showForm = true;
    }

    public function save()
    {
        abort_unless(Auth::user()->canInput(), 403);
        $data = $this->validate();
        $data['household_id'] = Auth::user()->household_id;
        $data['user_id'] = Auth::id();

        if ($this->editingId) {
            $item = Item::findOrFail($this->editingId);
            $item->update($data);
            ActivityLog::record($data['household_id'], Auth::id(), 'item_update', "Update catatan: {$data['name']}");
        } else {
            Item::create($data);
            ActivityLog::record($data['household_id'], Auth::id(), 'item_create', "Nambah catatan: {$data['name']}");
        }

        $this->showForm = false;
        $this->resetPage();
    }

    public function delete($id)
    {
        abort_unless(Auth::user()->canInput(), 403);
        $item = Item::where('household_id', Auth::user()->household_id)->findOrFail($id);
        $item->delete();
        ActivityLog::record(Auth::user()->household_id, Auth::id(), 'item_delete', "Hapus catatan: {$item->name}");
    }

    public function render()
    {
        $items = Item::where('household_id', Auth::user()->household_id)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterCategory, fn ($q) => $q->where('category_id', $this->filterCategory))
            ->when($this->filterStatus, fn ($q) => $q->where('stock_status', $this->filterStatus))
            ->latest('date')
            ->paginate(10);

        $categories = Category::where('household_id', Auth::user()->household_id)->get();

        return view('livewire.items.item-list', compact('items', 'categories'));
    }
}
