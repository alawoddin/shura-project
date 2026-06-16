@extends('admin.dashboard')

@section('admin')

@php
$fa = [
    'all.users' => 'صفحه کاربران',
    'all.category' => 'صفحه بخش ها',
    'all.income' => 'صفحه درآمد ها',
    'all.undeposited' => 'واریز نشده ها',
    'all.recieve.payment' => 'دریافت پرداخت',
    'all.financial.report' => 'گزارش مالی',
    'all.credits' => 'کریدت ها',
    'all.aid' => 'کمک ها',
    'all.expense' => 'مصارف',
    'all.report' => 'راپور ها',
];
@endphp

<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">همه نقش‌ها در مجوزها</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                     <a href="{{ route('add.roles.permission') }}" class="btn btn-secondary">اضافه کردن</a>
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
    <table class="table table-bordered dt-responsive table-responsive nowrap">
        <thead>
        <tr>
            <th>آیدی</th>
            <th>نام نقش</th>
            <th>نام مجوز</th>  
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
           @foreach ($roles as $key=> $item) 
            <tr>
                <td>{{ $key+1 }}</td>
                <td style="width: 120px">{{ $item->name }}</td>
                <td> 
                    @foreach ($item->permissions as $prem)
                        <span class="badge bg-danger">{{ $fa[$prem->name] ?? $prem->name }}</span>
                    @endforeach
                    </td> 
                <td>
              <a href="{{ route('admin.edit.roles',$item->id) }}" class="btn btn-success btn-sm">ویرایش</a>  
            <a href="{{ route('admin.delete.roles',$item->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>    
                </td> 
            </tr>
            @endforeach 
                
        </tbody>
    </table>
</div>

                </div>
            </div>
        </div>


     

    </div> <!-- container-fluid -->

</div> <!-- content -->



@endsection