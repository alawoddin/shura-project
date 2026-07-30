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

            <!-- start row -->
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="row g-3">

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1">کاربران</div>
                                    </div>

                                    <div class="d-flex align-items-baseline mb-2">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{ $totalUser }}</div>
                                        <div class="me-auto">
                                            <span class="text-primary d-inline-flex align-items-center">
                                                <i data-feather="trending-up" class="ms-1"
                                                    style="height: 22px; width: 22px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="website-visitors" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1">مجموعه مصارفات</div>
                                    </div>

                                    <div class="d-flex align-items-baseline mb-2">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-black">
                                            {{ number_format($totalExpense, 2) }}</div>
                                        <div class="me-auto">
                                            <span class="text-danger d-inline-flex align-items-center">
                                                <i data-feather="trending-down" class="ms-1"
                                                    style="height: 22px; width: 22px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="conversion-visitors" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1">مبلغ دریافت شده</div>
                                    </div>

                                    <div class="d-flex align-items-baseline mb-2">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-black">
                                            {{ number_format($totalReceive, 2) }}</div>
                                        <div class="me-auto">
                                            <span class="text-success d-inline-flex align-items-center">
                                                <i data-feather="trending-up" class="ms-1"
                                                    style="height: 22px; width: 22px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="session-visitors" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1">فیس دریافتی ماهانه</div>
                                    </div>

                                    <div class="d-flex align-items-baseline mb-2">
                                        <div class="fs-22 mb-0 me-2 fw-semibold text-black">
                                            {{ number_format($monthReceive, 2) }}</div>
                                        <div class="me-auto">
                                            <span class="text-success d-inline-flex align-items-center">
                                                <i data-feather="trending-up" class="ms-1"
                                                    style="height: 22px; width: 22px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div id="active-users" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end sales -->
            </div> <!-- end row -->


             <div class="row mt-3">

        <!-- Total Balance -->
        <div class="col-md-6 col-xl-6">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-center">
                        <div class="fs-14 mb-1">
                            مجموعه بلانس
                        </div>
                    </div>

                    <div class="d-flex align-items-baseline mb-2">

                        <div class="fs-22 mb-0 me-2 fw-semibold text-primary">
                            {{ number_format($totalBalance,2) }}
                        </div>

                        <div class="me-auto">
                            <span class="text-primary">
                                <i data-feather="credit-card"></i>
                            </span>
                        </div>

                    </div>

                    <small class="text-muted">
                        درآمد + پول دریافتی
                    </small>

                </div>
            </div>
        </div>

        <!-- Current Balance -->
        <div class="col-md-6 col-xl-6">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-center">
                        <div class="fs-14 mb-1">
                            مبلغ فعلی
                        </div>
                    </div>

                    <div class="d-flex align-items-baseline mb-2">

                        <div class="fs-22 mb-0 me-2 fw-semibold text-success">
                            {{ number_format($currentBalance,2) }}
                        </div>

                        <div class="me-auto">
                            <span class="text-success">
                                <i data-feather="wallet"></i>
                            </span>
                        </div>

                    </div>

                    <small class="text-muted">
                        درآمد + دریافتی - مصارف - کمک ها
                    </small>

                </div>
            </div>
        </div>

    </div>


    

            <!-- Start Monthly Sales -->
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
                                            <td>
                                                مجموع کاربران
                                            </td>
                                            <td>
                                                {{ number_format($totalUser) }}
                                            </td>
                                            <td>
                                                {{ number_format($monthUser) }}

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                مجموع دریافتی
                                            </td>
                                            <td>
                                                {{ number_format($totalReceive) }}
                                            </td>
                                            <td>
                                                {{ number_format($monthReceive) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                مجموع اعتبار (موجودی)
                                            </td>
                                            <td>
                                                {{ number_format($totalcredit, 2) }}
                                            </td>
                                            <td>
                                                {{ number_format($remaining_amount, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                مجموع مصارف
                                            </td>
                                            <td>
                                                {{ number_format($totalExpense) }}
                                            </td>
                                            <td>
                                                {{ number_format($monthExpense) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                بلانس
                                            </td>
                                            <td>
                                                {{ number_format($currentBalance, 2) }}
                                            </td>
                                            <td>
                                                {{ number_format($monthCurrentBalance, 2) }}
                                            </td>
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
                                                $percent = $totalIncome > 0 ? ($item->total / $totalIncome) * 100 : 0;
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