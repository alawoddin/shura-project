@extends('admin.dashboard')
@section('admin')
    @php
        $typeLabels = ['cash' => 'نقدی', 'aid' => 'کمک‌ها', 'income' => 'درآمد'];
        $typeBadges = ['deposit' => 'bg-success', 'withdraw' => 'bg-danger', 'transfer' => 'bg-primary'];
    @endphp

    <div class="content">
        <div class="container-fluid my-0">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">ارسال و دریافت پول در حساب‌ها</h4>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <!-- Account balances -->
            <div class="row">
                @forelse ($accounts as $account)
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{ route('all.account.transactions', ['account' => $account->id]) }}"
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
                            <h5 class="card-title mb-0">تراکنش جدید</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('store.account.transaction') }}" method="POST" class="row g-3" id="txForm">
                                @csrf
                                <input type="hidden" name="return_account" value="{{ $selectedAccountId }}">

                                <div class="col-12">
                                    <label class="form-label">نوع تراکنش</label>
                                    <select name="type" id="txType" class="form-control">
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type', 'deposit') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12" id="fromWrap">
                                    <label class="form-label">از حساب (ارسال‌کننده)</label>
                                    <select name="from_account_id" class="form-control">
                                        <option value="">انتخاب حساب</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @selected(old('from_account_id', $selectedAccountId) == $account->id)>
                                                {{ $account->name }} ({{ number_format($account->balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12" id="toWrap">
                                    <label class="form-label">به حساب (دریافت‌کننده)</label>
                                    <select name="to_account_id" class="form-control">
                                        <option value="">انتخاب حساب</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @selected(old('to_account_id', $selectedAccountId) == $account->id)>
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

                                <div class="col-12" id="partyWrap">
                                    <label class="form-label" id="partyLabel">از طرف (شخص / منبع)</label>
                                    <input type="text" class="form-control" name="party" value="{{ old('party') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit">ثبت تراکنش</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Transaction history -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                تاریخچه تراکنش‌ها
                                @if ($selectedAccountId)
                                    — {{ $accounts->firstWhere('id', $selectedAccountId)?->name }}
                                @endif
                            </h5>
                            @if ($selectedAccountId)
                                <a href="{{ route('all.account.transactions') }}" class="btn btn-sm btn-light">نمایش همه</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">تاریخ</th>
                                            <th class="text-center">نوع</th>
                                            <th class="text-center">از حساب</th>
                                            <th class="text-center">به حساب</th>
                                            <th class="text-center">مقدار</th>
                                            <th class="text-center">شخص / منبع</th>
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
                                                <td class="text-center">
                                                    <span class="badge {{ $typeBadges[$tx->type] ?? 'bg-secondary' }}">{{ $tx->type_label }}</span>
                                                </td>
                                                <td class="text-center">{{ $tx->fromAccount->name ?? '—' }}</td>
                                                <td class="text-center">{{ $tx->toAccount->name ?? '—' }}</td>
                                                <td class="text-center fw-semibold">{{ number_format($tx->amount, 2) }}</td>
                                                <td class="text-center">{{ $tx->party ?? '—' }}</td>
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

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var typeSelect = document.getElementById('txType');
            var fromWrap = document.getElementById('fromWrap');
            var toWrap = document.getElementById('toWrap');
            var partyWrap = document.getElementById('partyWrap');
            var partyLabel = document.getElementById('partyLabel');

            function syncFields() {
                var type = typeSelect.value;
                fromWrap.style.display = (type === 'withdraw' || type === 'transfer') ? '' : 'none';
                toWrap.style.display = (type === 'deposit' || type === 'transfer') ? '' : 'none';
                partyWrap.style.display = type === 'transfer' ? 'none' : '';
                partyLabel.textContent = type === 'withdraw' ? 'به (شخص / مقصد)' : 'از طرف (شخص / منبع)';
            }

            typeSelect.addEventListener('change', syncFields);
            syncFields();
        });
    </script>
@endsection
