@extends('admin.dashboard')

@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="container-fluid">

        <div class="py-3">
            <h4 class="fw-semibold">ویرایش دریافت پرداخت</h4>
        </div>

        <div class="card">

            <div class="card-header">
                <h5>ویرایش دریافت پرداخت</h5>
            </div>

            <div class="card-body">

                <form id="myForm" action="{{ route('update.receive.payment') }}" method="POST" class="row g-3">

                    @csrf
                    <input type="hidden" name="id" value="{{ $editData->id }}">

                    {{-- Date --}}
                    <div class="col-md-6">
                        <label class="form-label">تاریخ</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $editData->date) }}">
                    </div>

                    {{-- Member --}}
                    <div class="col-md-6">
                        <label class="form-label">عضو</label>
                        <select name="user_id" class="form-control">
                            <option value="">انتخاب کاربر</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', $editData->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Transaction Type --}}
                    <div class="col-md-6">
                        <label class="form-label">نوع تراکنش</label>
                        <select name="transaction_type" class="form-control">
                            <option value="credit"
                                {{ old('transaction_type', $editData->transaction_type ?? 'credit') === 'credit' ? 'selected' : '' }}>
                                بستانکار (Credit)
                            </option>
                            <option value="debit"
                                {{ old('transaction_type', $editData->transaction_type ?? 'credit') === 'debit' ? 'selected' : '' }}>
                                بدهکار (Debit)
                            </option>
                        </select>
                    </div>

                    {{-- Payment Type --}}
                    <div class="col-md-6">
                        <label class="form-label">نوع پرداخت</label>
                        <select name="category_id" class="form-control" id="category">
                            <option value="">انتخاب نوع پرداخت</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    data-is-monthly-fee="{{ $category->is_monthly_fee ? '1' : '0' }}"
                                    {{ old('category_id', $editData->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- For the month of --}}
                    <div class="col-md-6" id="monthField">
                        <label class="form-label">برای ماه</label>
                        <input type="month" name="month_of" id="month_of" class="form-control"
                            value="{{ old('month_of', $editData->month_of) }}">
                    </div>

                    {{-- Amount --}}
                    <div class="col-md-6">
                        <label class="form-label">مقدار</label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                            value="{{ old('amount', $editData->amount) }}">
                    </div>

                    {{-- Description --}}
                    <div class="col-md-12">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $editData->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">اپدیت</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <script>
        $(document).ready(function() {
            function toggleMonthField() {
                const selected = $('#category').find(':selected');
                const isMonthlyFee = selected.data('is-monthly-fee') == 1;

                if (isMonthlyFee) {
                    $('#month_of').prop('disabled', false).prop('required', true);
                    $('#monthField label').removeClass('text-muted');
                } else {
                    $('#month_of').prop('disabled', true).prop('required', false).val('');
                    $('#monthField label').addClass('text-muted');
                }
            }

            toggleMonthField();

            $('#category').change(function() {
                toggleMonthField();
            });

            $('#myForm').validate({
                rules: {
                    user_id: { required: true },
                    category_id: { required: true },
                    transaction_type: { required: true },
                    amount: { required: true, number: true, min: 0.01 },
                    date: { required: true },
                    month_of: {
                        required: function() {
                            return $('#category').find(':selected').data('is-monthly-fee') == 1;
                        }
                    }
                },
                messages: {
                    user_id: { required: 'عضو را انتخاب کنید' },
                    category_id: { required: 'نوع پرداخت را انتخاب کنید' },
                    amount: { required: 'مقدار را وارد کنید' },
                    date: { required: 'تاریخ را انتخاب کنید' },
                    month_of: { required: 'ماه را انتخاب کنید' }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.parent().append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
