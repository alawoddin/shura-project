{{-- Month picker: choose exactly which months the member pays the fee for (e.g. Jan, Feb, Aug). --}}
<div class="col-12" id="monthsField" style="display: none;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <label class="form-label mb-0">ماه‌های پرداخت فیس</label>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-danger" id="selectUnpaidMonths">انتخاب همه ماه‌های بدهکار</button>
            <button type="button" class="btn btn-sm btn-light" id="clearSelectedMonths">پاک کردن انتخاب</button>
        </div>
    </div>
    <small class="text-muted d-block mb-2">
        ماه‌هایی را که عضو فیس آن را می‌پردازد انتخاب کنید.
        <span class="badge bg-danger">قرمز</span> بدهکار،
        <span class="badge bg-success">سبز</span> پرداخت‌شده،
        <span class="badge bg-light text-dark border">سفید</span> ماه آینده.
    </small>
    <div id="feeMonthPicker">
        <div class="text-muted small">ابتدا عضو را انتخاب کنید.</div>
    </div>
    <div class="fw-semibold mt-2" id="feeMonthSummary"></div>
    <span class="text-danger small" id="feeMonthError" style="display: none;">لطفاً حداقل یک ماه را انتخاب کنید.</span>
</div>

<style>
    #feeMonthPicker .fee-year { font-weight: 600; margin: 10px 0 6px; }
    #feeMonthPicker .fee-month-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(105px, 1fr));
        gap: 6px;
    }
    #feeMonthPicker .fee-month input { position: absolute; opacity: 0; pointer-events: none; }
    #feeMonthPicker .fee-month label {
        display: block;
        margin: 0;
        padding: 6px 4px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        text-align: center;
        font-size: 12px;
        line-height: 1.5;
        cursor: pointer;
        user-select: none;
    }
    #feeMonthPicker .fee-month.is-unpaid label { border-color: #dc3545; color: #dc3545; }
    #feeMonthPicker .fee-month.is-paid label { background: #e8f5ee; border-color: #b7e1c8; color: #198754; cursor: not-allowed; }
    #feeMonthPicker .fee-month input:checked + label { background: #0d6efd; border-color: #0d6efd; color: #fff; }
    #feeMonthPicker .fee-month input:focus-visible + label { outline: 2px solid #0d6efd; outline-offset: 2px; }
</style>

<script>
    window.FeeMonthPicker = (function ($) {
        const MAX_MONTHS = {{ \App\Http\Controllers\Backend\ReceivePaymentController::MAX_FEE_MONTHS }};
        const MONTH_NAMES = ['جنوری', 'فبروری', 'مارچ', 'اپریل', 'می', 'جون', 'جولای', 'اگست', 'سپتمبر', 'اکتوبر', 'نومبر', 'دسمبر'];
        let onChange = function () {};

        function pad(n) { return String(n).padStart(2, '0'); }

        function currentMonth() {
            const d = new Date();
            return d.getFullYear() + '-' + pad(d.getMonth() + 1);
        }

        function addMonths(month, count) {
            const parts = month.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1 + count, 1);
            return d.getFullYear() + '-' + pad(d.getMonth() + 1);
        }

        function formatMoney(value) {
            return Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function selected() {
            return $('#feeMonthPicker input[name="months[]"]:checked').map(function () {
                return this.value;
            }).get();
        }

        function total() {
            let sum = 0;
            $('#feeMonthPicker input[name="months[]"]:checked').each(function () {
                sum += Number($(this).data('due') || 0);
            });
            return Math.round(sum * 100) / 100;
        }

        function updateSummary() {
            const months = selected();
            $('#feeMonthError').hide();

            if (!months.length) {
                $('#feeMonthSummary').text('');
            } else {
                $('#feeMonthSummary').text(
                    months.length + ' ماه انتخاب شد: ' + months.join('، ') + ' — مجموع: ' + formatMoney(total())
                );
            }
            onChange(total(), months);
        }

        /**
         * @param status   JSON from the member fee status endpoint
         * @param preselect months (Y-m) to tick when the grid is drawn
         */
        function render(status, preselect) {
            preselect = (preselect || []).filter(Boolean);
            const fee = Number(status.monthly_fee || 0);
            const unpaid = {};
            (status.months || []).forEach(function (item) { unpaid[item.month] = Number(item.remaining); });

            const now = currentMonth();
            let start = status.register_month || now;
            if (start > now) start = now;
            preselect.forEach(function (m) { if (m < start) start = m; });
            let end = addMonths(now, 12);
            preselect.forEach(function (m) { if (m > end) end = m; });

            let html = '';
            let year = null;

            for (let m = start; m <= end; m = addMonths(m, 1)) {
                const y = m.substring(0, 4);
                if (y !== year) {
                    if (year !== null) html += '</div>';
                    html += '<div class="fee-year">' + y + '</div><div class="fee-month-grid">';
                    year = y;
                }

                const isPre = preselect.indexOf(m) !== -1;
                let cls = 'is-future';
                let due = fee;
                let note = 'فیس: ' + formatMoney(fee);
                let disabled = false;

                if (fee > 0 && Object.prototype.hasOwnProperty.call(unpaid, m)) {
                    cls = 'is-unpaid';
                    due = unpaid[m];
                    note = 'باقی: ' + formatMoney(due);
                } else if (fee > 0 && m <= now && !isPre) {
                    cls = 'is-paid';
                    due = 0;
                    note = 'پرداخت‌شده';
                    disabled = true;
                }

                const id = 'fee-month-' + m;
                html += '<div class="fee-month ' + cls + '">' +
                    '<input type="checkbox" name="months[]" id="' + id + '" value="' + m + '" data-due="' + due + '"' +
                    (isPre && !disabled ? ' checked' : '') + (disabled ? ' disabled' : '') + '>' +
                    '<label for="' + id + '">' + MONTH_NAMES[parseInt(m.substring(5, 7), 10) - 1] +
                    '<br><small>' + m + '</small><br><small>' + note + '</small></label>' +
                    '</div>';
            }
            if (year !== null) html += '</div>';

            $('#feeMonthPicker').html(html);
            updateSummary();
        }

        function clear(message) {
            $('#feeMonthPicker').html('<div class="text-muted small">' + (message || 'ابتدا عضو را انتخاب کنید.') + '</div>');
            $('#feeMonthSummary').text('');
            $('#feeMonthError').hide();
        }

        /** Returns false (and shows an error) when no month is selected. */
        function validate() {
            const count = selected().length;

            if (!count) {
                $('#feeMonthError').text('لطفاً حداقل یک ماه را انتخاب کنید.').show();
                return false;
            }
            if (count > MAX_MONTHS) {
                $('#feeMonthError').text('حداکثر ' + MAX_MONTHS + ' ماه را می‌توانید یکجا پرداخت کنید.').show();
                return false;
            }
            return true;
        }

        $(document).on('change', '#feeMonthPicker input[name="months[]"]', updateSummary);

        $(document).on('click', '#selectUnpaidMonths', function () {
            $('#feeMonthPicker .fee-month.is-unpaid input').prop('checked', true);
            updateSummary();
        });

        $(document).on('click', '#clearSelectedMonths', function () {
            $('#feeMonthPicker input[name="months[]"]').prop('checked', false);
            updateSummary();
        });

        return {
            render: render,
            clear: clear,
            selected: selected,
            total: total,
            validate: validate,
            onChange: function (fn) { onChange = fn; },
        };
    })(jQuery);
</script>
