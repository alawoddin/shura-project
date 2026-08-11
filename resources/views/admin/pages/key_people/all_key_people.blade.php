@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid my-0">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">افراد کلیدی</h4>
                </div>
                <div class="text-end">
                    <a href="{{ route('add.key.person') }}" class="btn btn-secondary">اضافه کردن</a>
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
                                            <th class="text-center">نام</th>
                                            <th class="text-center">سمت</th>
                                            <th class="text-center">شماره تماس</th>
                                            <th class="text-center">ایمیل</th>
                                            <th class="text-center">یادداشت</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($keyPeople as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">{{ $item->user->name ?? '-' }}</td>
                                                <td class="text-center">{{ $item->position }}</td>
                                                <td class="text-center">{{ $item->user->phone ?? '-' }}</td>
                                                <td class="text-center">{{ $item->user->email ?? '-' }}</td>
                                                <td class="text-center">{{ $item->note ?? '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('edit.key.person', $item->id) }}" class="btn btn-success btn-sm">ویرایش</a>
                                                    <a href="{{ route('delete.key.person', $item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
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
