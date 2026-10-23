@extends('admin.dashboard')

@section('title', 'داشبورد')

@section('admin')
    <div class="content">
        <div class="container-fluid shura-dash">
            <div class="shura-hero">
                <div>
                    <p class="eyebrow">سیستم مدیریت شورا</p>
                    <h1>سلام، {{ Auth::user()->name }}</h1>
                    <p>امروز {{ now()->format('Y/m/d') }} است. وضعیت مالی، اعضا و حساب‌ها اینجاست.</p>
                </div>
                <div class="hero-balance">
                    <span>مبلغ فعلی</span>
                    <strong>{{ number_format($currentBalance, 2) }}</strong>
                    <small>ماه جاری: {{ number_format($monthCurrentBalance, 2) }}</small>
                </div>
            </div>

            <div class="quick-row">
                @if (Auth::user()->can('all.users'))
                    <a href="{{ route('all.users') }}">کاربران</a>
                @endif
                @if (Auth::user()->can('all.income'))
                    <a href="{{ route('all.income') }}">درامدها</a>
                @endif
                @if (Auth::user()->can('all.undeposited'))
                    <a href="{{ route('undeposited.income') }}">واریز نشده</a>
                @endif
                @if (Auth::user()->can('all.expense'))
                    <a href="{{ route('all.expense') }}">مصارف</a>
                @endif
                @if (Auth::user()->can('all.aid'))
                    <a href="{{ route('all.aid') }}">کمک‌ها</a>
                @endif
                @if (Auth::user()->can('all.credits'))
                    <a href="{{ route('all.credits') }}">قرض‌ها</a>
                @endif
                <a href="{{ route('admin.backup') }}">پشتیبان‌گیری</a>
            </div>

            <div class="metric-grid">
                <div class="metric green">
                    <div class="label">مجموع درآمد</div>
                    <div class="value">{{ number_format($totalIncomeAll, 2) }}</div>
                    <div class="hint">واریز شده: {{ number_format($totalIncome, 2) }}</div>
                </div>
                <div class="metric red">
                    <div class="label">مجموع مصارف</div>
                    <div class="value">{{ number_format($totalExpense, 2) }}</div>
                    <div class="hint">ماه جاری: {{ number_format($monthExpense, 2) }}</div>
                </div>
                <div class="metric blue">
                    <div class="label">بستانکار</div>
                    <div class="value">{{ number_format($totalCredit, 2) }}</div>
                    <div class="hint">دریافتی: {{ number_format($totalReceiveCredit, 2) }}</div>
                </div>
                <div class="metric gold">
                    <div class="label">بدهکار</div>
                    <div class="value">{{ number_format($totalDebit, 2) }}</div>
                    <div class="hint">دریافتی: {{ number_format($totalReceiveDebit, 2) }}</div>
                </div>
                <div class="metric">
                    <div class="label">مبلغ دریافت شده</div>
                    <div class="value">{{ number_format($totalReceive, 2) }}</div>
                    <div class="hint">ماه جاری: {{ number_format($monthReceive, 2) }}</div>
                </div>
                <div class="metric">
                    <div class="label">مجموعه بلانس</div>
                    <div class="value">{{ number_format($totalBalance, 2) }}</div>
                    <div class="hint">درآمد واریز شده + دریافتی</div>
                </div>
                <div class="metric green">
                    <div class="label">کمک‌ها</div>
                    <div class="value">{{ number_format($totalAids, 2) }}</div>
                    <div class="hint">ماه جاری: {{ number_format($monthAids, 2) }}</div>
                </div>
                <div class="metric blue">
                    <div class="label">کاربران</div>
                    <div class="value">{{ $totalUser }}</div>
                    <div class="hint">ماه جاری: {{ $monthUser }}</div>
                </div>
            </div>

            <div class="split">
                <div class="panel">
                    <div class="panel-head">
                        <h5>خلاصه مالی</h5>
                        <span class="hint">کل و ماه جاری</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>بخش</th>
                                    <th>مجموع</th>
                                    <th>ماه جاری</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>مجموع درآمد</td>
                                    <td>{{ number_format($totalIncomeAll, 2) }}</td>
                                    <td>{{ number_format($monthIncomeAll, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>درآمد واریز شده</td>
                                    <td>{{ number_format($totalIncome, 2) }}</td>
                                    <td>{{ number_format($monthIncome, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>مجموع دریافتی</td>
                                    <td>{{ number_format($totalReceive, 2) }}</td>
                                    <td>{{ number_format($monthReceive, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>بستانکار</td>
                                    <td>{{ number_format($totalCredit, 2) }}</td>
                                    <td>{{ number_format($monthCredit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>بدهکار</td>
                                    <td>{{ number_format($totalDebit, 2) }}</td>
                                    <td>{{ number_format($monthDebit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>مصارف</td>
                                    <td>{{ number_format($totalExpense, 2) }}</td>
                                    <td>{{ number_format($monthExpense, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>کمک‌ها</td>
                                    <td>{{ number_format($totalAids, 2) }}</td>
                                    <td>{{ number_format($monthAids, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>اعتبار موجود / باقی قرض</td>
                                    <td>{{ number_format($totalcredit, 2) }}</td>
                                    <td>{{ number_format($remaining_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>بلانس فعلی</td>
                                    <td>{{ number_format($currentBalance, 2) }}</td>
                                    <td>{{ number_format($monthCurrentBalance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <h5>بهترین منابع درآمد</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>نوع درآمد</th>
                                    <th>مبلغ</th>
                                    <th>سهم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incomes as $item)
                                    @php
                                        $percent = $totalIncomeAll > 0 ? ($item->total / $totalIncomeAll) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->category->name ?? '—' }}</td>
                                        <td>{{ number_format($item->total, 2) }}</td>
                                        <td style="min-width: 140px;">
                                            <div class="bar"><span style="width: {{ min($percent, 100) }}%"></span></div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">درآمدی ثبت نشده است</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h5>موجودی حساب‌ها</h5>
                    <span>درآمد منهای مصارف، کمک‌ها و قرض‌ها</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>حساب</th>
                                <th>نوع</th>
                                <th>درآمد واریزی</th>
                                <th>مصارف</th>
                                <th>کمک‌ها</th>
                                <th>قرض‌ها</th>
                                <th>مجموع خروجی</th>
                                <th>موجودی فعلی</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accountSummaries as $account)
                                <tr>
                                    <td>{{ $account['name'] }}</td>
                                    <td>
                                        <span class="tag {{ $account['account_type'] === 'cash' ? 'cash' : 'income' }}">
                                            {{ $account['account_type'] === 'cash' ? 'نقدی' : 'درآمد' }}
                                        </span>
                                    </td>
                                    <td class="text-success">{{ number_format($account['money_in'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($account['expense_out'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($account['aid_out'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($account['loan_out'], 2) }}</td>
                                    <td class="fw-semibold">{{ number_format($account['money_out'], 2) }}</td>
                                    <td class="fw-semibold {{ $account['remaining'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($account['remaining'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">حسابی یافت نشد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
<<<<<<< HEAD

            <!-- Stats tables -->
            <div class="row">
                <div class="col-md-6 col-xl-6">
                    <div class="card overflow-hidden">

                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                    <i data-feather="table" class="widgets-icons"></i>
                                </div>
                                <h5 class="card-title mb-0">اطلاعات آماری</h5>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-traffic mb-0">
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th>
                                                    بخش
                                                </th>
                                                <th>
                                                    مجموع
                                                </th>
                                                <th>
                                                    ماه جاری
                                                </th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>مجموع درآمد</td>
                                            <td>{{ number_format($totalIncomeAll, 2) }}</td>
                                            <td>{{ number_format($monthIncomeAll, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>درآمد واریز شده</td>
                                            <td>{{ number_format($totalIncome, 2) }}</td>
                                            <td>{{ number_format($monthIncome, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>دریافتی بستانکار</td>
                                            <td>{{ number_format($totalReceiveCredit, 2) }}</td>
                                            <td>{{ number_format($monthReceiveCredit, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>دریافتی بدهکار</td>
                                            <td>{{ number_format($totalReceiveDebit, 2) }}</td>
                                            <td>{{ number_format($monthReceiveDebit, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>دریافتی خالص</td>
                                            <td>{{ number_format($totalReceive, 2) }}</td>
                                            <td>{{ number_format($monthReceive, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>بستانکار (Credit)</td>
                                            <td>{{ number_format($totalCredit, 2) }}</td>
                                            <td>{{ number_format($monthCredit, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>بدهکار (Debit)</td>
                                            <td>{{ number_format($totalDebit, 2) }}</td>
                                            <td>{{ number_format($monthDebit, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>مجموع مصارف</td>
                                            <td>{{ number_format($totalExpense, 2) }}</td>
                                            <td>{{ number_format($monthExpense, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>مجموع کمک‌ها</td>
                                            <td>{{ number_format($totalAids, 2) }}</td>
                                            <td>{{ number_format($monthAids, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>اعتبار موجود</td>
                                            <td>{{ number_format($totalcredit, 2) }}</td>
                                            <td>{{ number_format($monthCreditAvailable, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>مانده قرض‌ها</td>
                                            <td>{{ number_format($remaining_amount, 2) }}</td>
                                            <td>{{ number_format($monthLoansRemaining, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>بلانس فعلی</td>
                                            <td>{{ number_format($currentBalance, 2) }}</td>
                                            <td>{{ number_format($monthCurrentBalance, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-6 col-xl-6">
                    <div class="card overflow-hidden">

                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                    <i data-feather="tablet" class="widgets-icons"></i>
                                </div>
                                <h5 class="card-title mb-0">بهترین منابع درآمد</h5>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-traffic mb-0">
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th>
                                                    نوع درآمد
                                                </th>
                                                <th colspan="2">
                                                    مبلغ
                                                </th>
                                            </tr>
                                        </thead>
                                        @foreach ($incomes as $item)
                                            @php
                                                $percent = $totalIncomeAll > 0 ? ($item->total / $totalIncomeAll) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $item->category->name }}
                                                </td>
                                                <td>
                                                    {{ number_format($item->total) }}
                                                </td>
                                                <td class="w-50">
                                                    <div class="progress progress-md">
                                                        <div class="progress-bar bg-primary"
                                                            style="width:{{ $percent }}%">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Monthly Sales -->

        </div> <!-- container-fluid -->
    </div>  
@endsection
=======
        </div>
    </div>
@endsection
>>>>>>> 6c856495bc33b149b96ddf949c8af8ca03bdaa71
