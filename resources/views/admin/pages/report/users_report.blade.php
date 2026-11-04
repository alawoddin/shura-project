@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">گزارش کاربران</h4>
                    <small class="text-muted">گزارش مالی هر عضو — جدا از گزارشات عمومی</small>
                </div>

                <form method="GET" action="{{ route('users.report') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="جستجوی نام عضو" value="{{ $search }}">
                    <button type="submit" class="btn btn-primary">جستجو</button>
                    @if ($search !== '')
                        <a href="{{ route('users.report') }}" class="btn btn-light">همه</a>
                    @endif
                </form>
            </div>

            {{-- Summary --}}
            <div class="row">
                <div class="col-md">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">فیس پرداخت‌شده</h6>
                            <h4 class="text-success mb-0">{{ number_format($totals['fees_paid'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">بدهی فیس</h6>
                            <h4 class="text-danger mb-0">{{ number_format($totals['fee_debt'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">باقی‌مانده قرض‌ها</h6>
                            <h4 class="text-danger mb-0">{{ number_format($totals['loan_remaining'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">کمک‌های دریافتی</h6>
                            <h4 class="mb-0">{{ number_format($totals['aid_received'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">مسدود (قرض بیش از ۶ ماه)</h6>
                            <h4 class="text-warning mb-0">{{ $totals['blocked'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عضو</th>
                                    <th>فیس ماهانه</th>
                                    <th>فیس پرداخت‌شده</th>
                                    <th>ماه‌های پرداخت‌نشده</th>
                                    <th>بدهی فیس</th>
                                    <th>مجموع قرض</th>
                                    <th>باقی‌مانده قرض</th>
                                    <th>کمک دریافتی</th>
                                    <th>وضعیت</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $key => $row)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $row['user']->name }}</td>
                                        <td>{{ number_format($row['monthly_fee'], 2) }}</td>
                                        <td class="text-success">{{ number_format($row['fees_paid'], 2) }}</td>
                                        <td>{{ $row['unpaid_months'] }}</td>
                                        <td class="{{ $row['fee_debt'] > 0 ? 'text-danger' : '' }}">{{ number_format($row['fee_debt'], 2) }}</td>
                                        <td>{{ number_format($row['loans_total'], 2) }}</td>
                                        <td class="{{ $row['loan_remaining'] > 0 ? 'text-danger' : '' }}">{{ number_format($row['loan_remaining'], 2) }}</td>
                                        <td>{{ number_format($row['aid_received'], 2) }}</td>
                                        <td>
                                            @if ($row['blocked'])
                                                <span class="badge bg-danger">مسدود — قرض بیش از ۶ ماه</span>
                                            @else
                                                <span class="badge bg-success">عادی</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.fee.report', $row['user']->id) }}" class="btn btn-sm btn-primary">گزارش فیس</a>
                                            <a href="{{ route('users.details', $row['user']->id) }}" class="btn btn-sm btn-info">جزئیات</a>
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
@endsection
