@extends('admin.dashboard')
@section('admin')

<div class="content">

    <!-- Start Content-->
    <div class="container-xxl">

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">ویرایش مجوز</h5>
                    </div><!-- end card header -->

<div class="card-body">
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form action="{{ route('update.permission') }}" method="post" class="row g-3" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $permissions->id }}">

        <div class="col-md-6">
            <label>نام مجوز</label>
            <select name="name" class="form-control">
                @foreach (config('shura_permissions.permissions') as $permName => $permInfo)
                    <option value="{{ $permName }}" data-group="{{ $permInfo[0] }}" @selected($permissions->name == $permName)>{{ $permInfo[1] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label>انتخاب گروپ</label>
            <select name="group_name" class="form-control">
                @foreach (config('shura_permissions.groups') as $groupKey => $groupLabel)
                    <option value="{{ $groupKey }}" @selected($permissions->group_name == $groupKey)>{{ $groupLabel }}</option>
                @endforeach
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