@extends('admin.dashboard')

@section('admin')

    <div class="container-fluid">
        <div class="py-3">
            <h4 class="fw-semibold">
                اضافه کردن دریافت پرداخت
            </h4>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>
                    دریافت پرداخت
                </h5>
            </div>
            <div class="card-body">
                <form id="myForm" action="{{ route('store.receive.payment') }}" method="POST" class="row g-3">
                    @csrf
                    {{-- Date --}}
                    <div class="col-md-6">
                        <label class="form-label">تاریخ</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}">
                    </div>
                    {{-- Member --}}
                    <div class="col-md-6">
                        <label class="form-label">عضو</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">انتخاب کاربر</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    data-monthly-fee="{{ $user->monthly_fee }}"
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Transaction Type --}}
                    <div class="col-md-6">
                        <label class="form-label">نوع تراکنش</label>
                        <select name="transaction_type" class="form-control">
                            <option value="credit" {{ old('transaction_type', 'credit') === 'credit' ? 'selected' : '' }}>
                                بستانکار (Credit)
                            </option>
                            <option value="debit" {{ old('transaction_type') === 'debit' ? 'selected' : '' }}>
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
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- For the month of --}}
                    <div class="col-md-6" id="monthField">
                        <label class="form-label">برای ماه</label>
                        <input type="month" name="month_of" id="month_of" class="form-control"
                            value="{{ old('month_of') }}" disabled>
                    </div>
                    {{-- Amount --}}
                    <div class="col-md-6">
                        <label class="form-label">مقدار</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}">
                    </div>

                    <div class="col-12" id="feeRemainingBox" style="display: none;">
                        <div class="alert alert-danger mb-0">
                            <div class="mb-2">
                                <strong id="feeRemainingTitle">قرض فیس ماهانه به سیستم</strong>
                            </div>
                            <div id="feeRemainingSummary" class="fw-semibold mb-2"></div>
                            <div class="small text-muted mb-2" id="feeRegisterInfo"></div>
                            <div id="feeRemainingList" class="small"></div>
                            <div class="small mt-2" id="feePayHint"></div>
                        </div>
                    </div>

                     {{-- Description --}}
                    <div class="col-md-12">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary">ذخیره</button>
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
                const box = $('#feeRemainingBox .alert');

                $('#feeRemainingTitle').text('قرض فیس ماهانه به سیستم — ' + (feeStatus.user_name || ''));
                $('#feeRegisterInfo').text(
                    'ماه ثبت‌نام: ' + (feeStatus.register_month || '-') +
                    ' | فیس هر ماه: ' + formatMoney(monthlyFee)
                );

                if (!count) {
                    box.removeClass('alert-danger').addClass('alert-success');
                    $('#feeRemainingSummary').text('بدهی فیس به سیستم ندارد. همه ماه‌ها پرداخت شده‌اند.');
                    $('#feeRemainingList').empty();
                    $('#feePayHint').empty();
                } else {
                    box.removeClass('alert-success').addClass('alert-danger');
                    $('#feeRemainingSummary').text(
                        feeStatus.debt_message ||
                        (count + ' ماه پرداخت نشده — قرض سیستم: ' + formatMoney(remainingAmount))
                    );

                    const rows = (feeStatus.months || []).map(function(item, index) {
                        const badge = index === 0 ? ' <span class="badge bg-dark">قدیمی‌ترین</span>' : '';
                        return '<div class="d-flex justify-content-between border-bottom py-1">' +
                            '<span>' + item.month + badge + '</span>' +
                            '<span>پرداخت‌شده: ' + formatMoney(item.paid) +
                            ' | باقی این ماه: ' + formatMoney(item.remaining) + '</span>' +
                            '</div>';
                    }).join('');
                    $('#feeRemainingList').html(rows);
                    $('#feePayHint').html(
                        'اگر الان ۱ ماه بپردازد، بعد از ذخیره سیستم دوباره چک می‌کند و ماه‌های باقی‌مانده را نشان می‌دهد.'
                    );
                }

                $('#feeRemainingBox').show();
            }

            function applyOldestUnpaidMonth() {
                if (!feeStatus) return;

                if (feeStatus.oldest_unpaid_month && !$('#month_of').val()) {
                    $('#month_of').val(feeStatus.oldest_unpaid_month);
                }

                const selectedMonth = $('#month_of').val();
                const match = (feeStatus.months || []).find(function(item) {
                    return item.month === selectedMonth;
                });

                if (match && match.remaining > 0) {
                    $('#amount').val(match.remaining);
                } else if (feeStatus.monthly_fee) {
                    $('#amount').val(feeStatus.monthly_fee);
                }
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
                        applyOldestUnpaidMonth();
                    })
                    .fail(function() {
                        feeStatus = null;
                        $('#feeRemainingBox').hide();
                    });
            }

            toggleMonthField();
            loadFeeStatus();

            $('#category').change(function() {
                toggleMonthField();
                loadFeeStatus();
            });

            $('#user_id').change(function() {
                loadFeeStatus();
            });

            $('#month_of').change(function() {
                if (!feeStatus || !feeStatus.months) return;

                const selectedMonth = $(this).val();
                const match = feeStatus.months.find(function(item) {
                    return item.month === selectedMonth;
                });

                if (match && match.remaining > 0) {
                    $('#amount').val(match.remaining);
                } else if (feeStatus.monthly_fee) {
                    $('#amount').val(feeStatus.monthly_fee);
                }
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
