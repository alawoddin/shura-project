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
                        <h5 class="card-title mb-0">ویرایش نقش</h5>
                    </div><!-- end card header -->

<div class="card-body">
    <form action="{{ route('update.roles') }}" method="post" class="row g-3" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="id" value="{{ $roles->id }}">

        <div class="col-md-6">
            <label for="validationDefault01" class="form-label">اسم نقش</label>
            <input type="text" class="form-control" name="name" value="{{ $roles->name }}"  > 
        </div>
 
            
        <div class="col-12">
            <button class="btn btn-primary" type="submit">ذخیره</button>
        </div>
    </form>
</div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->

          
        </div>

        

    </div> <!-- container-fluid -->

</div>
 

@endsection