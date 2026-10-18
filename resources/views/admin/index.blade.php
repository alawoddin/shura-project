@extends('admin.dashboard')

@section('title', 'داشبورد')

@section('admin')
    <style>
        .shura-dash {
            --ink: #1c2430;
            --muted: #667085;
            --line: #e7e1d6;
            --paper: #f6f3ec;
            --card: #ffffff;
            --green: #1f7a4d;
            --green-soft: #e7f6ee;
            --red: #b42318;
            --red-soft: #fdeceb;
            --gold: #9a6b2f;
            --gold-soft: #f8f1e4;
            --blue: #1d4e89;
            --blue-soft: #e8f1fb;
            color: var(--ink);
        }

        .shura-dash .shura-hero {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: stretch;
            padding: 28px;
            border-radius: 22px;
            background:
                radial-gradient(circle at left top, rgba(255, 255, 255, .16), transparent 34%),
                linear-gradient(135deg, #16324f 0%, #1f7a4d 100%);
            color: #fff;
            margin-bottom: 22px;
            box-shadow: 0 18px 40px rgba(22, 50, 79, .18);
        }

        .shura-dash .eyebrow {
            margin: 0 0 8px;
            letter-spacing: .08em;
            font-size: 12px;
            opacity: .75;
        }

        .shura-dash .shura-hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 700;
        }

        .shura-dash .shura-hero p {
            margin: 0;
            opacity: .86;
        }

        .shura-dash .hero-balance {
            min-width: 260px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 18px;
            padding: 18px 20px;
            backdrop-filter: blur(8px);
        }

        .shura-dash .hero-balance span,
        .shura-dash .hero-balance small {
            display: block;
            opacity: .8;
        }

        .shura-dash .hero-balance strong {
            display: block;
            margin: 6px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .shura-dash .quick-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
        }

        .shura-dash .quick-row a {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--ink);
            border-radius: 999px;
            padding: 8px 14px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .shura-dash .quick-row a:hover {
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .shura-dash .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .shura-dash .metric {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px 16px 14px;
            box-shadow: 0 8px 24px rgba(28, 36, 48, .04);
        }

        .shura-dash .metric .label {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 8px;
        }

        .shura-dash .metric .value {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.2;
        }

        .shura-dash .metric .hint {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
        }

        .shura-dash .metric.green { background: linear-gradient(180deg, var(--green-soft), #fff 58%); }
        .shura-dash .metric.red { background: linear-gradient(180deg, var(--red-soft), #fff 58%); }
        .shura-dash .metric.gold { background: linear-gradient(180deg, var(--gold-soft), #fff 58%); }
        .shura-dash .metric.blue { background: linear-gradient(180deg, var(--blue-soft), #fff 58%); }
        .shura-dash .metric.green .value { color: var(--green); }
        .shura-dash .metric.red .value { color: var(--red); }
        .shura-dash .metric.gold .value { color: var(--gold); }
        .shura-dash .metric.blue .value { color: var(--blue); }

        .shura-dash .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(28, 36, 48, .04);
            margin-bottom: 22px;
        }

        .shura-dash .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            background: #fbfaf7;
        }

        .shura-dash .panel-head h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .shura-dash .panel table {
            margin: 0;
        }

        .shura-dash .panel th {
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
            background: #fff;
            white-space: nowrap;
        }

        .shura-dash .panel td,
        .shura-dash .panel th {
            padding: 12px 14px;
            vertical-align: middle;
        }

        .shura-dash .split {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 14px;
        }

        .shura-dash .bar {
            height: 8px;
            background: #efeae1;
            border-radius: 99px;
            overflow: hidden;
        }

        .shura-dash .bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #1f7a4d, #c4a35a);
            border-radius: 99px;
        }

        .shura-dash .tag {
            display: inline-block;
            border-radius: 99px;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .shura-dash .tag.cash { background: var(--blue-soft); color: var(--blue); }
        .shura-dash .tag.income { background: var(--gold-soft); color: var(--gold); }

        @media (max-width: 1100px) {
            .shura-dash .metric-grid,
            .shura-dash .split,
            .shura-dash .shura-hero {
                grid-template-columns: 1fr;
                display: grid;
            }

            .shura-dash .hero-balance { min-width: 0; }
        }
    </style>

    <div class="content">
        <div class="container-fluid shura-dash">
            <div class="shura-hero">
                <div>
                    <p class="eyebrow">سیستم مدیریت شورا</p>
                    <h1>سلام، {{ Auth::user()->name }}</h1>
                    <p>امروز {{ now()->format('Y/m/d') }} است. وضعیت مالی، اعضا و حساب‌ها اینجاست.</p>
                </div>
                <div class="hero-balance">
                    <span>مبلغ فعلی</span>
                    <strong>{{ number_format($currentBalance, 2) }}</strong>
                    <small>ماه جاری: {{ number_format($monthCurrentBalance, 2) }}</small>
                </div>
            </div>

            <div class="quick-row">
                @if (Auth::user()->can('all.users'))
                    <a href="{{ route('all.users') }}">کاربران</a>
                @endif
                @if (Auth::user()->can('all.income'))
                    <a href="{{ route('all.income') }}">درامدها</a>
                @endif
                @if (Auth::user()->can('all.undeposited'))
                    <a href="{{ route('undeposited.income') }}">واریز نشده</a>
                @endif
                @if (Auth::user()->can('all.expense'))
                    <a href="{{ route('all.expense') }}">مصارف</a>
                @endif
                @if (Auth::user()->can('all.aid'))
                    <a href="{{ route('all.aid') }}">کمک‌ها</a>
                @endif
                @if (Auth::user()->can('all.credits'))
                    <a href="{{ route('all.credits') }}">قرض‌ها</a>
                @endif
                <a href="{{ route('admin.backup') }}">پشتیبان‌گیری</a>
            </div>

            <div class="metric-grid">
                <div class="metric green">
                    <div class="label">مجموع درآمد</div>
                    <div class="value">{{ number_format($totalIncomeAll, 2) }}</div>
                    <div class="hint">واریز شده: {{ number_format($totalIncome, 2) }}</div>
                </div>
                <div class="metric red">
                    <div class="label">مجموع مصارف</div>
                    <div class="value">{{ number_format($totalExpense, 2) }}</div>
                    <div class="hint">ماه جاری: {{ number_format($monthExpense, 2) }}</div>
                </div>
                <div class="metric blue">
                    <div class="label">بستانکار</div>
                    <div class="value">{{ number_format($totalCredit, 2) }}</div>
                    <div class="hint">دریافتی: {{ number_format($totalReceiveCredit, 2) }}</div>
                </div>
                <div class="metric gold">
                    <div class="label">بدهکار</div>
                    <div class="value">{{ number_format($totalDebit, 2) }}</div>
                    <div class="hint">دریافتی: {{ number_format($totalReceiveDebit, 2) }}</div>
                </div>
                <div class="metric">
                    <div class="label">مبلغ دریافت شده</div>
                    <div class="value">{{ number_format($totalReceive, 2) }}</div>
                    <div class="hint">ماه جاری: {{ number_format($monthReceive, 2) }}</div>
                </div>
                <div class="metric">
                    <div class="label">مجموعه بلانس</div>
                    <div class="value">{{ number_format($totalBalance, 2) }}</div>
                    <div class="hint">درآمد واریز شده + دریافتی</div>
                </div>
                <div class="metric green">
                    <div class="label">کمک‌ها</div>
                    <div class="value">{{ number_format($totalAids, 2) }}</div>
                    <div class="hint">ماه جاری: {{ number_format($monthAids, 2) }}</div>
                </div>
                <div class="metric blue">
                    <div class="label">کاربران</div>
                    <div class="value">{{ $totalUser }}</div>
                    <div class="hint">ماه جاری: {{ $monthUser }}</div>
                </div>
            </div>

            <div class="split">
                <div class="panel">
                    <div class="panel-head">
                        <h5>خلاصه مالی</h5>
                        <span class="hint">کل و ماه جاری</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>بخش</th>
                                    <th>مجموع</th>
                                    <th>ماه جاری</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>مجموع درآمد</td>
                                    <td>{{ number_format($totalIncomeAll, 2) }}</td>
                                    <td>{{ number_format($monthIncomeAll, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>درآمد واریز شده</td>
                                    <td>{{ number_format($totalIncome, 2) }}</td>
                                    <td>{{ number_format($monthIncome, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>مجموع دریافتی</td>
                                    <td>{{ number_format($totalReceive, 2) }}</td>
                                    <td>{{ number_format($monthReceive, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>بستانکار</td>
                                    <td>{{ number_format($totalCredit, 2) }}</td>
                                    <td>{{ number_format($monthCredit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>بدهکار</td>
                                    <td>{{ number_format($totalDebit, 2) }}</td>
                                    <td>{{ number_format($monthDebit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>مصارف</td>
                                    <td>{{ number_format($totalExpense, 2) }}</td>
                                    <td>{{ number_format($monthExpense, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>کمک‌ها</td>
                                    <td>{{ number_format($totalAids, 2) }}</td>
                                    <td>{{ number_format($monthAids, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>اعتبار موجود / باقی قرض</td>
                                    <td>{{ number_format($totalcredit, 2) }}</td>
                                    <td>{{ number_format($remaining_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>بلانس فعلی</td>
                                    <td>{{ number_format($currentBalance, 2) }}</td>
                                    <td>{{ number_format($monthCurrentBalance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <h5>بهترین منابع درآمد</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>نوع درآمد</th>
                                    <th>مبلغ</th>
                                    <th>سهم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incomes as $item)
                                    @php
                                        $percent = $totalIncomeAll > 0 ? ($item->total / $totalIncomeAll) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->category->name ?? '—' }}</td>
                                        <td>{{ number_format($item->total, 2) }}</td>
                                        <td style="min-width: 140px;">
                                            <div class="bar"><span style="width: {{ min($percent, 100) }}%"></span></div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">درآمدی ثبت نشده است</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h5>موجودی حساب‌ها</h5>
                    <span>درآمد منهای مصارف، کمک‌ها و قرض‌ها</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>حساب</th>
                                <th>نوع</th>
                                <th>درآمد واریزی</th>
                                <th>مصارف</th>
                                <th>کمک‌ها</th>
                                <th>قرض‌ها</th>
                                <th>مجموع خروجی</th>
                                <th>موجودی فعلی</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accountSummaries as $account)
                                <tr>
                                    <td>{{ $account['name'] }}</td>
                                    <td>
                                        <span class="tag {{ $account['account_type'] === 'cash' ? 'cash' : 'income' }}">
                                            {{ $account['account_type'] === 'cash' ? 'نقدی' : 'درآمد' }}
                                        </span>
                                    </td>
                                    <td class="text-success">{{ number_format($account['money_in'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($account['expense_out'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($account['aid_out'], 2) }}</td>
                                    <td class="text-danger">{{ number_format($account['loan_out'], 2) }}</td>
                                    <td class="fw-semibold">{{ number_format($account['money_out'], 2) }}</td>
                                    <td class="fw-semibold {{ $account['remaining'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($account['remaining'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">حسابی یافت نشد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
