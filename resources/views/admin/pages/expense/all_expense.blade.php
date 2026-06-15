@extends('admin.dashboard')
@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">مصارف</h4>
                </div>

                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <a href="{{ route('add.expense') }}" class="btn btn-secondary">اضافه کردن</a>
                    </ol>
                </div>
            </div>

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
                                            <th class="text-center">بخش</th>
                                            <th class="text-center">نام مصرف</th>
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
                                                <td class="text-center">{{ $item->category->name }}</td>
                                                <td class="text-center">{{ $item->expense_name }}</td>
                                                <td class="text-center">{{ $item->amount }}</td>
                                                <td class="text-center">{{ $item->date }}</td>
                                                <td class="text-center">{{ $item->description }}</td>


                                                <td class="text-center">
                                                    @if (Auth::guard('web')->user()->can('edit.expense'))
                                                         <a href="{{ route('edit.expense', $item->id) }}"
                                                        class="btn btn-success btn-sm">ویرایش</a>
                                                    @endif

                                                    @if (Auth::guard('web')->user()->can('delete.expense'))
                                                         <a href="{{ route('delete.expense', $item->id) }}"
                                                        class="btn btn-danger btn-sm" id="delete">حذف</a>
                                                    @endif
                                                    {{-- <a href="{{ route('edit.expense', $item->id) }}"
                                                        class="btn btn-success btn-sm">ویرایش</a> --}}

                                                    {{-- <a href="{{ route('delete.expense', $item->id) }}"
                                                        class="btn btn-danger btn-sm" id="delete">حذف</a> --}}
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
