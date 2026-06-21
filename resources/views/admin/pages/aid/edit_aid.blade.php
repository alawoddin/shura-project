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
                    <div class="card-header">
                        <h5 class="card-title mb-0">اضافه کردن کمک</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <form id="myForm" action="{{ route('update.aid') }}" method="POST"  class="row g-3">
                            @csrf

                            <input type="hidden" name="id" id="id" value="{{ $editdata->id }}">
                                    <div class="col-md-6">
                                <label class="form-label">اسم</label>

                                <select name="user_id" class="form-control">
                                    <option value="">انتخاب کاربر</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $editdata->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-6">
                                <label>Aid Source Account</label>
                                <select name="source_account_id" class="form-control">
                                    <option value="">انتخاب حساب</option>
                                    @foreach ($sourceAccounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ $editdata->source_account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>بخش</label>
                                <select name="category_id" class="form-control">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $editdata->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                    

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">مقدار</label>
                                <input type="number" class="form-control" name="amount" value="{{ $editdata->amount }}">
                            </div>

                            <div class="col-md-6">
                                <label for="validationDefault01" class="form-label">تاریخ</label>
                                <input type="date" class="form-control" name="date" value="{{ $editdata->date }}">
                            </div>


                            <div class="col-md-6">
                                <label for="validationDefault02" class="form-label">نوت</label>
                                <textarea class="form-control" name="description">{{ $editdata->description }}</textarea>



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
                    date: {
                        required: true,
                    },


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
