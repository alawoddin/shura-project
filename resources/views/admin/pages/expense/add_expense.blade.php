@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Start Content-->
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">اضافه مصارف</h4>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">اضافه مصارف</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <form id="myForm" action="{{ route('store.expense') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label>بخش مصرف</label>
                                <select name="category_id" class="form-control">
                                    <option value="">انتخاب بخش</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>حساب منبع (Expense Account)</label>
                                <select name="source_account_id" class="form-control">
                                    <option value="">انتخاب حساب</option>
                                    @foreach ($sourceAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">نام مصرف</label>
                                <input type="text" class="form-control" name="expense_name">
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">مقدار</label>
                                <input type="text" class="form-control" name="amount">
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">تاریخ</label>
                                <input type="date" class="form-control" name="date">
                            </div>


                            <div class="col-md-6">
                                <label for="validationDefault02" class="form-label">نوت</label>
                                <textarea class="form-control" name="description"></textarea>



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
                    category_id: {
                        required: true,
                    },
                    source_account_id: {
                        required: true,
                    },
                    expense_name: {
                        required: true,
                    },
                    amount: {
                        required: true,
                        number: true,
                    },
                    date: {
                        required: true,
                    },


                },
                messages: {
                    category_id: {
                        required: 'Please Select Category',
                    },
                    expense_name: {
                        required: 'Please Enter Expense Name',
                    },
                    amount: {
                        required: 'Please Enter Amount',
                        number: 'Please Enter Valid Number',
                    },
                    date: {
                        required: 'Please Select Date',
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