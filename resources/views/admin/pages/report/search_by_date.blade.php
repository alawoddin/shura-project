@extends('admin.dashboard')

@section('admin')
    <div class="content">
        <div class="container-xxl">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">
                        گزارش روزانه ( {{ $date }} )
                    </h4>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="row">

                <div class="col-md-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-body text-center">
                            <h5>مجموع دریافتی</h5>
                            <h2>{{ number_format($totalReceive) }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-body text-center">
                            <h5>مجموع درآمد</h5>
                            <h2>{{ number_format($totalIncome) }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body text-center">
                            <h5>مجموع مصارف</h5>
                            <h2>{{ number_format($totalExpense) }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body text-center">
                            <h5>بلانس</h5>
                            <h2>{{ number_format($balance) }}</h2>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Expense Report --}}
            <div class="row mt-4">
                <div class="col-12">

                    <div class="card shadow-sm">

                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                گزارش روزانه مصارف
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>نام مصرف</th>
                                            <th>مبلغ</th>
                                            <th>تاریخ</th>
                                            <th>یادداشت</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($expenses as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->expense_name }}</td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->note }}</td>
                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    هیچ مصرفی یافت نشد
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- Receive Report --}}
            <div class="row mt-4">

                <div class="col-12">

                    <div class="card shadow-sm">

                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                گزارش روزانه دریافتی
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>نام عضو</th>
                                            <th>دسته‌بندی</th>
                                            <th>مبلغ</th>
                                            <th>تاریخ</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($receives as $key => $item)
                                            <tr>

                                                <td>{{ $key + 1 }}</td>

                                                <td>{{ $item->users->name ?? '' }}</td>

                                                <td>{{ $item->category->name ?? '' }}</td>

                                                <td>{{ $item->amount }}</td>

                                                <td>{{ $item->date }}</td>


                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    هیچ دریافتی یافت نشد
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Income Report --}}
            <div class="row mt-4">

                <div class="col-12">

                    <div class="card shadow-sm">

                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                گزارش روزانه درآمد
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>نام طلبکار</th>
                                            <th>مبلغ</th>
                                            <th>تاریخ</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($incomes as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->creditor_name ?? '' }}</td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->date }}</td>
                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    هیچ درآمدی یافت نشد
                                                </td>
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