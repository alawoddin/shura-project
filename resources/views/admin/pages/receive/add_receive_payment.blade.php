@extends('admin.dashboard')

@section('admin')

    <div class="container-fluid">
        <div class="py-3">
            <h4 class="fw-semibold">
                دریافت فیس و قرض
            </h4>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>
                    ثبت دریافت فیس اعضا یا قسط قرض
                </h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                @if ($categories->isEmpty())
                    <div class="alert alert-warning">
                        هیچ نوع پرداخت «فیس ماهانه» یا «قرض» تعریف نشده است. لطفاً در
                        <a href="{{ route('add.category') }}">بخش حساب‌ها</a>
                        یک حساب با تیک «فیس ماهانه» یا نوع «حساب‌های دریافتنی» (قرض) بسازید.
                    </div>
                @endif
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
                    <div class="col-md-6" id="transactionTypeField">
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
                                    data-is-loan="{{ $category->isLoanType() ? '1' : '0' }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                    {{ $category->is_monthly_fee ? '(فیس ماهانه)' : '(قرض)' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- For the month of --}}
                    <div class="col-md-6" id="monthField">
                        <label class="form-label">از ماه</label>
                        <input type="month" name="month_of" id="month_of" class="form-control"
                            value="{{ old('month_of') }}" disabled>
                    </div>
                    {{-- Number of months paid at once --}}
                    <div class="col-md-6" id="monthsCountField">
                        <label class="form-label">تعداد ماه (پرداخت یکجا)</label>
                        <input type="number" min="1" max="{{ \App\Http\Controllers\Backend\ReceivePaymentController::MAX_FEE_MONTHS }}"
                            name="months_count" id="months_count" class="form-control"
                            value="{{ old('months_count', 1) }}" disabled>
                        <small class="text-muted" id="monthsRangeHint"></small>
                    </div>
                    {{-- Loan (for loan repayments) --}}
                    <div class="col-md-6" id="loanField" style="display: none;">
                        <label class="form-label">قرض</label>
                        <select name="credit_id" id="credit_id" class="form-control" disabled>
                            <option value="">ابتدا عضو را انتخاب کنید</option>
                        </select>
                        <small class="text-muted" id="loanHint"></small>
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
            const memberLoansUrl = @json(route('receive.payment.member.loans', ['user' => '__USER__']));
            const oldCreditId = @json(old('credit_id'));
            let feeStatus = null;

            function selectedCategory() {
                return $('#category').find(':selected');
            }

            function isMonthlyFeeSelected() {
                return selectedCategory().data('is-monthly-fee') == 1;
            }

            function isLoanSelected() {
                return selectedCategory().data('is-loan') == 1;
            }

            function monthsCount() {
                const value = parseInt($('#months_count').val(), 10);
                return isNaN(value) || value < 1 ? 1 : value;
            }

            function addMonths(month, count) {
                if (!month) return '';
                const parts = month.split('-');
                const date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1 + count, 1);
                return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
            }

            function formatMoney(value) {
                return Number(value || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function toggleFields() {
                const fee = isMonthlyFeeSelected();
                const loan = isLoanSelected();

                $('#month_of').prop('disabled', !fee).prop('required', fee);
                $('#months_count').prop('disabled', !fee);
                $('#monthField, #monthsCountField').toggle(fee);
                if (!fee) {
                    $('#month_of').val('');
                    $('#feeRemainingBox').hide();
                }

                $('#credit_id').prop('disabled', !loan).prop('required', loan);
                $('#loanField').toggle(loan);

                // A loan repayment is always money received (credit).
                if (loan) {
                    $('select[name="transaction_type"]').val('credit');
                }
                $('#transactionTypeField').toggle(!loan);
            }

            function updateMonthsRangeHint() {
                const start = $('#month_of').val();
                const count = monthsCount();

                if (!start || count <= 1) {
                    $('#monthsRangeHint').text('');
                    return;
                }

                $('#monthsRangeHint').text('ماه‌ها: ' + start + ' تا ' + addMonths(start, count - 1));
            }

            // Fill the amount for the selected months: unpaid remainder for past months, full fee for later months.
            function applyFeeAmount() {
                if (!isMonthlyFeeSelected() || !feeStatus) return;

                const start = $('#month_of').val();
                const count = monthsCount();
                const monthlyFee = Number(feeStatus.monthly_fee || 0);
                const unpaid = {};
                (feeStatus.months || []).forEach(function(item) {
                    unpaid[item.month] = Number(item.remaining);
                });

                let total = 0;
                for (let i = 0; i < count; i++) {
                    const month = addMonths(start, i);
                    total += Object.prototype.hasOwnProperty.call(unpaid, month) ? unpaid[month] : monthlyFee;
                }

                if (total > 0) {
                    $('#amount').val(total.toFixed(2));
                }
                updateMonthsRangeHint();
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
                    $('#feePayHint').text('می‌توانید فیس ماه‌های آینده را هم یکجا پرداخت کنید.');
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
                        'برای پرداخت چند ماه یکجا، «تعداد ماه» را وارد کنید. مثلاً ' + count +
                        ' ماه = تمام بدهی (' + formatMoney(remainingAmount) + ').'
                    );
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
                        if (feeStatus.oldest_unpaid_month && !$('#month_of').val()) {
                            $('#month_of').val(feeStatus.oldest_unpaid_month);
                        }
                        applyFeeAmount();
                    })
                    .fail(function() {
                        feeStatus = null;
                        $('#feeRemainingBox').hide();
                    });
            }

            function loadMemberLoans() {
                const userId = $('#user_id').val();
                const select = $('#credit_id');

                if (!isLoanSelected()) return;

                if (!userId) {
                    select.html('<option value="">ابتدا عضو را انتخاب کنید</option>');
                    $('#loanHint').text('');
                    return;
                }

                $.get(memberLoansUrl.replace('__USER__', userId))
                    .done(function(data) {
                        const loans = data.loans || [];

                        if (!loans.length) {
                            select.html('<option value="">این عضو قرض فعال ندارد</option>');
                            $('#loanHint').text('');
                            return;
                        }

                        const options = ['<option value="">انتخاب قرض</option>'].concat(loans.map(function(loan) {
                            return '<option value="' + loan.id + '" data-remaining="' + loan.remaining_amount + '"' +
                                (String(loan.id) === String(oldCreditId) ? ' selected' : '') + '>' +
                                'قرض ' + formatMoney(loan.amount) + ' — تاریخ ' + loan.date +
                                ' — باقی: ' + formatMoney(loan.remaining_amount) +
                                '</option>';
                        }));
                        select.html(options.join(''));

                        if (loans.length === 1 && !select.val()) {
                            select.val(String(loans[0].id));
                        }
                        select.trigger('change');
                    })
                    .fail(function() {
                        select.html('<option value="">خطا در دریافت قرض‌ها</option>');
                    });
            }

            toggleFields();
            loadFeeStatus();
            loadMemberLoans();

            $('#category').change(function() {
                toggleFields();
                loadFeeStatus();
                loadMemberLoans();
            });

            $('#user_id').change(function() {
                loadFeeStatus();
                loadMemberLoans();
            });

            $('#month_of, #months_count').on('change input', function() {
                applyFeeAmount();
            });

            $('#credit_id').change(function() {
                const remaining = $(this).find(':selected').data('remaining');

                if (remaining) {
                    $('#loanHint').text('باقی‌مانده این قرض: ' + formatMoney(remaining));
                    $('#amount').attr('max', remaining);
                    if (!$('#amount').val()) {
                        $('#amount').val(Number(remaining).toFixed(2));
                    }
                } else {
                    $('#loanHint').text('');
                    $('#amount').removeAttr('max');
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
                    },
                    months_count: { digits: true, min: 1, max: {{ \App\Http\Controllers\Backend\ReceivePaymentController::MAX_FEE_MONTHS }} },
                    credit_id: {
                        required: function() {
                            return isLoanSelected();
                        }
                    }
                },
                messages: {
                    user_id: { required: 'عضو را انتخاب کنید' },
                    category_id: { required: 'نوع پرداخت را انتخاب کنید' },
                    amount: { required: 'مقدار را وارد کنید', max: 'مقدار از باقی‌مانده قرض بیشتر است' },
                    date: { required: 'تاریخ را انتخاب کنید' },
                    month_of: { required: 'ماه را انتخاب کنید' },
                    months_count: { min: 'حداقل ۱ ماه', max: 'تعداد ماه زیاد است' },
                    credit_id: { required: 'قرض را انتخاب کنید' }
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
