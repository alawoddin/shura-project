@extends('client.client_dashboard')

@section('title', 'داشبورد عضو')

@section('client')
<div class="content">
    <div class="container-fluid shura-dash">
        <div class="shura-hero">
            <div class="hero-user">
                <img src="{{ $user->photo ? url('upload/user_images/' . $user->photo) : url('upload/no_image.jpg') }}" alt="photo">
                <div>
                    <p class="eyebrow">سیستم مدیریت شورا</p>
                    <h1>سلام، {{ $user->name }}</h1>
                    <p>امروز {{ now()->format('Y/m/d') }} است. وضعیت حساب، پرداخت‌ها و قرض‌های شما اینجاست.</p>
                </div>
            </div>
            <div class="hero-balance">
                <span>بیلانس فعلی</span>
                <strong>{{ number_format($stats['current_balance'], 2) }}</strong>
                <small>فیس ماهانه: {{ number_format($user->monthly_fee, 2) }}</small>
            </div>
        </div>

        <div class="quick-row">
            <a href="{{ route('user.profile') }}">پروفایل من</a>
            <a href="{{ route('user.change.password') }}">تغییر رمز</a>
            <a href="#payments">پرداخت‌ها</a>
            <a href="#credits">قرض‌ها</a>
            <a href="#family">اعضای فامیل</a>
        </div>

        <div class="metric-grid">
            <div class="metric green">
                <div class="label">مجموع پرداخت‌ها</div>
                <div class="value">{{ number_format($stats['total_payments'], 2) }}</div>
                <div class="hint">همه دریافت‌های شما</div>
            </div>
            <div class="metric gold">
                <div class="label">قرض‌ها</div>
                <div class="value">{{ number_format($stats['total_credits'], 2) }}</div>
                <div class="hint">مجموع قرض‌های ثبت شده</div>
            </div>
            <div class="metric red">
                <div class="label">باقی‌مانده قرض</div>
                <div class="value">{{ number_format($stats['remaining_credits'], 2) }}</div>
                <div class="hint">هنوز پرداخت نشده</div>
            </div>
            <div class="metric blue">
                <div class="label">کمک دریافتی</div>
                <div class="value">{{ number_format($stats['total_aid'], 2) }}</div>
                <div class="hint">مجموع کمک‌ها</div>
            </div>
            <div class="metric green">
                <div class="label">بیلانس فعلی</div>
                <div class="value">{{ number_format($stats['current_balance'], 2) }}</div>
                <div class="hint">آخرین گزارش مالی</div>
            </div>
            <div class="metric">
                <div class="label">فیس ماهانه</div>
                <div class="value">{{ number_format($user->monthly_fee, 2) }}</div>
                <div class="hint">مبلغ ثابت ماهانه</div>
            </div>
            <div class="metric blue">
                <div class="label">اعضای فامیل</div>
                <div class="value">{{ $stats['family_count'] }}</div>
                <div class="hint">ثبت شده در حساب شما</div>
            </div>
            <div class="metric gold">
                <div class="label">وضعیت عضو</div>
                <div class="value" style="font-size: 18px;">{{ $user->status ?? 'active' }}</div>
                <div class="hint">{{ $user->member_type ?? 'عضو' }}</div>
            </div>
        </div>

        <div class="split">
            <div class="panel" id="payments">
                <div class="panel-head">
                    <h5>پرداخت‌های من</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>نوع</th>
                                <th>ماه</th>
                                <th>مقدار</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->receivePayments->sortByDesc('date')->take(8) as $payment)
                                <tr>
                                    <td>{{ $payment->date }}</td>
                                    <td>{{ $payment->category->name ?? '-' }}</td>
                                    <td>{{ $payment->month_of ?? '-' }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">پرداختی ثبت نشده</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h5>گزارش مالی من</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>توضیحات</th>
                                <th>Dr</th>
                                <th>Cr</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->financialReports->sortByDesc('date')->take(8) as $report)
                                <tr>
                                    <td>{{ $report->date }}</td>
                                    <td>{{ $report->description ?? '-' }}</td>
                                    <td>{{ number_format($report->debit, 2) }}</td>
                                    <td>{{ number_format($report->credit, 2) }}</td>
                                    <td>{{ number_format($report->balance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">گزارش مالی ثبت نشده</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="split">
            <div class="panel" id="credits">
                <div class="panel-head">
                    <h5>قرض‌های من</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>مقدار</th>
                                <th>باقی‌مانده</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->credits->sortByDesc('date') as $credit)
                                <tr>
                                    <td>{{ $credit->date }}</td>
                                    <td>{{ number_format($credit->amount, 2) }}</td>
                                    <td>{{ number_format($credit->remaining_amount, 2) }}</td>
                                    <td>
                                        <span class="tag {{ in_array($credit->status, ['active', 'paid'], true) ? $credit->status : 'income' }}">
                                            {{ $credit->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">قرضی ثبت نشده</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h5>کمک‌های من</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>توضیحات</th>
                                <th>مقدار</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->aids->sortByDesc('date') as $aid)
                                <tr>
                                    <td>{{ $aid->date }}</td>
                                    <td>{{ $aid->description ?? '-' }}</td>
                                    <td>{{ number_format($aid->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">کمکی ثبت نشده</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel" id="family">
            <div class="panel-head">
                <h5>اعضای فامیل من</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>اسم</th>
                            <th>جنسیت</th>
                            <th>تاریخ تولد</th>
                            <th>سن</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user->familyMembers as $member)
                            <tr>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->gender }}</td>
                                <td>{{ $member->birth_date }}</td>
                                <td>{{ $member->age }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">عضو فامیل ثبت نشده</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
