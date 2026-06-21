<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Accounts Receivable', 'description' => 'All pending fees and credit loans', 'account_type' => 'receivable', 'is_monthly_fee' => false],
            ['name' => 'Accounts Payable', 'description' => 'All shura liabilities', 'account_type' => 'payable', 'is_monthly_fee' => false],
            ['name' => 'Collected Fees', 'description' => 'Cash based account for member fees', 'account_type' => 'cash', 'is_monthly_fee' => false],
            ['name' => 'Charity Aids', 'description' => 'All charity aid funds', 'account_type' => 'cash', 'is_monthly_fee' => false],
            ['name' => 'Zakat', 'description' => 'All zakat payments', 'account_type' => 'cash', 'is_monthly_fee' => false],
            ['name' => 'Business Income', 'description' => 'All business income of shura', 'account_type' => 'cash', 'is_monthly_fee' => false],
            ['name' => 'Salary Exp', 'description' => 'Salary expenses', 'account_type' => 'expense', 'is_monthly_fee' => false],
            ['name' => 'Zakat Distribution Exp', 'description' => 'Zakat distribution expenses', 'account_type' => 'expense', 'is_monthly_fee' => false],
            ['name' => 'Admin Exp', 'description' => 'Administrative expenses', 'account_type' => 'expense', 'is_monthly_fee' => false],
            ['name' => 'Other Exp', 'description' => 'Other expenses', 'account_type' => 'expense', 'is_monthly_fee' => false],
            ['name' => 'Member Fee', 'description' => 'Monthly member fee payment', 'account_type' => 'payment_type', 'is_monthly_fee' => true],
            ['name' => 'Charity Aid', 'description' => 'Charity aid payment from member', 'account_type' => 'payment_type', 'is_monthly_fee' => false],
            ['name' => 'Zakat Payment', 'description' => 'Zakat payment from member', 'account_type' => 'payment_type', 'is_monthly_fee' => false],
            ['name' => 'Business Income Payment', 'description' => 'Business income payment from member', 'account_type' => 'payment_type', 'is_monthly_fee' => false],
            ['name' => 'Loans Received', 'description' => 'Loan repayment from member', 'account_type' => 'payment_type', 'is_monthly_fee' => false],
            ['name' => 'Member Fee Income', 'description' => 'External member fee income source', 'account_type' => 'income', 'is_monthly_fee' => false],
            ['name' => 'Charity Aids Income', 'description' => 'External charity aid income source', 'account_type' => 'income', 'is_monthly_fee' => false],
            ['name' => 'Zakat Income', 'description' => 'External zakat income source', 'account_type' => 'income', 'is_monthly_fee' => false],
            ['name' => 'Business Income Source', 'description' => 'External business income source', 'account_type' => 'income', 'is_monthly_fee' => false],
        ];

        foreach ($accounts as $account) {
            Category::updateOrCreate(
                ['slug' => strtolower(str_replace(' ', '-', $account['name']))],
                [
                    'name' => $account['name'],
                    'description' => $account['description'],
                    'account_type' => $account['account_type'],
                    'is_monthly_fee' => $account['is_monthly_fee'],
                ]
            );
        }
    }
}
