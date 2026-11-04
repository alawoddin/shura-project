@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">گزارشات سالانه ( {{ $year }} )</h4>
                </div>

                <form method="GET" action="{{ route('annual.report') }}" class="d-flex gap-2">
                    <select name="year" class="form-select">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">نمایش</button>
                    <button type="button" class="btn btn-light" onclick="window.print()">چاپ</button>
                </form>
            </div>

            {{-- Summary --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">مجموع ورودی</h6>
                            <h3 class="text-success mb-0">{{ number_format($totals['money_in'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">مجموع خروجی</h6>
                            <h3 class="text-danger mb-0">{{ number_format($totals['money_out'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">خالص سال</h6>
                            <h3 class="mb-0 {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($totals['net'], 2) }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">فیس اعضا</h6>
                            <h3 class="mb-0">{{ number_format($totals['fees'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Month by month --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">گزارش ماه به ماه</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center mb-0">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">ماه</th>
                                    <th colspan="5" class="table-success">ورودی</th>
                                    <th colspan="4" class="table-danger">خروجی</th>
                                    <th rowspan="2" class="align-middle">خالص</th>
                                </tr>
                                <tr>
                                    <th>فیس اعضا</th>
                                    <th>درآمد واریز شده</th>
                                    <th>دریافت پول</th>
                                    <th>قسط قرض</th>
                                    <th>مجموع</th>
                                    <th>مصارف</th>
                                    <th>کمک‌ها</th>
                                    <th>قرض داده شده</th>
                                    <th>مجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($months as $row)
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td>{{ number_format($row['fees'], 2) }}</td>
                                        <td>{{ number_format($row['incomes'], 2) }}</td>
                                        <td>{{ number_format($row['deposits'], 2) }}</td>
                                        <td>{{ number_format($row['loan_repayments'], 2) }}</td>
                                        <td class="fw-semibold text-success">{{ number_format($row['money_in'], 2) }}</td>
                                        <td>{{ number_format($row['expenses'], 2) }}</td>
                                        <td>{{ number_format($row['aids'], 2) }}</td>
                                        <td>{{ number_format($row['loans'], 2) }}</td>
                                        <td class="fw-semibold text-danger">{{ number_format($row['money_out'], 2) }}</td>
                                        <td class="fw-bold {{ $row['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($row['net'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td>مجموع سال</td>
                                    <td>{{ number_format($totals['fees'], 2) }}</td>
                                    <td>{{ number_format($totals['incomes'], 2) }}</td>
                                    <td>{{ number_format($totals['deposits'], 2) }}</td>
                                    <td>{{ number_format($totals['loan_repayments'], 2) }}</td>
                                    <td class="text-success">{{ number_format($totals['money_in'], 2) }}</td>
                                    <td>{{ number_format($totals['expenses'], 2) }}</td>
                                    <td>{{ number_format($totals['aids'], 2) }}</td>
                                    <td>{{ number_format($totals['loans'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($totals['money_out'], 2) }}</td>
                                    <td class="{{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($totals['net'], 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
