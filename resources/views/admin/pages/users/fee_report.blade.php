@extends('admin.dashboard')

@section('admin')
<div class="container">
    <h4>گزارش فیس های : {{ $user->name }}</h4>

    <form method="GET" class="mb-3 col-2">
        <label>سال:</label>
        <select name="year" onchange="this.form.submit()" class="form-control">
            @for($y = $currentYear; $y >= $startYear; $y--)
                <option value="{{ $y }}"
                    {{ $year == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
    </form>

    @php
        $totalFee = collect($months)->sum('expected');
        $totalPaid = collect($months)->sum('paid');
        $totalRemaining = collect($months)->sum('remaining');
    @endphp

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="alert alert-primary">
                کل فیس: {{ number_format($totalFee) }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-success">
                کل پرداخت‌شده: {{ number_format($totalPaid) }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-danger">
                کل باقی‌مانده: {{ number_format($totalRemaining) }}
            </div>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ماه</th>
                <th>فیس</th>
                <th>پرداخت‌شده</th>
                <th>باقی‌مانده</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @forelse($months as $month)
                <tr>
                    <td>{{ $month['month'] }}</td>
                    <td class="text-primary">{{ number_format($month['expected']) }}</td>
                    <td class="text-success">{{ number_format($month['paid']) }}</td>
                    <td class="text-danger">{{ number_format($month['remaining']) }}</td>
                    <td>{{ $month['status'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">اطلاعاتی وجود ندارد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection