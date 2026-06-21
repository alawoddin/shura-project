@extends('admin.dashboard')

@section('admin')



 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

 <!-- Start Content-->
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">ویرایش</h4>
                </div>
            </div>

            <!-- Form Validation -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">ویرایش</h5>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <form action="{{ route('update.category') }}" method="POST" class="row g-3">
                                @csrf
                                <input type="hidden" name="id" value="{{ $category->id }}">
                                <div class="col-md-6">
                                    <label for="validationDefault01" class="form-label">اسم</label>
                                    <input type="text" class="form-control" name="name" value="{{ $category->name }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نوع حساب</label>
                                    <select name="account_type" class="form-control">
                                        <option value="">عمومی</option>
                                        <option value="receivable" {{ $category->account_type == 'receivable' ? 'selected' : '' }}>Accounts Receivable</option>
                                        <option value="payable" {{ $category->account_type == 'payable' ? 'selected' : '' }}>Accounts Payable</option>
                                        <option value="cash" {{ $category->account_type == 'cash' ? 'selected' : '' }}>Cash / Fund Account</option>
                                        <option value="income" {{ $category->account_type == 'income' ? 'selected' : '' }}>Income Source</option>
                                        <option value="expense" {{ $category->account_type == 'expense' ? 'selected' : '' }}>Expense Account</option>
                                        <option value="payment_type" {{ $category->account_type == 'payment_type' ? 'selected' : '' }}>Payment Type</option>
                                    </select>
                                </div>
                              
                                 <div class="col-md-6">
                                    <label for="validationDefault02" class="form-label">توضیحات</label>
                                    <textarea class="form-control" name="description">{{ $category->description }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_monthly_fee" value="1" id="is_monthly_fee"
                                            {{ $category->is_monthly_fee ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_monthly_fee">
                                            فیس ماهانه (For the month of becomes active)
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button class="btn btn-primary" type="submit">Save Change</button>
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
