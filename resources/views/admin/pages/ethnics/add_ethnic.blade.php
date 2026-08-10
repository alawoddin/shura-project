@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Start Content-->
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">اضافه کردن شاخه قومی</h4>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">

                    <div class="card-body">
                        <form id="myForm" action="{{ route('store.ethnic') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label class="form-label">اسم قوم</label>
                                <input type="text" class="form-control" name="name">
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




    <script type="text/javascript">
        $(document).ready(function() {
            $('#myForm').validate({
                rules: {
                    user_id: {
                        required: true,
                    },
                    category_id: {
                        required: true,
                    },
                    amount: {
                        required: true,
                        number: true,
                    },
                    source_account_id: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                messages: {
                    user_id: {
                        required: 'لطفاً کاربر را انتخاب کنید',
                    },
                    category_id: {
                        required: 'لطفاً بخش را انتخاب کنید',
                    },
                    amount: {
                        required: 'لطفاً مقدار را وارد کنید',
                        number: 'فقط عدد وارد کنید',
                    },
                    date: {
                        required: 'لطفاً تاریخ را انتخاب کنید',
                    },


                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.parent().append(error);
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