<?php

namespace App\Console\Commands;

use App\Services\LoanEnforcementService;
use Illuminate\Console\Command;

class SyncLoanStatuses extends Command
{
    protected $signature = 'loans:sync-statuses';

    protected $description = 'Suspend or reactivate users based on overdue loan payments';

    public function handle(LoanEnforcementService $loanEnforcement): int
    {
        $loanEnforcement->syncAllLoanStatuses();
        $this->info('Loan statuses synchronized successfully.');

        return self::SUCCESS;
    }
}
