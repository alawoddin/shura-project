@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Start Content-->
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">اضافه کردن کمک</h4>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">

                    <div class="card-body">
                        <form id="myForm" action="{{ route('update.ethnic') }}" method="POST"  class="row g-3">
                            @csrf

                            <input type="hidden" name="id" id="id" value="{{ $ethnic->id }}">

                            <div class="col-md-6">
                                <label class="form-label">اسم قوم</label>
                                <input type="text" name="name" id="id" class="form-control" value="{{ $ethnic->name }}">
                            </div>

                                <div class="col-12 mt-3">
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
