@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid">

        <div class="py-3">
            <h4 class="fw-semibold">
                ویرایش پرداخت
            </h4>
        </div>


        <div class="card">

            <div class="card-header">

                <h5>
                    ویرایش پرداخت
                </h5>

            </div>


            <div class="card-body">

                <form id="myForm" action="{{ route('update.receive.payment') }}" method="POST" class="row g-3">

                    @csrf


                    <input type="hidden" name="id" value="{{ $editData->id }}">



                    {{-- User --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            اسم

                        </label>

                        <select name="user_id" class="form-control">

                            <option value="">
                                انتخاب کاربر
                            </option>

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $editData->user_id ? 'selected' : '' }}>

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
                                <option value="{{ $category->id }}"
                                    {{ $category->id == $editData->category_id ? 'selected' : '' }}>

                                    {{ $category->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>




                    {{-- Month --}}
                    <div class="col-md-6" id="monthField">

                        <label class="form-label">

                            برای ماه

                        </label>

                        <input type="month" name="month_of" class="form-control" value="{{ $editData->month_of }}">

                    </div>




                    {{-- Amount --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            مقدار

                        </label>

                        <input type="number" name="amount" class="form-control" value="{{ $editData->amount }}">

                    </div>




                    {{-- Date --}}
                    <div class="col-md-6">

                        <label class="form-label">

                            تاریخ

                        </label>

                        <input type="date" name="date" class="form-control" value="{{ $editData->date }}">

                    </div>




                    {{-- Description --}}
                    <div class="col-md-12">

                        <label class="form-label">

                            نوت

                        </label>

                        <textarea name="description" rows="4" class="form-control">{{ $editData->description }}</textarea>

                    </div>




                    <div class="col-12">

                        <button class="btn btn-primary">

                            اپدیت

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <script>
        $(document).ready(function() {


            function toggleMonthField() {

                let selected =

                    $('#category')
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

                }

            }



            toggleMonthField();



            $('#category').change(function() {

                toggleMonthField();

            });




            $('#myForm').validate({

                rules: {

                    user_id: {
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

                    user_id: {
                        required: 'کاربر را انتخاب کنید'
                    },

                    category_id: {
                        required: 'نوع پرداخت را انتخاب کنید'
                    },

                    amount: {
                        required: 'مقدار را وارد کنید'
                    },

                    date: {
                        required: 'تاریخ را انتخاب کنید'
                    }

                }

            });


        });
    </script>
@endsection
