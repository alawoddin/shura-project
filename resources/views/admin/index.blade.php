@extends('admin.dashboard')
@section('admin')
    <div class="content">

        @php

            $incomes = \App\Models\Income::with('category')
                ->selectRaw('category_id, SUM(amount) as total')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $totalIncome = $incomes->sum('total');
            $monthIncome = \App\Models\Income::whereMonth('date', now()->month)

                ->whereYear('date', now()->year)

                ->sum('amount');

            $totalExpense = \App\Models\Expense::sum('amount');
            $monthExpense = \App\Models\Expense::whereMonth('date', now()->month)

                ->whereYear('date', now()->year)

                ->sum('amount');

            // receive payment data for charts

            // Total Receive

            $totalReceive = \App\Models\ReceivePayment::sum('amount');

            $totalcredit = \App\Models\Credit::sum('amount');
            $remaining_amount = \App\Models\Credit::sum('remaining_amount');

            // Current Month Receive

            $monthReceive = \App\Models\ReceivePayment::whereMonth('date', now()->month)

                ->whereYear('date', now()->year)

                ->sum('amount');

        @endphp

        {{-- user chart  --}}

        @php
            $totalUser = \App\Models\User::count();
            $monthUser = \App\Models\User::whereMonth('created_at', now()->month)

                ->whereYear('created_at', now()->year)

                ->count();
        @endphp



        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
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
                                        <div class="fs-14 mb-1">Total Users</div>
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
                                        <div class="fs-14 mb-1">Expense Total</div>
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
                                        <div class="fs-14 mb-1">Receive Payment</div>
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
                                        <div class="fs-14 mb-1">Monthly Recive Payment</div>
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




            <!-- Start Monthly Sales -->
            <div class="row">
                <div class="col-md-6 col-xl-6">
                    <div class="card overflow-hidden">

                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                    <i data-feather="table" class="widgets-icons"></i>
                                </div>
                                <h5 class="card-title mb-0">Info In Chart</h5>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-traffic mb-0">
                                    <tbody>

                                        <thead>
                                            <tr>

                                                <th>
                                                    Section
                                                </th>

                                                <th>
                                                    Total
                                                </th>

                                                <th>
                                                    Current Month
                                                </th>



                                            </tr>
                                        </thead>



                                        <tr>

                                            <td>

                                                Total Users

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

                                                Total Receive

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

                                                Total Credit

                                            </td>

                                            <td>

                                                {{ number_format($totalcredit) }}

                                            </td>

                                            <td>

                                                {{ number_format($remaining_amount) }}

                                            </td>



                                        </tr>




                                        <tr>

                                            <td>

                                                Total Expense

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

                                                Balance

                                            </td>

                                            <td>

                                                {{ number_format($totalIncome) }}

                                            </td>

                                            <td>

                                                {{ number_format($monthIncome) }}


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
                                <h5 class="card-title mb-0">Best Source</h5>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-traffic mb-0">
                                    <tbody>

                                        <thead>

                                            <tr>

                                                <th>

                                                    Income Type

                                                </th>

                                                <th colspan="2">

                                                    Amount

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
