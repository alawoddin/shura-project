@extends('admin.dashboard')
@section('admin')
    <div class="content">
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">پرداخت‌های پرداخت نشده</h4>
                </div>
                @if ($pendingNotifications->isNotEmpty())
                    <form action="{{ route('unpaid.payments.mark.all') }}" method="POST" class="ms-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            علامت‌گذاری همه اعلان‌ها
                        </button>
                    </form>
                @endif
            </div>

            @if ($pendingNotifications->isNotEmpty())
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading mb-2">
                        <i data-feather="bell"></i>
                        پرداخت‌های جدید دریافت شده — نیاز به بررسی
                    </h5>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>عضو</th>
                                    <th>نوع</th>
                                    <th>نوع تراکنش</th>
                                    <th>مقدار</th>
                                    <th>تاریخ</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingNotifications as $payment)
                                    <tr>
                                        <td>{{ $payment->users->name ?? '-' }}</td>
                                        <td>{{ $payment->category->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $payment->transaction_type === 'debit' ? 'bg-warning' : 'bg-success' }}">
                                                {{ $payment->transaction_type === 'debit' ? 'بدهکار' : 'بستانکار' }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->date }}</td>
                                        <td>
                                            <form action="{{ route('unpaid.payments.mark.reviewed') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                <button type="submit" class="btn btn-sm btn-primary">بررسی شد</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">اعضای دارای پرداخت معوق</h5>
                        </div>
                        <div class="card-body">
                            @if (count($unpaidMembers) === 0)
                                <p class="text-muted mb-0">هیچ پرداخت معوقی یافت نشد.</p>
                            @else
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th class="text-center">عضو</th>
                                                <th class="text-center">ماه</th>
                                                <th class="text-center">مبلغ مورد انتظار</th>
                                                <th class="text-center">پرداخت شده</th>
                                                <th class="text-center">باقی‌مانده</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($unpaidMembers as $item)
                                                <tr>
                                                    <td class="text-center">{{ $item['user']->name }}</td>
                                                    <td class="text-center">{{ $item['month'] }}</td>
                                                    <td class="text-center">{{ number_format($item['expected'], 2) }}</td>
                                                    <td class="text-center">{{ number_format($item['paid'], 2) }}</td>
                                                    <td class="text-center text-danger fw-semibold">
                                                        {{ number_format($item['remaining'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
