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
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

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

                    {{-- Only credit (بستانکار) is allowed on this page --}}
                    <input type="hidden" name="transaction_type" value="credit">

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

                    {{-- Amount --}}
                    <div class="col-md-6">
                        <label class="form-label">مقدار</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                            value="{{ old('amount', $editData->amount) }}">
                    </div>

                    {{-- Months --}}
                    @include('admin.pages.receive.partials.month_picker')

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
            const editingPaymentId = @json($editData->id);
            const originalUserId = @json((string) $editData->user_id);
            let preselectMonths = @json(array_values(array_filter((array) old('months', [$editData->month_of]))));
            let feeStatus = null;
            let firstLoad = true;

            function isMonthlyFeeSelected() {
                return $('#category').find(':selected').data('is-monthly-fee') == 1;
            }

            function formatMoney(value) {
                return Number(value || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function toggleMonthField() {
                const fee = isMonthlyFeeSelected();

                $('#monthsField').toggle(fee);
                $('#feeMonthPicker input').prop('disabled', function() {
                    return !fee || $(this).closest('.fee-month').hasClass('is-paid');
                });
                if (!fee) {
                    $('#feeRemainingBox').hide();
                }
            }

            // The amount follows the selected months. On first load keep the saved amount.
            FeeMonthPicker.onChange(function(total) {
                if (firstLoad || !isMonthlyFeeSelected()) return;
                $('#amount').val(total > 0 ? total.toFixed(2) : '');
            });

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
                    FeeMonthPicker.clear();
                    return;
                }

                // For the same member, leave this payment out so its own month is not shown as already paid.
                let url = feeStatusUrl.replace('__USER__', userId);
                if (String(userId) === originalUserId) {
                    url += '?exclude=' + encodeURIComponent(editingPaymentId);
                }

                $.get(url)
                    .done(function(data) {
                        feeStatus = data;
                        renderFeeRemaining();
                        FeeMonthPicker.render(data, preselectMonths);
                        preselectMonths = [];
                        toggleMonthField();
                        firstLoad = false;
                    })
                    .fail(function() {
                        feeStatus = null;
                        $('#feeRemainingBox').hide();
                        FeeMonthPicker.clear('خطا در دریافت وضعیت فیس عضو.');
                        firstLoad = false;
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
                    amount: { required: true, number: true, min: 0.01 },
                    date: { required: true }
                },
                messages: {
                    user_id: { required: 'عضو را انتخاب کنید' },
                    category_id: { required: 'نوع پرداخت را انتخاب کنید' },
                    amount: { required: 'مقدار را وارد کنید' },
                    date: { required: 'تاریخ را انتخاب کنید' }
                },
                submitHandler: function(form) {
                    if (isMonthlyFeeSelected() && !FeeMonthPicker.validate()) {
                        document.getElementById('monthsField').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                    form.submit();
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
