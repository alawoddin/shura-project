@extends('admin.dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Edit Permission</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    
                    <li class="breadcrumb-item active">Edit Permission</li>
                </ol>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Permission</h5>
                    </div><!-- end card header -->

<div class="card-body">
    <form action="{{ route('update.permission') }}" method="post" class="row g-3" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $permissions->id }}">

        <div class="col-md-6">
            <label>نام بخش</label>
            <select name="name" class="form-control">
                <option value="all.users" {{ $permissions->name == 'all.users' ? 'selected' : '' }}>صفحه کاربران</option>
                <option value="all.category" {{ $permissions->name == 'all.category' ? 'selected' : '' }}>صفحه بخش ها</option>
                <option value="all.income" {{ $permissions->name == 'all.income' ? 'selected' : '' }}>صفحه درآمد ها</option>
                <option value="all.undeposited" {{ $permissions->name == 'all.undeposited' ? 'selected' : '' }}>صفحه واریز نشده ها</option>
                <option value="all.recieve.payment" {{ $permissions->name == 'all.recieve.payment' ? 'selected' : '' }}>صفحه دریافت پرداخت</option>
                <option value="all.financial.report" {{ $permissions->name == 'all.financial.report' ? 'selected' : '' }}>صفحه گزارش مالی اعضا</option>
                <option value="all.credits" {{ $permissions->name == 'all.credits' ? 'selected' : '' }}>صفحه کریدت ها</option>
                <option value="all.aid" {{ $permissions->name == 'all.aid' ? 'selected' : '' }}>صفحه کمک ها</option>
                <option value="all.expense" {{ $permissions->name == 'all.expense' ? 'selected' : '' }}>صفحه مصارف</option>
                <option value="all.report" {{ $permissions->name == 'all.report' ? 'selected' : '' }}>صفحه راپور ها</option>
            </select>
        </div>

        <div class="col-md-6">
            <label>انتخاب گروپ</label>
            <select name="group_name" class="form-control">
                <option value="Users" {{ $permissions->group_name == 'Users' ? 'selected' : '' }}>کاربران</option>
                <option value="Category" {{ $permissions->group_name == 'Category' ? 'selected' : '' }}>بخش ها</option>
                <option value="Income" {{ $permissions->group_name == 'Income' ? 'selected' : '' }}>درآمد ها</option>
                <option value="Undeposited" {{ $permissions->group_name == 'Undeposited' ? 'selected' : '' }}>واریز نشده</option>
                <option value="Recieve" {{ $permissions->group_name == 'Recieve' ? 'selected' : '' }}>دریافت پرداخت</option>
                <option value="Financial" {{ $permissions->group_name == 'Financial' ? 'selected' : '' }}>گزارش مالی اعضا</option>
                <option value="Credits" {{ $permissions->group_name == 'Credits' ? 'selected' : '' }}>کریدت ها</option>
                <option value="Aid" {{ $permissions->group_name == 'Aid' ? 'selected' : '' }}>کمک ها</option>
                <option value="Expense" {{ $permissions->group_name == 'Expense' ? 'selected' : '' }}>مصارف</option>
                <option value="Reports" {{ $permissions->group_name == 'Reports' ? 'selected' : '' }}>راپور ها</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary" type="submit">ذخیره تغییرات</button>
        </div>
    </form>
</div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->

          
        </div>

        

    </div> <!-- container-fluid -->

</div>
 

@endsection