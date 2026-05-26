@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid">

        <div class="py-3">
            <h4 class="fw-semibold">
                اضافه کردن پرداخت ها
            </h4>
        </div>


        <div class="card">

            <div class="card-header">

                <h5>
                    اضافه کردن درآمد
                </h5>

            </div>


            <div class="card-body">

                <form id="myForm" action="{{ route('store.receive.payment') }}" method="POST" class="row g-3">

                    @csrf
                    


                    {{-- Member --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            اسم

                        </label>

                        <select name="user_id" class="form-control">

                            <option value="">
                                انتخاب کاربر
                            </option>

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">

                                    {{ $user->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>



                    {{-- Category --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            نوع پرداخت

                        </label>

                        <select name="category_id" class="form-control" id="category">

                            <option value="">

                                انتخاب بخش

                            </option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">

                                    {{ $category->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>



                    {{-- Month Fee --}}
                    <div class="col-md-6" id="monthField" style="display:none;">

                        <label class="form-label">

                            برای ماه

                        </label>

                        <input type="month" name="month_of" class="form-control">

                    </div>



                    {{-- Amount --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            مقدار

                        </label>

                        <input type="number" name="amount" class="form-control">

                    </div>



                    {{-- Date --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            تاریخ

                        </label>

                        <input type="date" name="date" class="form-control">

                    </div>



                    {{-- Note --}}
                    <div class="col-md-12">

                        <label class="form-label">

                            نوت

                        </label>

                        <textarea name="description" rows="4" class="form-control">
</textarea>

                    </div>



                    <div class="col-12">

                        <button class="btn btn-primary">

                            ذخیره

                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>



    <script>
        $(document).ready(function() {



            $('#category').change(function() {

                let selected =

                    $(this)

                    .find(':selected')

                    .text()

                    .toLowerCase();


                if (

                    selected.includes('monthly')

                    ||

                    selected.includes('fee')

                    ||

                    selected.includes('حق')

                ) {

                    $('#monthField')

                        .show();

                } else {

                    $('#monthField')

                        .hide();

                    $('input[name="month_of"]')

                        .val('');

                }

            });




            $('#myForm').validate({

                rules: {

                    creditor_name: {

                        required: true

                    },

                    category_id: {

                        required: true

                    },

                    amount: {

                        required: true

                    },

                    date: {

                        required: true

                    }

                },

                messages: {

                    creditor_name: {

                        required: 'اسم را انتخاب کنید'

                    },

                    category_id: {

                        required: 'بخش را انتخاب کنید'

                    },

                    amount: {

                        required: 'مقدار را وارد کنید'

                    },

                    date: {

                        required: 'تاریخ را انتخاب کنید'

                    }

                },

                errorElement: 'span',

                errorPlacement: function(
                    error,
                    element
                ) {

                    error.addClass(
                        'invalid-feedback'
                    );

                    element.parent()
                        .append(
                            error
                        );

                },

                highlight: function(
                    element
                ) {

                    $(element)
                        .addClass(
                            'is-invalid'
                        );

                },

                unhighlight: function(
                    element
                ) {

                    $(element)
                        .removeClass(
                            'is-invalid'
                        );

                }

            });

        });
    </script>
@endsection
