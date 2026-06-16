@extends('admin.dashboard')
@section('admin')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Start Content-->
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">اضافه کردن </h4>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">اضافه کردن</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <form action="{{ route('store.permission') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">name</label>
                                <input type="text" class="form-control" name="name">
                            </div>

                            <div class="col-md-6">
                                <label>Select Group</label>
                                <select name="group_name" class="form-control">
                                    <option value="Brand">-- select --</option>
                                    <option value="Users">کاربران</option>
                                    <option value="Category">بخش ها</option>
                                    <option value="Undeposited">واریز نشده</option>
                                    <option value="Recieve">دریافت پرداخت</option>
                                    <option value="Financial">گزارش مالی اعضا</option>
                                    <option value="Credits">کریدت ها</option>
                                    <option value="Aid">کمک ها</option>
                                    <option value="Expense">Expense</option>
                                    <option value="Reports">راپور ها</option>
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
