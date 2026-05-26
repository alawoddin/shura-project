@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid my-0">

        <div class="py-3">

            <h4 class="fw-semibold">

                اضافه کردن قرض

            </h4>

        </div>



        <div class="card">

            <div class="card-header">

                <h5>

                    اضافه کردن قرض

                </h5>

            </div>



            <div class="card-body">

                <form id="myForm" action="{{ route('store.credit') }}" method="POST" class="row g-3">

                    @csrf



                    {{-- Category --}}
                    <div class="col-md-6">

                        <label>

                            بخش

                        </label>

                        <select name="category_id" class="form-control">

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




                    {{-- User --}}
                    <div class="col-md-6">

                        <label>

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




                    {{-- Amount --}}
                    <div class="col-md-6">

                        <label>

                            مقدار قرض

                        </label>

                        <input type="number" name="amount" class="form-control">

                    </div>




                    {{-- Date --}}
                    <div class="col-md-6">

                        <label>

                            تاریخ

                        </label>

                        <input type="date" name="date" class="form-control">

                    </div>




                    {{-- Description --}}
                    <div class="col-md-12">

                        <label>

                            نوت

                        </label>

                        <textarea name="description" class="form-control">
</textarea>

                    </div>




                    <div class="col-12">

                        <button type="submit" class="btn btn-primary">

                            ذخیره

                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>




    <script>
        $(function() {

            $('#myForm').validate({

                rules: {

                    category_id: {
                        required: true
                    },

                    user_id: {
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

                    category_id: {
                        required: 'بخش را انتخاب کنید'
                    },

                    user_id: {
                        required: 'کاربر را انتخاب کنید'
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

                }

            });

        });
    </script>
@endsection
