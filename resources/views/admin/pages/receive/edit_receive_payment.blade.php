@extends('admin.dashboard')

@section('admin')

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
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">انتخاب کاربر</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    data-monthly-fee="{{ $user->monthly_fee }}"
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
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                            value="{{ old('amount', $editData->amount) }}">
                    </div>

                    <div class="col-12" id="feeRemainingBox" style="display: none;">
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                <strong id="feeRemainingTitle">باقی‌مانده فیس ماهانه</strong>
                                <span id="feeRemainingSummary"></span>
                            </div>
                            <div id="feeRemainingList" class="small"></div>
                        </div>
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
            const feeStatusUrl = @json(route('receive.payment.member.fee.status', ['user' => '__USER__']));
            let feeStatus = null;

            function isMonthlyFeeSelected() {
                return $('#category').find(':selected').data('is-monthly-fee') == 1;
            }

            function toggleMonthField() {
                if (isMonthlyFeeSelected()) {
                    $('#month_of').prop('disabled', false).prop('required', true);
                    $('#monthField label').removeClass('text-muted');
                } else {
                    $('#month_of').prop('disabled', true).prop('required', false).val('');
                    $('#monthField label').addClass('text-muted');
                    $('#feeRemainingBox').hide();
                }
            }

            function formatMoney(value) {
                return Number(value || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function renderFeeRemaining() {
                if (!isMonthlyFeeSelected() || !feeStatus) {
                    $('#feeRemainingBox').hide();
                    return;
                }

                const count = feeStatus.unpaid_months_count || 0;
                const remainingAmount = feeStatus.remaining_amount || 0;
                const monthlyFee = feeStatus.monthly_fee || 0;

                $('#feeRemainingTitle').text('باقی‌مانده فیس ماهانه — ' + (feeStatus.user_name || ''));
                $('#feeRemainingSummary').text(
                    count + ' ماه باقی‌مانده | مبلغ باقی‌مانده: ' + formatMoney(remainingAmount) +
                    ' | فیس ماهانه: ' + formatMoney(monthlyFee)
                );

                if (!count) {
                    $('#feeRemainingList').html('<div class="text-success">تمام ماه‌ها پرداخت شده‌اند.</div>');
                } else {
                    const rows = (feeStatus.months || []).map(function(item) {
                        return '<div class="d-flex justify-content-between border-bottom py-1">' +
                            '<span>' + item.month + '</span>' +
                            '<span>پرداخت‌شده: ' + formatMoney(item.paid) +
                            ' | باقی: ' + formatMoney(item.remaining) + '</span>' +
                            '</div>';
                    }).join('');
                    $('#feeRemainingList').html(rows);
                }

                $('#feeRemainingBox').show();
            }

            function loadFeeStatus() {
                const userId = $('#user_id').val();

                if (!userId || !isMonthlyFeeSelected()) {
                    feeStatus = null;
                    $('#feeRemainingBox').hide();
                    return;
                }

                $.get(feeStatusUrl.replace('__USER__', userId))
                    .done(function(data) {
                        feeStatus = data;
                        renderFeeRemaining();
                    })
                    .fail(function() {
                        feeStatus = null;
                        $('#feeRemainingBox').hide();
                    });
            }

            toggleMonthField();
            loadFeeStatus();

            $('#category, #user_id').change(function() {
                toggleMonthField();
                loadFeeStatus();
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
                            return isMonthlyFeeSelected();
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
