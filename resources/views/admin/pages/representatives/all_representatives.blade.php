@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid my-0">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">نماینده‌ها</h4>
                </div>
                <div class="text-end">
                    <a href="{{ route('add.representative') }}" class="btn btn-secondary">اضافه کردن</a>
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
                                            <th class="text-center">اسم نماینده</th>
                                            <th class="text-center">شاخه قومی</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allRepresentatives as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">{{ $item->name }}</td>
                                                <td class="text-center">{{ $item->ethnicBranch->name ?? '-' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('edit.representative', $item->id) }}" class="btn btn-success btn-sm">ویرایش</a>
                                                    <a href="{{ route('delete.representative', $item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
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
