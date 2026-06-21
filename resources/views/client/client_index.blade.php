@extends('client.client_dashboard')
@section('client')
<div class="content">
    <div class="container-fluid">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">داشبورد عضو</h4>
                <p class="text-muted mb-0">فقط اطلاعات حساب شما نمایش داده می‌شود</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <img src="{{ $user->photo ? url('upload/user_images/' . $user->photo) : url('upload/no_image.jpg') }}"
                                class="rounded-circle" width="80" height="80" alt="photo">
                            <div>
                                <h5 class="mb-1">{{ $user->name }} {{ $user->lastname }}</h5>
                                <p class="text-muted mb-1">{{ $user->email }} | {{ $user->phone }}</p>
                                <span class="badge bg-primary">{{ $user->member_type ?? 'عضو' }}</span>
                                <span class="badge bg-success">{{ $user->status ?? 'active' }}</span>
                            </div>
                            <div class="ms-auto text-end">
                                <div class="text-muted">فیس ماهانه</div>
                                <h4 class="mb-0">{{ number_format($user->monthly_fee, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-xl-2">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted">مجموع پرداخت‌ها</div>
                        <h4 class="mb-0">{{ number_format($stats['total_payments'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted">قرض‌ها</div>
                        <h4 class="mb-0">{{ number_format($stats['total_credits'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted">باقی‌مانده قرض</div>
                        <h4 class="mb-0 text-danger">{{ number_format($stats['remaining_credits'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted">کمک دریافتی</div>
                        <h4 class="mb-0">{{ number_format($stats['total_aid'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted">بیلانس فعلی</div>
                        <h4 class="mb-0">{{ number_format($stats['current_balance'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted">اعضای فامیل</div>
                        <h4 class="mb-0">{{ $stats['family_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">پرداخت‌های من</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>تاریخ</th>
                                        <th>نوع</th>
                                        <th>ماه</th>
                                        <th>مقدار</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($user->receivePayments->sortByDesc('date') as $payment)
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
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">گزارش مالی من</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
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
                                    @forelse ($user->financialReports->sortByDesc('date') as $report)
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
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قرض‌های من</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
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
                                            <td>{{ $credit->status }}</td>
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
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">کمک‌های من</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
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
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">اعضای فامیل من</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
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
        </div>

    </div>
</div>
@endsection
