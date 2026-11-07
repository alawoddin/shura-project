@extends('admin.dashboard')
@section('admin')


    <!-- Start Content-->
    <div class="container-fluid my-0">

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">اضافه کردن</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form action="{{ route('store.permission') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                {{-- <label for="validationDefault01" class="form-label">name</label>
                                <input type="text" class="form-control" name="name"> --}}

                                <label>نام مجوز</label>
                                <select name="name" class="form-control">
                                    <option value="">-- انتخاب --</option>
                                    @foreach (config('shura_permissions.permissions') as $permName => $permInfo)
                                        <option value="{{ $permName }}" data-group="{{ $permInfo[0] }}">{{ $permInfo[1] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>انتخاب گروپ</label>
                                <select name="group_name" class="form-control">
                                    <option value="">-- انتخاب --</option>
                                    @foreach (config('shura_permissions.groups') as $groupKey => $groupLabel)
                                        <option value="{{ $groupKey }}">{{ $groupLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <button class="btn btn-primary" type="submit">ذخیره</button>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->


        </div>



    </div> <!-- container-fluid -->

    </div>




    <script type="text/javascript">
        $(document).ready(function() {
            $('#myForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    father_name: {
                        required: true,
                    },


                },
                messages: {
                    name: {
                        required: 'Please Enter customer name',
                    },
                    father_name: {
                        required: 'Please Enter User father_name',
                    },


                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            });
        });
    </script>
@endsection
