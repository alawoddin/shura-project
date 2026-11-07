@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">گزارش کاربران</h4>
                    <small class="text-muted">یک عضو را انتخاب کنید تا گزارش کامل آن نمایش داده شود</small>
                </div>
            </div>

            {{-- Choose a member --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('users.report') }}" class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">عضو</label>
                            <select name="user_id" class="form-control" onchange="this.form.submit()">
                                <option value="">انتخاب عضو</option>
                                @foreach ($members as $m)
                                    <option value="{{ $m->id }}" @selected($member && $member->id === $m->id)>
                                        {{ $m->name }}{{ $m->father_name ? ' — فرزند ' . $m->father_name : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
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

                {{-- Summary --}}
                <div class="row">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">مجموع قرض گرفته</h6>
                                <h4 class="mb-0">{{ number_format($summary['loans_total'], 2) }}</h4>
                                <small class="text-muted">{{ $summary['loans_count'] }} قرض</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">قرض پرداخت‌شده / باقی‌مانده</h6>
                                <h4 class="mb-0">
                                    <span class="text-success">{{ number_format($summary['loans_paid'], 2) }}</span>
                                    /
                                    <span class="text-danger">{{ number_format($summary['loans_remaining'], 2) }}</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">فیس پرداخت‌شده / بدهی فیس</h6>
                                <h4 class="mb-0">
                                    <span class="text-success">{{ number_format($summary['fees_paid'], 2) }}</span>
                                    /
                                    <span class="text-danger">{{ number_format($summary['fee_debt'], 2) }}</span>
                                </h4>
                                <small class="text-muted">{{ $summary['unpaid_months'] }} ماه پرداخت‌نشده</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="text-muted">کمک‌های دریافتی</h6>
                                <h4 class="mb-0">{{ number_format($summary['aid_total'], 2) }}</h4>
                                <small class="text-muted">{{ $summary['aid_count'] }} بار</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loans --}}
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="card-title mb-0">قرض‌ها (کریدیت‌ها)</h5></div>
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
                            <p class="text-muted mb-0">این عضو قرضی نگرفته است.</p>
                        @endforelse
                    </div>
                </div>

                <div class="row">
                    {{-- Fees --}}
                    <div class="col-xl-6">
                        <div class="card shadow-sm">
                            <div class="card-header"><h5 class="card-title mb-0">پرداخت‌های فیس</h5></div>
                            <div class="card-body">
                                @if ($feeStatus['unpaid_months_count'] > 0)
                                    <div class="alert alert-danger small">
                                        ماه‌های پرداخت‌نشده:
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
                                                <tr><td colspan="4" class="text-muted">پرداختی ثبت نشده است.</td></tr>
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
                            <div class="card-header"><h5 class="card-title mb-0">کمک‌های دریافتی</h5></div>
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
                                                <tr><td colspan="4" class="text-muted">کمکی دریافت نکرده است.</td></tr>
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
@endsection
