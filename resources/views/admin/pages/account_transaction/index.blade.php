@extends('admin.dashboard')
@section('admin')
    @php
        $isTransfer = $mode === 'transfer';
        $pageRoute = $isTransfer ? 'all.account.transactions' : 'all.account.deposits';
        $pageTitle = $isTransfer ? 'انتقال بین حساب‌ها' : 'دریافت پول';
        $typeLabels = ['cash' => 'نقدی', 'aid' => 'کمک‌ها', 'income' => 'درآمد'];
    @endphp

    <div class="content">
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">{{ $pageTitle }}</h4>
                    <small class="text-muted">
                        {{ $isTransfer ? 'انتقال پول از یک حساب به حساب دیگر' : 'دریافت پول به یکی از حساب‌ها' }}
                    </small>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <!-- Account balances -->
            <div class="row">
                @forelse ($accounts as $account)
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{ route($pageRoute, ['account' => $account->id]) }}"
                           class="card text-reset {{ $selectedAccountId === $account->id ? 'border border-primary' : '' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">{{ $account->name }}</span>
                                    <span class="badge {{ $account->account_type === 'aid' ? 'bg-warning text-dark' : 'bg-light text-dark' }}">
                                        {{ $typeLabels[$account->account_type] ?? $account->account_type }}
                                    </span>
                                </div>
                                <div class="fs-18 fw-bold {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($account->balance, 2) }}
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            هیچ حسابی (نقدی، کمک‌ها یا درآمد) تعریف نشده است.
                            <a href="{{ route('add.category') }}">ساختن حساب</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="row">
                <!-- New transaction form -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $isTransfer ? 'انتقال حساب به حساب' : 'دریافت پول جدید' }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('store.account.transaction') }}" method="POST" class="row g-3">
                                @csrf
                                <input type="hidden" name="type" value="{{ $mode }}">
                                <input type="hidden" name="return_account" value="{{ $selectedAccountId }}">

                                @if ($isTransfer)
                                    <div class="col-12">
                                        <label class="form-label">از حساب</label>
                                        <select name="from_account_id" class="form-control" required>
                                            <option value="">انتخاب حساب</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" @selected(old('from_account_id', $selectedAccountId) == $account->id)>
                                                    {{ $account->name }} ({{ number_format($account->balance, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <label class="form-label">{{ $isTransfer ? 'به حساب' : 'به حساب (دریافت‌کننده)' }}</label>
                                    <select name="to_account_id" class="form-control" required>
                                        <option value="">انتخاب حساب</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                @selected(old('to_account_id', $isTransfer ? null : $selectedAccountId) == $account->id)>
                                                {{ $account->name }} ({{ number_format($account->balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">مقدار</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" value="{{ old('amount') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">تاریخ</label>
                                    <input type="date" class="form-control" name="date" value="{{ old('date', now()->toDateString()) }}" required>
                                </div>

                                @unless ($isTransfer)
                                    <div class="col-12">
                                        <label class="form-label">از طرف (شخص / منبع)</label>
                                        <input type="text" class="form-control" name="party" value="{{ old('party') }}">
                                    </div>
                                @endunless

                                <div class="col-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit">
                                        {{ $isTransfer ? 'انتقال' : 'ثبت دریافت' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                {{ $isTransfer ? 'تاریخچه انتقال‌ها' : 'تاریخچه دریافت‌ها' }}
                                @if ($selectedAccountId)
                                    — {{ $accounts->firstWhere('id', $selectedAccountId)?->name }}
                                @endif
                            </h5>
                            @if ($selectedAccountId)
                                <a href="{{ route($pageRoute) }}" class="btn btn-sm btn-light">نمایش همه</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">تاریخ</th>
                                            @if ($isTransfer)
                                                <th class="text-center">از حساب</th>
                                            @endif
                                            <th class="text-center">به حساب</th>
                                            <th class="text-center">مقدار</th>
                                            @unless ($isTransfer)
                                                <th class="text-center">از طرف</th>
                                            @endunless
                                            <th class="text-center">توضیحات</th>
                                            <th class="text-center">ثبت‌کننده</th>
                                            <th class="text-center">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $key => $tx)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">{{ $tx->date?->format('Y-m-d') }}</td>
                                                @if ($isTransfer)
                                                    <td class="text-center">{{ $tx->fromAccount->name ?? '—' }}</td>
                                                @endif
                                                <td class="text-center">{{ $tx->toAccount->name ?? '—' }}</td>
                                                <td class="text-center fw-semibold">{{ number_format($tx->amount, 2) }}</td>
                                                @unless ($isTransfer)
                                                    <td class="text-center">{{ $tx->party ?? '—' }}</td>
                                                @endunless
                                                <td class="text-center">{{ $tx->description }}</td>
                                                <td class="text-center">{{ $tx->creator->name ?? '—' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('delete.account.transaction', $tx->id) }}" class="btn btn-danger btn-sm" id="delete">حذف</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
