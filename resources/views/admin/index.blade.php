@extends('admin.dashboard')
@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">داشبورد</h4>
                </div>
            </div>

            <!-- Summary cards -->
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="row g-3">

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مجموع درآمد</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-success">
                                            {{ number_format($totalIncomeAll, 2) }}
                                        </div>
                                        <span class="text-success"><i data-feather="trending-up"></i></span>
                                    </div>
                                    <small class="text-muted">واریز شده: {{ number_format($totalIncome, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مجموع مصارف</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-danger">
                                            {{ number_format($totalExpense, 2) }}
                                        </div>
                                        <span class="text-danger"><i data-feather="trending-down"></i></span>
                                    </div>
                                    <small class="text-muted">ماه جاری: {{ number_format($monthExpense, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مجموع بستانکار (Credit)</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-primary">
                                            {{ number_format($totalCredit, 2) }}
                                        </div>
                                        <span class="text-primary"><i data-feather="plus-circle"></i></span>
                                    </div>
                                    <small class="text-muted">دریافتی: {{ number_format($totalReceiveCredit, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مجموع بدهکار (Debit)</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-warning">
                                            {{ number_format($totalDebit, 2) }}
                                        </div>
                                        <span class="text-warning"><i data-feather="minus-circle"></i></span>
                                    </div>
                                    <small class="text-muted">دریافتی: {{ number_format($totalReceiveDebit, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مبلغ دریافت شده</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-black">
                                            {{ number_format($totalReceive, 2) }}
                                        </div>
                                    </div>
                                    <small class="text-muted">ماه جاری: {{ number_format($monthReceive, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مجموعه بلانس</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-primary">
                                            {{ number_format($totalBalance, 2) }}
                                        </div>
                                    </div>
                                    <small class="text-muted">درآمد واریز شده + دریافتی</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">مبلغ فعلی</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-success">
                                            {{ number_format($currentBalance, 2) }}
                                        </div>
                                    </div>
                                    <small class="text-muted">بلانس - مصارف - کمک‌ها</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="fs-14 mb-1">کاربران</div>
                                    <div class="d-flex align-items-baseline mb-1">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{ $totalUser }}</div>
                                    </div>
                                    <small class="text-muted">ماه جاری: {{ $monthUser }}</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Account balances: income minus expenses per source -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                    <i data-feather="pie-chart" class="widgets-icons"></i>
                                </div>
                                <h5 class="card-title mb-0">موجودی حساب‌ها (درآمد − مصارف)</h5>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-traffic mb-0">
                                    <thead>
                                        <tr>
                                            <th>حساب / منبع درآمد</th>
                                            <th>نوع</th>
                                            <th>درآمد واریزی</th>
                                            <th>مصارف (-)</th>
                                            <th>کمک‌ها (-)</th>
                                            <th>قرض‌ها (-)</th>
                                            <th>مجموع خروجی (-)</th>
                                            <th>موجودی فعلی</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($accountSummaries as $account)
                                            <tr>
                                                <td>{{ $account['name'] }}</td>
                                                <td>
                                                    <span class="badge {{ $account['account_type'] === 'cash' ? 'bg-primary' : 'bg-info' }}">
                                                        {{ $account['account_type'] === 'cash' ? 'نقدی' : 'درآمد' }}
                                                    </span>
                                                </td>
                                                <td class="text-success">{{ number_format($account['money_in'], 2) }}</td>
                                                <td class="text-danger">{{ number_format($account['expense_out'], 2) }}</td>
                                                <td class="text-danger">{{ number_format($account['aid_out'], 2) }}</td>
                                                <td class="text-danger">{{ number_format($account['loan_out'], 2) }}</td>
                                                <td class="text-danger fw-semibold">{{ number_format($account['money_out'], 2) }}</td>
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
                    </div>
                </div>
            </div>

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
                                            <td>مجموع دریافتی</td>
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