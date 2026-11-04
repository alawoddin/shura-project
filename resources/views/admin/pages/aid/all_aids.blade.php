@extends('admin.dashboard')
@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">کمک ها</h4>
                </div>

                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0 gap-2">
                        <a href="{{ route('all.account.transactions') }}" class="btn btn-outline-primary">انتقال بین حساب‌ها</a>
                        <a href="{{ route('add.aid') }}" class="btn btn-secondary">اضافه کردن</a>
                    </ol>
                </div>
            </div>

            <!-- Aid accounts -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">حساب‌های کمک‌ها</h5>
                            <a href="{{ route('add.category') }}" class="btn btn-sm btn-light">حساب جدید</a>
                        </div>
                        <div class="card-body">
                            @if ($aidAccounts->isEmpty())
                                <p class="text-muted mb-0">
                                    هنوز هیچ حسابی با نوع «حساب کمک‌ها» تعریف نشده است. از بخش حساب‌ها یک حساب کمک‌ها بسازید.
                                </p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">حساب</th>
                                                <th class="text-center">مجموع کمک‌های پرداخت‌شده</th>
                                                <th class="text-center">موجودی فعلی</th>
                                                <th class="text-center">عملیات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($aidAccounts as $account)
                                                <tr>
                                                    <td class="text-center">{{ $account->name }}</td>
                                                    <td class="text-center text-danger">{{ number_format($aidTotals[$account->id] ?? 0, 2) }}</td>
                                                    <td class="text-center fw-semibold {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($account->balance, 2) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('all.account.transactions', ['account' => $account->id]) }}" class="btn btn-sm btn-primary">انتقال</a>
                                                        <a href="{{ route('reports.category', $account->id) }}" class="btn btn-sm btn-light">گزارش</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($blockedUsers->count())
                <div class="alert alert-warning">
                    <strong>{{ $blockedUsers->count() }} عضو</strong> به دلیل قرض بیشتر از ۶ ماه نمی‌توانند کمک دریافت کنند:
                    <ul class="mb-0 mt-2">
                        @foreach ($blockedUsers as $bUser)
                            <li><strong>{{ $bUser->name }}</strong> — {{ $bUser->debt_block_reason }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Datatables  -->
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header">

                        </div><!-- end card header -->

                        <div class="card-body">

                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">آیدی</th>
                                            <th class="text-center">اسم</th>
                                            <th class="text-center">حساب</th>
                                            <th class="text-center">مقدار</th>
                                            <th class="text-center">تاریخ</th>
                                            <th class="text-center">نوت</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alldata as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">{{ $item->user->name ?? '—' }}</td>
                                                <td class="text-center">{{ $item->sourceAccount->name ?? '—' }}</td>
                                                <td class="text-center">{{ $item->amount }}</td>
                                                <td class="text-center">{{ $item->date }}</td>
                                                <td class="text-center">{{ $item->description }}</td>
                                                

                                                <td class="text-center">
                                                    <a href="{{ route('edit.aid', $item->id) }}" class="btn btn-success btn-sm">ویرایش</a>

                                                    <a href="{{ route('delete.aid', $item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
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


        </div> <!-- container-fluid -->

    </div> <!-- content -->
@endsection
