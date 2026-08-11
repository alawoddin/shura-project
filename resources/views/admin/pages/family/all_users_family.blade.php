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
                        @if($familyMembers->count() < $user->family_members)
                            <a href="{{ route('add.users.family', $user->id) }}" class="btn btn-secondary">
                                اضافه کردن
                            </a>
                        @endif
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
                                            <th class="text-center">اسم کاربر</th>
                                            <th class="text-center">اسم عضو فامیل</th>
                                            <th class="text-center">جنسیت</th>
                                            <th class="text-center">تاریخ تولد</th>
                                            <th class="text-center">سن</th>
                                            <th class="text-center">حالت مدنی</th>
                                            <th class="text-center">حساب کاربری</th>
                                            <th class="text-center">تحصیلات</th>
                                            <th class="text-center">درجه</th>
                                            <th class="text-center">یادداشت</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($familyMembers as $key => $item)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">{{ $item->user->name }}</td>
                                                <td class="text-center">{{ $item->name }}</td>
                                                <td class="text-center">{{ $item->gender }}</td>
                                                <td class="text-center">{{ $item->birth_date }}</td>
                                                <td class="text-center">{{ $item->birth_date ? \Carbon\Carbon::parse($item->birth_date)->age : '-' }}</td>
                                                <td class="text-center">
                                                    {{ ($item->marital_status ?? 'single') === 'married' ? 'متاهل' : 'مجرد' }}
                                                </td>
                                                <td class="text-center">
                                                    @if($item->linked_user_id)
                                                        <span class="badge bg-success">ساخته شده</span>
                                                    @elseif(($item->marital_status ?? 'single') === 'married')
                                                        <a href="{{ route('create.account.family', $item->id) }}" class="btn btn-sm btn-primary">ساخت حساب</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->qualification }}</td>
                                                <td class="text-center">{{ $item->degree }}</td>
                                                <td class="text-center">{{ $item->note }}</td>
                                                

                                                <td class="text-center">
                                                    <a href="{{ route('edit.users.family', $item->id) }}" class="btn btn-success btn-sm">ویرایش</a>

                                                    <a href="{{ route('delete.users.family', $item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
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