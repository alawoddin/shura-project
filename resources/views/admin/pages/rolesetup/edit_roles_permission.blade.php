@extends('admin.dashboard')

@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
    .form-check-label{
        text-transform: capitalize;
    }
</style>

<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">ویرایش نقش در مجوز</h4>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">ویرایش نقش در مجوز</h5>
                    </div><!-- end card header -->

<div class="card-body">

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
    'manage.roles' => 'مدیریت نقش ها',
];

$groupFa = [
    'Users' => 'کاربران',
    'Category' => 'بخش ها',
    'Income' => 'درآمد ها',
    'Undeposited' => 'واریز نشده',
    'Recieve' => 'دریافت پرداخت',
    'Financial' => 'گزارش مالی',
    'Credits' => 'کریدت ها',
    'Aid' => 'کمک ها',
    'Expense' => 'مصارف',
    'Reports' => 'راپور ها',
    'Access' => 'مدیریت دسترسی',
];
@endphp

     <form action="{{ route('admin.roles.update',$role->id) }}" method="post" class="row g-3" enctype="multipart/form-data">
        @csrf

        <div class="col-md-6">
            <label for="validationDefault01" class="form-label">نام نقش</label>
            <h4>{{ $role->name }}</h4>
        </div> 

    <div class="form-check mb-2">
    <input class="form-check-input" type="checkbox" id="formCheck1">
    <label class="form-check-label" for="formCheck1">
     تمام مجوز ها
    </label>
    </div>

    <hr>
    @foreach ($permission_groups as $group)
    <div class="row">
        <div class="col-3">

    @php
        $permissions = App\Models\User::getpermissionByGroupName($group->group_name)
    @endphp

     <div class="form-check mb-2">
    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" {{ App\Models\User::roleHasPermissions($role,$permissions) ? 'checked' : '' }} >
    <label class="form-check-label" for="flexCheckDefault">
     {{ $groupFa[$group->group_name] ?? $group->group_name }}
    </label>
    </div> 
        </div>


  <div class="col-9">
    

    @foreach ($permissions as $permission) 
     <div class="form-check mb-2">
    <input class="form-check-input" name="permission[]" value="{{ $permission->id }}" type="checkbox" id="flexCheckDefault{{ $permission->id }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
    <label class="form-check-label" for="flexCheckDefault{{ $permission->id }}">
     {{ $fa[$permission->name] ?? $permission->name }}
    </label>
    </div> 
     @endforeach
     <br> 

      </div>  
    </div> 
    {{-- // End Row --}}
        
    @endforeach
         
        
            
        <div class="col-12">
            <button class="btn btn-primary" type="submit">دخیره</button>
        </div>
    </form>
</div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->

          
        </div>

        

    </div> <!-- container-fluid -->

</div>
 
<script>
    $('#formCheck1').click(function(){
        if($(this).is(':checked')){
            $('input[type=checkbox]').prop('checked',true)
        }else {
             $('input[type=checkbox]').prop('checked',false)
        }
    })
</script>

@endsection