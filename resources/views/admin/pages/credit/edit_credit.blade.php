@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Start Content-->
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">اضافه کردن درآمد</h4>
            </div>
        </div>

        <!-- Form Validation -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">اضافه کردن درآمد</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <form action="{{ route('update.credit') }}" method="POST" class="row g-3">
                            @csrf

                            <input type="hidden" name="id" value="{{ $credit->id }}">


                            <div class="col-md-6">
                                <label>Credit Account</label>
                                <select name="source_account_id" class="form-control">
                                    <option value="">انتخاب حساب</option>
                                    @foreach ($sourceAccounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ $credit->source_account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>بخش</label>
                                <select name="category_id" class="form-control">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $credit->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم</label>

                                <select name="user_id" class="form-control">
                                    <option value="">انتخاب کاربر</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $credit->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">مقدار</label>
                                <input type="number" class="form-control" name="amount" value="{{ $credit->amount }}">
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">تاریخ</label>
                                <input type="date" class="form-control" name="date" value="{{ $credit->date }}">
                            </div>


                            <div class="col-md-6">
                                <label for="validationDefault02" class="form-label">نوت</label>
                                <textarea class="form-control" name="description">{{ $credit->description }}</textarea>



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
                    user_id: {
                        required: true,
                    },


                },
                messages: {
                    category_id: {
                        required: 'Please Select Category',
                    },
                    user_id: {
                        required: 'Please Select User',
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
