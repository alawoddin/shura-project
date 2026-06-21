@extends('admin.dashboard')
@section('admin')
    <div class="content">

        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Undeposited Fund / Cash Flow</h4>
                </div>

                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <a href="{{ route('add.income') }}" class="btn btn-secondary">اضافه کردن</a>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">

                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">آیدی</th>
                                            <th class="text-center">تاریخ</th>
                                            <th class="text-center">توضیحات</th>
                                            <th class="text-center">عضو/منبع</th>
                                            <th class="text-center">مقدار</th>
                                            <th class="text-center">نوع</th>
                                            <th class="text-center">وضعیت</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($undepositedIncomes as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">{{ $item->income->date }}</td>
                                                <td class="text-center">{{ $item->income->note }}</td>
                                                <td class="text-center">{{ $item->income->creditor_name }}</td>
                                                <td class="text-center">{{ $item->income->amount }}</td>
                                                <td class="text-center">{{ $item->income->category->name ?? '-' }}</td>
                                                <td class="text-center">

                                                    @if ($item->status == 'pending')
                                                        <form action="{{ route('income.transfer', $item->id) }}"
                                                            method="POST" class="d-flex gap-2 justify-content-center">

                                                            @csrf

                                                            <select name="target_account_id" class="form-select form-select-sm" required>
                                                                <option value="">Transfer to account</option>
                                                                @foreach ($cashAccounts as $account)
                                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                                @endforeach
                                                            </select>

                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                انتقال
                                                            </button>

                                                        </form>
                                                    @elseif($item->status == 'transferred')
                                                        <span class="badge bg-success">
                                                            انتقال شد
                                                            @if ($item->targetAccount)
                                                                ({{ $item->targetAccount->name }})
                                                            @endif
                                                        </span>
                                                    @elseif($item->status == 'cancelled')
                                                        <span class="badge bg-danger">لغو شد</span>
                                                    @endif

                                                </td>

                                                <td class="text-center">
                                                    <a href="{{ route('edit.income', $item->income_id) }}"
                                                        class="btn btn-success btn-sm">ویرایش</a>

                                                    <a href="{{ route('delete.income', $item->income_id) }}"
                                                        class="btn btn-danger btn-sm" id="delete">حذف</a>
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

        </div>

    </div>
@endsection
