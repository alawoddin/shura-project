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
                                            <th class="text-center">نمبر تذکره</th>
                                            <th class="text-center">گروپ خون</th>
                                            <th class="text-center">تعداد فرزندان</th>
                                            <th class="text-center">حالت مدنی</th>
                                            <th class="text-center">نوعیت وضعیت</th>
                                            <th class="text-center">فیس ماهانه</th>
                                            <th class="text-center">شاخه قومی</th>
                                            <th class="text-center">اسم نماینده</th>
                                            <th class="text-center">عکس</th>
                                            <th class="text-center">وضعیت عضو</th>
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
                                                <td class="text-center">{{ $item->national_id }}</td>
                                                <td class="text-center">{{ $item->blood_group }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('all.users.family', $item->id) }}" class="btn btn-success btn-sm">
                                                        {{ $item->family_members ? $item->family_members : 0 }}
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ $item->marital_status }}</td>
                                                <td class="text-center">
                                                    <span 
                                                        class="badge px-3 py-2"
                                                        style="
                                                            @if($item->member_type == 'golden')
                                                                background: linear-gradient(45deg, #FFD700, #FFC107, #FFEB3B);
                                                                color:#000;
                                                                box-shadow:0 0 10px rgba(255,215,0,0.8);

                                                            @elseif($item->member_type == 'silver')
                                                                background: linear-gradient(45deg, #C0C0C0, #E0E0E0);
                                                                color:#000;

                                                            @else
                                                                background: linear-gradient(45deg, #0d6efd, #4da3ff);
                                                                color:#fff;
                                                            @endif

                                                            border-radius:10px;
                                                            font-size:13px;
                                                        "
                                                    >
                                                        @if($item->member_type == 'golden')
                                                            طلایی
                                                        @elseif($item->member_type == 'silver')
                                                            نقره‌ای
                                                        @else
                                                            معمولی
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $item->monthly_fee }}</td>
                                                <td class="text-center">{{ $item->ethnicBranch->name ?? '-' }}</td>
                                                <td class="text-center">{{ $item->representativeName->name ?? '-' }}</td>

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
                                                    <span 
                                                        class="badge px-2 py-2
                                                            @if($item->status == 'active')
                                                                bg-success
                                                            @elseif($item->status == 'inactive')
                                                                bg-danger
                                                            @elseif($item->status == 'pending')
                                                                bg-warning text-dark
                                                            @elseif($item->status == 'dead')
                                                                bg-dark
                                                            @endif
                                                        "
                                                        style="border-radius:10px; font-size:13px;"
                                                    >
                                                        @if($item->status == 'active')
                                                            فعال
                                                        @elseif($item->status == 'inactive')
                                                            غیرفعال
                                                        @elseif($item->status == 'pending')
                                                            تعلیق
                                                        @elseif($item->status == 'dead')
                                                            فوت شده
                                                        @endif
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <a href="{{ route('edit.users', $item->id) }}" class="btn btn-success btn-sm">ویرایش</a>

                                                    <a href="{{ route('delete.user', $item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
                                                    <a href="{{ route('users.details', $item->id) }}" class="btn btn-info btn-sm">جزئیات</a>
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
