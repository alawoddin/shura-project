@extends('admin.dashboard')
@section('admin')
    @php
        $monthNames = ['جنوری', 'فبروری', 'مارچ', 'اپریل', 'می', 'جون', 'جولای', 'اگست', 'سپتمبر', 'اکتوبر', 'نومبر', 'دسمبر'];
        $reportType = old('report_type', 'daily');
    @endphp

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">گزارشات عمومی</h4>
                    <small class="text-muted">نوع گزارش (روزانه، ماهانه یا سالانه) را انتخاب کنید</small>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <form id="reportForm" action="{{ route('search.reports.by.date') }}" method="post">
                                @csrf

                                {{-- Report type --}}
                                <div class="mb-3">
                                    <label class="form-label" for="report_type">نوع گزارش</label>
                                    <select name="report_type" id="report_type" class="form-select">
                                        <option value="daily" @selected($reportType === 'daily')>گزارش روزانه</option>
                                        <option value="monthly" @selected($reportType === 'monthly')>گزارش ماهانه</option>
                                        <option value="yearly" @selected($reportType === 'yearly')>گزارش سالانه</option>
                                    </select>
                                </div>

                                {{-- Daily --}}
                                <div class="mb-3 report-field" data-type="daily">
                                    <label class="form-label" for="report_date">تاریخ</label>
                                    <input class="form-control" type="date" name="date" id="report_date"
                                        value="{{ old('date', now()->toDateString()) }}">
                                </div>

                                {{-- Monthly --}}
                                <div class="mb-3 report-field" data-type="monthly">
                                    <label class="form-label" for="report_month">ماه</label>
                                    <select name="month" id="report_month" class="form-select">
                                        @foreach ($monthNames as $i => $name)
                                            <option value="{{ $i + 1 }}" @selected((int) old('month', now()->month) === $i + 1)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Yearly --}}
                                <div class="mb-3 report-field" data-type="yearly">
                                    <label class="form-label" for="report_year">سال</label>
                                    <select name="year" id="report_year" class="form-select">
                                        @for ($y = now()->year; $y >= 2025; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">نمایش گزارش</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->

    </div> <!-- content -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reportForm');
            const typeSelect = document.getElementById('report_type');
            const targets = {
                daily: { action: @json(route('search.reports.by.date')), method: 'post' },
                monthly: { action: @json(route('search.reports.by.month')), method: 'post' },
                yearly: { action: @json(route('annual.report')), method: 'get' },
            };

            function sync() {
                const type = typeSelect.value;

                // Show only the field of the chosen report type; hidden fields are not sent.
                form.querySelectorAll('.report-field').forEach(function (field) {
                    const active = field.dataset.type === type;
                    field.style.display = active ? '' : 'none';
                    field.querySelectorAll('input, select').forEach(function (input) {
                        input.disabled = !active;
                    });
                });

                form.action = targets[type].action;
                form.method = targets[type].method;
                // The yearly report is a normal page link, it does not need the form token.
                form.querySelector('input[name="_token"]').disabled = type === 'yearly';
                typeSelect.disabled = false;
            }

            typeSelect.addEventListener('change', sync);
            window.addEventListener('pageshow', sync); // coming back with the browser's Back button
            sync();

            // report_type itself is only used on this page; do not send it to the yearly page URL.
            form.addEventListener('submit', function () {
                if (typeSelect.value === 'yearly') {
                    typeSelect.disabled = true;
                }
            });
        });
    </script>
@endsection
