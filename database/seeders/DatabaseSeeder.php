<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Debt;
use App\Models\Household;
use App\Models\Item;
use App\Models\SavingsGoal;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Budi (Owner)',
            'email' => 'owner@cukupin.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $household = Household::create([
            'name' => 'Keluarga Budi',
            'owner_id' => $owner->id,
            'monthly_budget' => 3000000,
        ]);

        $owner->update(['household_id' => $household->id]);

        $member = User::create([
            'name' => 'Sari (Anggota)',
            'email' => 'user@cukupin.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'permission' => 'can_input',
            'household_id' => $household->id,
        ]);

        $dapur = Category::create(['household_id' => $household->id, 'name' => 'Dapur']);
        $kebersihan = Category::create(['household_id' => $household->id, 'name' => 'Kebersihan']);

        Item::create(['household_id' => $household->id, 'category_id' => $dapur->id, 'user_id' => $owner->id, 'name' => 'Beras 5kg', 'qty' => 1, 'unit' => 'karung', 'price' => 72000, 'stock_status' => 'menipis', 'date' => now()]);
        Item::create(['household_id' => $household->id, 'category_id' => $dapur->id, 'user_id' => $member->id, 'name' => 'Minyak Goreng 2L', 'qty' => 1, 'unit' => 'botol', 'price' => 38000, 'stock_status' => 'aman', 'date' => now()]);
        Item::create(['household_id' => $household->id, 'category_id' => $kebersihan->id, 'user_id' => $owner->id, 'name' => 'Sabun Cuci Piring', 'qty' => 1, 'unit' => 'botol', 'price' => 15000, 'stock_status' => 'habis', 'date' => now()]);

        $cash = Wallet::create(['household_id' => $household->id, 'name' => 'Cash', 'opening_balance' => 500000, 'current_balance' => 500000, 'is_cash_flow' => true, 'allow_negative' => false]);
        Wallet::create(['household_id' => $household->id, 'name' => 'BCA', 'opening_balance' => 2000000, 'current_balance' => 2000000, 'is_cash_flow' => true, 'allow_negative' => false]);

        Debt::create(['household_id' => $household->id, 'type' => 'debt', 'party_name' => 'Koperasi RT', 'amount' => 500000, 'date' => now(), 'status' => 'unpaid']);
        Debt::create(['household_id' => $household->id, 'type' => 'receivable', 'party_name' => 'Tetangga (Pak Joko)', 'amount' => 200000, 'date' => now(), 'status' => 'unpaid']);

        SavingsGoal::create(['household_id' => $household->id, 'wallet_id' => $cash->id, 'name' => 'Dana Darurat', 'target_amount' => 5000000, 'status' => 'active']);
    }
}
