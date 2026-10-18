@extends('admin.dashboard')

@section('admin')
<div class="container-fluid">
    <h4>Reports: {{ $category->name }}</h4>

    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>From Account</th>
                    <th>To User</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $reports = collect();

                    // عایدات
                    foreach ($incomes as $income) {
                        $transfer = $income->undeposited;

                        if ($transfer && $transfer->status === 'transferred') {
                            continue;
                        }

                        if ((int) $income->category_id === (int) $category->id) {
                            $reports->push([
                                'type' => 'Income',
                                'from' => '-',
                                'to' => $category->name,
                                'amount' => abs((float) $income->amount),
                                'date' => $income->date,
                                'direction' => 'in',
                            ]);
                        }
                    }

                    // انتقال میان حساب‌ها
                    foreach ($undepositedTransfers as $transfer) {
                        if (!$transfer->income || $transfer->status !== 'transferred') {
                            continue;
                        }

                        $sourceId = (int) $transfer->income->category_id;
                        $targetId = (int) $transfer->target_account_id;
                        $amount = abs((float) $transfer->income->amount);

                        if ($sourceId === (int) $category->id) {
                            $reports->push([
                                'type' => 'Transfer Sent',
                                'from' => $transfer->income->category->name ?? $category->name,
                                'to' => $transfer->targetAccount->name ?? 'Unknown',
                                'amount' => $amount,
                                'date' => $transfer->income->date,
                                'direction' => 'out',
                            ]);
                        }

                        if ($targetId === (int) $category->id) {
                            $reports->push([
                                'type' => 'Transfer Received',
                                'from' => $transfer->income->category->name ?? 'Unknown',
                                'to' => $transfer->targetAccount->name ?? $category->name,
                                'amount' => $amount,
                                'date' => $transfer->income->date,
                                'direction' => 'in',
                            ]);
                        }
                    }

                    // هزینه‌ها
                    foreach ($expenses as $expense) {
                        if ((int) $expense->source_account_id !== (int) $category->id) {
                            continue;
                        }

                        $reports->push([
                            'type' => 'Expense',
                            'from' => $expense->sourceAccount->name ?? $category->name,
                            'to' => $expense->category->name ?? 'Unknown',
                            'amount' => abs((float) $expense->amount),
                            'date' => $expense->date,
                            'direction' => 'out',
                        ]);
                    }

                    // قرض‌ها و بازپرداخت قرض
                    foreach ($credits as $credit) {
                        if ((int) $credit->source_account_id !== (int) $category->id) {
                            continue;
                        }

                        $reports->push([
                            'type' => 'Credit',
                            'from' => $credit->sourceAccount->name ?? $category->name,
                            'to' => $credit->user->name ?? 'Unknown User',
                            'amount' => abs((float) $credit->amount),
                            'date' => $credit->date,
                            'direction' => 'out',
                        ]);

                        foreach ($credit->payments as $payment) {
                            $reports->push([
                                'type' => 'Credit Repayment',
                                'from' => $credit->user->name ?? 'Unknown User',
                                'to' => $credit->sourceAccount->name ?? $category->name,
                                'amount' => abs((float) $payment->amount),
                                'date' => $payment->paid_at ?? $payment->created_at,
                                'direction' => 'in',
                            ]);
                        }
                    }

                    // پرداخت‌های دریافتی
                    // foreach ($receivePayments as $payment) {
                    //     $reports->push([
                    //         'type' => $payment->transaction_type === 'credit'
                    //             ? 'Payment Received'
                    //             : 'Payment Debit',
                    //         'from' => $payment->users->name ?? 'Unknown User',
                    //         'to' => $payment->category->name ?? $category->name,
                    //         'amount' => abs((float) $payment->amount),
                    //         'date' => $payment->date,
                    //         'direction' => $payment->transaction_type === 'credit'
                    //             ? 'in'
                    //             : 'out',
                    //     ]);
                    // }

                    // کمک‌های مالی
                    foreach ($aids as $aid) {
                        if ((int) $aid->source_account_id !== (int) $category->id) {
                            continue;
                        }

                        $reports->push([
                            'type' => 'Aid',
                            'from' => $aid->sourceAccount->name ?? $category->name,
                            'to' => $aid->user->name ?? 'Unknown User',
                            'amount' => abs((float) $aid->amount),
                            'date' => $aid->date,
                            'direction' => 'out',
                        ]);
                    }

                    $reports = $reports->sortByDesc('date');
                @endphp

                @forelse($reports as $report)
                    <tr>
                        <td>
                            @if($report['type'] === 'Transfer Received')
                                <span class="badge bg-success">Received</span>
                            @elseif($report['type'] === 'Transfer Sent')
                                <span class="badge bg-danger">Sent</span>
                            @elseif($report['type'] === 'Income')
                                <span class="badge bg-primary">Income</span>
                            @elseif($report['type'] === 'Aid')
                                <span class="badge bg-warning text-dark">Aid</span>
                            @elseif($report['type'] === 'Credit')
                                <span class="badge bg-info text-dark">Credit</span>
                            @elseif($report['type'] === 'Credit Repayment')
                                <span class="badge bg-success">Credit Repayment</span>
                            @elseif($report['type'] === 'Payment Received')
                                <span class="badge bg-success">Payment Received</span>
                            @elseif($report['type'] === 'Payment Debit')
                                <span class="badge bg-danger">Payment Debit</span>
                            @else
                                <span class="badge bg-secondary">Expense</span>
                            @endif
                        </td>

                        <td>{{ $report['from'] }}</td>
                        <td>{{ $report['to'] }}</td>

                        <td class="fw-bold {{ $report['direction'] === 'out' ? 'text-danger' : 'text-success' }}">
                            {{ $report['direction'] === 'out' ? '-' : '+' }}{{ number_format($report['amount'], 2) }}
                        </td>

                        <td>
                            {{ $report['date'] ? \Carbon\Carbon::parse($report['date'])->format('Y-m-d') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection