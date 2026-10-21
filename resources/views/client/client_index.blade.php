@extends('client.client_dashboard')

@section('title', 'داشبورد عضو')

@section('client')
<div class="content">
    <div class="container-fluid client-dash">

        <div class="client-hero">
            <div class="client-welcome">
                <img src="{{ $user->photo ? url('upload/user_images/' . $user->photo) : url('upload/no_image.jpg') }}" alt="photo">
                <div>
                    <p class="eyebrow">پورتال عضو شورا</p>
                    <h1>سلام، {{ $user->name }}</h1>
                    <p>{{ $user->email }}@if($user->phone) | {{ $user->phone }}@endif</p>
                    <div class="badges">
                        <span>{{ $user->member_type ?? 'عضو' }}</span>
                        <span>{{ $user->status ?? 'active' }}</span>
                        <span>امروز {{ now()->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>

            <div class="client-fee">
                <span>فیس ماهانه</span>
                <strong>{{ number_format($user->monthly_fee, 2) }}</strong>
                <small>بیلانس فعلی: {{ number_format($stats['current_balance'], 2) }}</small>
            </div>
        </div>

        <div class="client-quick">
            <a href="{{ route('user.profile') }}">پروفایل من</a>
            <a href="{{ route('user.change.password') }}">تغییر رمز</a>
            <a href="#payments">پرداخت‌ها</a>
            <a href="#credits">قرض‌ها</a>
            <a href="#family">اعضای فامیل</a>
        </div>

        <div class="client-metrics">
            <div class="client-metric green">
                <div class="label">مجموع پرداخت‌ها</div>
                <div class="value">{{ number_format($stats['total_payments'], 2) }}</div>
            </div>
            <div class="client-metric gold">
                <div class="label">قرض‌ها</div>
                <div class="value">{{ number_format($stats['total_credits'], 2) }}</div>
            </div>
            <div class="client-metric red">
                <div class="label">باقی‌مانده قرض</div>
                <div class="value">{{ number_format($stats['remaining_credits'], 2) }}</div>
            </div>
            <div class="client-metric blue">
                <div class="label">کمک دریافتی</div>
                <div class="value">{{ number_format($stats['total_aid'], 2) }}</div>
            </div>
            <div class="client-metric green">
                <div class="label">بیلانس فعلی</div>
                <div class="value">{{ number_format($stats['current_balance'], 2) }}</div>
            </div>
            <div class="client-metric">
                <div class="label">اعضای فامیل</div>
                <div class="value">{{ $stats['family_count'] }}</div>
            </div>
        </div>

        <div class="client-split">
            <div class="client-panel" id="payments">
                <div class="client-panel-head">
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

            <div class="client-panel">
                <div class="client-panel-head">
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

        <div class="client-split">
            <div class="client-panel" id="credits">
                <div class="client-panel-head">
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
                                        <span class="client-status {{ in_array($credit->status, ['active', 'paid'], true) ? $credit->status : 'default' }}">
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

            <div class="client-panel">
                <div class="client-panel-head">
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

        <div class="client-panel" id="family">
            <div class="client-panel-head">
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
