@extends('admin.dashboard')
@section('admin')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">کاربران</h4>
                </div>

                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <a href="{{ route('add.user') }}" class="btn btn-secondary">اضافه کردن</a>
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
                                            <th class="text-center">اسم</th>
                                            <th class="text-center">ولد</th>
                                            <th class="text-center">ولدیت</th>
                                            <th class="text-center">تخلص</th>
                                            <th class="text-center">جنسیت</th>
                                            <th class="text-center">حالت مدنی</th>
                                            <th class="text-center">نوعیت وضعیت</th>
                                            <th class="text-center">وضعیت عضو</th>
                                            <th class="text-center">فیس ماهانه</th>
                                            <th class="text-center">شاخه قومی</th>
                                            <th class="text-center">اسم نماینده</th>
                                            <th class="text-center">عکس</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $item->id }}</td>

                                                <td class="text-center">{{ $item->name }}</td>
                                                <td class="text-center">{{ $item->father_name }}</td>
                                                <td class="text-center">{{ $item->grandfather_name }}</td>
                                                <td class="text-center">{{ $item->lastname }}</td>
                                                <td class="text-center">{{ $item->gender }}</td>
                                                <td class="text-center">{{ $item->marital_status }}</td>
                                                <td class="text-center">{{ $item->member_type }}</td>
                                                <td class="text-center">{{ $item->status }}</td>
                                                <td class="text-center">{{ $item->monthly_fee }}</td>
                                                <td class="text-center">{{ $item->ethnic_branch }}</td>
                                                <td class="text-center">{{ $item->representative_name }}</td>

                                                <td class="text-center">
                                                    <img src="{{ (!empty($item->photo)) 
                                                    ? url('upload/user_images/'.$item->photo) 
                                                    : url('upload/no_image.jpg') }}"
                                                    width="40"
                                                    height="40"
                                                    class="rounded-circle border"
                                                    style="object-fit: cover;">
                                                </td>

                                                <td class="text-center">
                                                    {{-- <a href="{{ route('edit.users', $item->id) }}" class="btn btn-success btn-sm">ویرایش</a>

                                                    <a href="{{ route('delete.user', $item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a> --}}
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
