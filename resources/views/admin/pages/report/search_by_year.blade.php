@extends('admin.dashboard')

@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3">
            <h4>
                Yearly Financial Report ( {{ $year }} )
            </h4>
        </div>

        <div class="row">

            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5>Total Receive</h5>
                        <h2>{{ number_format($totalReceive) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h5>Total Income</h5>
                        <h2>{{ number_format($totalIncome) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h5>Total Expense</h5>
                        <h2>{{ number_format($totalExpense) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5>Balance</h5>
                        <h2>{{ number_format($balance) }}</h2>
                    </div>
                </div>
            </div>

        </div>

        {{-- Expenses Table --}}
        {{-- Receives Table --}}
        {{-- Income Table --}}

        {{-- Copy the same three tables from search_by_month.blade.php --}}

    </div>
</div>

@endsection