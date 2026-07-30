@extends('admin.dashboard')
@section('admin')

<div class="content">
    <div class="container-xxl">
        <div class="py-3">
            <h4>
                گزارش مالی سالانه ( {{ $year }} )
            </h4>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5>مجموع دریافتی</h5>
                        <h2>{{ number_format($totalReceive) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h5>مجموع درآمد</h5>
                        <h2>{{ number_format($totalIncome) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5>مجموع مصارف</h5>
                        <h2>{{ number_format($totalExpense) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5>بلانس</h5>
                        <h2>{{ number_format($balance) }}</h2>
                    </div>
                </div>
            </div>
        </div>
        {{-- جدول مصارف --}}
        {{-- جدول دریافتی --}}
        {{-- جدول درآمد --}}
        {{-- همان سه جدول فایل search_by_month.blade.php را اینجا کپی کنید --}}
    </div>
</div>

@endsection