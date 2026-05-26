@extends('admin.dashboard')

@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="container-fluid my-0">

    <div class="py-3">
        <h4 class="fs-18 fw-semibold m-0">
            ویرایش راپور مالی عضو
        </h4>
    </div>

    <div class="row">
        <div class="col-xl-12">

            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">
                        ویرایش راپور مالی
                    </h5>
                </div>

                <div class="card-body">

                    <form id="myForm" action="{{ route('update.financial.report') }}" method="POST" class="row g-3">
                        @csrf

                        <input type="hidden" name="id" value="{{ $editdata->id }}">

                        <div class="col-md-6">
                            <label class="form-label">عضو</label>

                            <select name="member_id" class="form-control">

                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $editdata->member_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">تاریخ</label>

                            <input type="date" class="form-control"
                                name="date"
                                value="{{ $editdata->date }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">بدهکار (Dr)</label>

                            <input type="number" class="form-control"
                                name="debit"
                                value="{{ $editdata->debit }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">بستانکار (Cr)</label>

                            <input type="number" class="form-control"
                                name="credit"
                                value="{{ $editdata->credit }}">
                        </div>

                        {{-- <div class="col-md-6">
                            <label class="form-label">بیلانس</label>

                            <input type="number" class="form-control"
                                name="balance"
                                value="{{ $editdata->balance }}">
                        </div> --}}

                        <div class="col-md-6">
                            <label class="form-label">توضیحات</label>

                            <textarea class="form-control" name="description">
                                {{ $editdata->description }}
                            </textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <button class="btn btn-primary" type="submit">
                                اپدیت
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myForm').validate({
            rules: {
                member_id: {
                    required: true,
                },
                date: {
                    required: true,
                },
                debit: {
                    required: true,
                    number: true,
                },
                credit: {
                    required: true,
                    number: true,
                },
            },
            messages: {
                member_id: {
                    required: 'لطفا عضو را انتخاب کنید',
                },
                date: {
                    required: 'لطفا تاریخ را انتخاب کنید',
                },
                debit: {
                    required: 'لطفا مقدار بدهکار را وارد کنید',
                    number: 'فقط عدد وارد کنید',
                },
                credit: {
                    required: 'لطفا مقدار بستانکار را وارد کنید',
                    number: 'فقط عدد وارد کنید',
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