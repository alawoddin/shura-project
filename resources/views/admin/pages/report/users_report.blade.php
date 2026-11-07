@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">گزارش کاربران</h4>
                    <small class="text-muted">یک عضو و یک دوره (روز، ماه، سال یا همه) را انتخاب کنید تا گزارش همان عضو نمایش داده شود</small>
                </div>
            </div>

            {{-- Choose a member and a period --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('users.report') }}" class="row g-2 align-items-end" id="usersReportForm">
                        <div class="col-md-4">
                            <label class="form-label">عضو</label>
                            <select name="user_id" class="form-control">
                                <option value="">انتخاب عضو</option>
                                @foreach ($members as $m)
                                    <option value="{{ $m->id }}" @selected($member && $member->id === $m->id)>
                                        {{ $m->name }}{{ $m->father_name ? ' — فرزند ' . $m->father_name : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">دوره</label>
                            <select name="period" id="reportPeriod" class="form-control">
                                <option value="all" @selected($period === 'all')>همه زمان‌ها</option>
                                <option value="day" @selected($period === 'day')>یک روز</option>
                                <option value="month" @selected($period === 'month')>یک ماه</option>
                                <option value="year" @selected($period === 'year')>یک سال</option>
                            </select>
                        </div>
                        <div class="col-md-2 period-input" data-period="day">
                            <label class="form-label">روز</label>
                            <input type="date" name="day" class="form-control" value="{{ $dayInput }}">
                        </div>
                        <div class="col-md-2 period-input" data-period="month">
                            <label class="form-label">ماه</label>
                            <input type="month" name="month" class="form-control" value="{{ $monthInput }}">
                        </div>
                        <div class="col-md-2 period-input" data-period="year">
                            <label class="form-label">سال</label>
                            <select name="year" class="form-control">
                                @for ($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" @selected((int) $yearInput === $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">نمایش گزارش</button>
                            @if ($member)
                                <button type="button" class="btn btn-light" onclick="window.print()">چاپ</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if (! $member)
                <div class="alert alert-info">برای دیدن گزارش، یک عضو را از لیست بالا انتخاب کنید.</div>
            @else
                {{-- Member info --}}
                <div class="card shadow-sm">
                    <div class="card-body d-flex flex-wrap justify-content-between gap-3">
                        <div>
                            <h5 class="mb-1">{{ $member->name }} {{ $member->lastname }}</h5>
                            <div class="text-muted small">
                                @if ($member->father_name) فرزند {{ $member->father_name }} · @endif
                                @if ($member->phone) تلفن: {{ $member->phone }} · @endif
                                تاریخ ثبت‌نام: {{ $member->register_date ?? $member->created_at?->format('Y-m-d') }}
                                · فیس ماهانه: {{ number_format($summary['monthly_fee'], 2) }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if ($blockReason)
                                <span class="badge bg-danger fs-6">مسدود</span>
                            @else
                                <span class="badge bg-success fs-6">عادی</span>
                            @endif
                            <a href="{{ route('users.fee.report', $member->id) }}" class="btn btn-sm btn-outline-primary">گزارش فیس ماه به ماه</a>
                            <a href="{{ route('users.details', $member->id) }}" class="btn btn-sm btn-outline-info">جزئیات عضو</a>
                        </div>
                    </div>
                    @if ($blockReason)
                        <div class="card-footer text-danger small">{{ $blockReason }}</div>
                    @endif
                </div>

                {{-- Totals for the chosen period --}}
                <h5 class="mb-2">گزارش {{ $member->name }} — {{ $periodLabel }}</h5>
                <div class="row">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">قرض گرفته</h6>
                                <h4 class="mb-0 text-danger">{{ number_format($summary['loans_taken'], 2) }}</h4>
                                <small class="text-muted">{{ $summary['loans_count'] }} قرض</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">قسط قرض پرداخت کرده</h6>
                                <h4 class="mb-0 text-success">{{ number_format($summary['loan_payments'], 2) }}</h4>
                                <small class="text-muted">{{ $summary['loan_payments_count'] }} قسط</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">فیس پرداخت کرده</h6>
                                <h4 class="mb-0 text-success">{{ number_format($summary['fees_paid'], 2) }}</h4>
                                <small class="text-muted">{{ $summary['fee_records'] }} پرداخت</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">کمک دریافت کرده</h6>
                                <h4 class="mb-0">{{ number_format($summary['aid_total'], 2) }}</h4>
                                <small class="text-muted">{{ $summary['aid_count'] }} بار</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current status (today) --}}
                <div class="alert alert-light border d-flex flex-wrap gap-4 small">
                    <strong>وضعیت فعلی (امروز):</strong>
                    <span>باقی‌مانده قرض: <span class="text-danger fw-semibold">{{ number_format($current['loans_remaining'], 2) }}</span> ({{ $current['active_loans'] }} قرض فعال)</span>
                    <span>بدهی فیس: <span class="text-danger fw-semibold">{{ number_format($current['fee_debt'], 2) }}</span> ({{ $current['unpaid_months'] }} ماه پرداخت‌نشده)</span>
                </div>

                {{-- Day-by-day / month-by-month breakdown --}}
                @if ($period !== 'day')
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $period === 'month' ? 'گزارش روز به روز' : 'گزارش ماه به ماه' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered text-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ $period === 'month' ? 'روز' : 'ماه' }}</th>
                                            <th>قرض گرفته</th>
                                            <th>قسط قرض پرداخت کرده</th>
                                            <th>فیس پرداخت کرده</th>
                                            <th>کمک دریافت کرده</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($breakdown as $row)
                                            <tr>
                                                <td>{{ $row['label'] }}</td>
                                                <td class="{{ $row['loans'] > 0 ? 'text-danger' : 'text-muted' }}">{{ number_format($row['loans'], 2) }}</td>
                                                <td class="{{ $row['loan_payments'] > 0 ? 'text-success' : 'text-muted' }}">{{ number_format($row['loan_payments'], 2) }}</td>
                                                <td class="{{ $row['fees'] != 0 ? 'text-success' : 'text-muted' }}">{{ number_format($row['fees'], 2) }}</td>
                                                <td class="{{ $row['aid'] > 0 ? '' : 'text-muted' }}">{{ number_format($row['aid'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-muted">در این دوره فعالیتی ثبت نشده است.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Loans taken in the period --}}
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="card-title mb-0">قرض‌های گرفته‌شده (کریدیت‌ها) — {{ $periodLabel }}</h5></div>
                    <div class="card-body">
                        @forelse ($loans as $loan)
                            <div class="border rounded p-2 mb-3">
                                <div class="d-flex flex-wrap justify-content-between gap-2">
                                    <div>
                                        <strong>قرض {{ number_format($loan->amount, 2) }}</strong>
                                        <span class="text-muted small">— تاریخ {{ $loan->date }} — از حساب {{ $loan->sourceAccount->name ?? '—' }}</span>
                                    </div>
                                    <div>
                                        @if ($loan->status === 'paid')
                                            <span class="badge bg-success">تسویه شده</span>
                                        @elseif ($loan->is_overdue)
                                            <span class="badge bg-danger">{{ $loan->months_without_payment }} ماه بدون پرداخت</span>
                                        @else
                                            <span class="badge bg-warning text-dark">فعال</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="small mt-1">
                                    پرداخت‌شده: <span class="text-success">{{ number_format($loan->paid_amount, 2) }}</span>
                                    · باقی‌مانده: <span class="text-danger">{{ number_format($loan->remaining_amount, 2) }}</span>
                                    @if ($loan->description) · {{ $loan->description }} @endif
                                </div>
                                @if ($loan->payments->count())
                                    <table class="table table-sm table-bordered text-center mt-2 mb-0">
                                        <thead>
                                            <tr><th>تاریخ قسط</th><th>مقدار</th><th>یادداشت</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($loan->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                                    <td>{{ $payment->note ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="small text-muted mt-1">هنوز قسطی پرداخت نشده است.</div>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">در این دوره قرضی نگرفته است.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Loan payments made in the period --}}
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="card-title mb-0">قسط‌های پرداخت‌شده قرض — {{ $periodLabel }}</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mb-0">
                                <thead>
                                    <tr><th>تاریخ</th><th>مقدار</th><th>برای قرض</th><th>یادداشت</th></tr>
                                </thead>
                                <tbody>
                                    @forelse ($loanPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                            <td class="text-success">{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if ($payment->credit)
                                                    قرض {{ number_format($payment->credit->amount, 2) }} ({{ $payment->credit->date }})
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $payment->note ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted">در این دوره قسطی پرداخت نکرده است.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Fees --}}
                    <div class="col-xl-6">
                        <div class="card shadow-sm">
                            <div class="card-header"><h5 class="card-title mb-0">پرداخت‌های فیس — {{ $periodLabel }}</h5></div>
                            <div class="card-body">
                                @if ($feeStatus['unpaid_months_count'] > 0)
                                    <div class="alert alert-danger small">
                                        ماه‌های پرداخت‌نشده (تا امروز):
                                        {{ collect($feeStatus['months'])->pluck('month')->implode('، ') }}
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered text-center mb-0">
                                        <thead>
                                            <tr><th>تاریخ</th><th>برای ماه</th><th>نوع</th><th>مقدار</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($feePayments as $payment)
                                                <tr>
                                                    <td>{{ $payment->date }}</td>
                                                    <td>{{ $payment->month_of ?? '—' }}</td>
                                                    <td>{{ $payment->category->name ?? '—' }}</td>
                                                    <td class="{{ $payment->transaction_type === 'debit' ? 'text-danger' : 'text-success' }}">
                                                        {{ number_format($payment->amount, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-muted">در این دوره پرداختی ثبت نشده است.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aid --}}
                    <div class="col-xl-6">
                        <div class="card shadow-sm">
                            <div class="card-header"><h5 class="card-title mb-0">کمک‌های دریافتی — {{ $periodLabel }}</h5></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered text-center mb-0">
                                        <thead>
                                            <tr><th>تاریخ</th><th>حساب</th><th>مقدار</th><th>توضیحات</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($aids as $aid)
                                                <tr>
                                                    <td>{{ $aid->date }}</td>
                                                    <td>{{ $aid->sourceAccount->name ?? '—' }}</td>
                                                    <td>{{ number_format($aid->amount, 2) }}</td>
                                                    <td>{{ $aid->description ?? '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-muted">در این دوره کمکی دریافت نکرده است.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const period = document.getElementById('reportPeriod');

            // Show only the date field that belongs to the chosen period; hidden fields are not sent.
            function sync() {
                document.querySelectorAll('#usersReportForm .period-input').forEach(function (box) {
                    const active = box.dataset.period === period.value;
                    box.style.display = active ? '' : 'none';
                    box.querySelectorAll('input, select').forEach(function (input) { input.disabled = !active; });
                });
            }

            period.addEventListener('change', sync);
            sync();
        });
    </script>
@endsection
