@extends('admin.dashboard')

@section('admin')

<div class="content">
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">گزارش فعالیت‌ها</h4>
                <small class="text-muted">چه کسی، چه وقت، در کدام بخش، چه تغییری داده است</small>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.activity.logs') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">بخش</label>
                        <select name="section" class="form-select">
                            <option value="">همه بخش‌ها</option>
                            @foreach ($sections as $key => $label)
                                <option value="{{ $key }}" @selected($filters['section'] === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع فعالیت</label>
                        <select name="event" class="form-select">
                            <option value="">همه</option>
                            @foreach ($events as $key => $label)
                                <option value="{{ $key }}" @selected($filters['event'] === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">کاربر</label>
                        <select name="causer" class="form-select">
                            <option value="">همه</option>
                            @foreach ($causers as $causer)
                                <option value="{{ $causer->id }}" @selected($filters['causer'] === $causer->id)>{{ $causer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">از تاریخ</label>
                        <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">تا تاریخ</label>
                        <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100">فیلتر</button>
                    </div>
                    @if (array_filter($filters))
                        <div class="col-12">
                            <a href="{{ route('admin.activity.logs') }}" class="small">پاک کردن فیلترها</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Activity list --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">تاریخچه فعالیت‌ها</h5>
                <small class="text-muted">{{ number_format($activities->total()) }} فعالیت</small>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 150px;">تاریخ و زمان</th>
                                <th class="text-center" style="width: 110px;">نوع</th>
                                <th class="text-center" style="width: 150px;">بخش</th>
                                <th>شرح فعالیت و تغییرات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="text-center">
                                        <div>{{ $row['when']?->format('Y/m/d') }}</div>
                                        <small class="text-muted">{{ $row['when']?->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $row['badge'] }}">{{ $row['event_label'] }}</span>
                                    </td>
                                    <td class="text-center">{{ $row['section'] }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $row['sentence'] }}</div>

                                        @if (count($row['changes']))
                                            <details class="mt-1" @if ($row['event'] === 'updated') open @endif>
                                                <summary class="small text-primary" style="cursor: pointer;">
                                                    @if ($row['event'] === 'updated')
                                                        {{ count($row['changes']) }} مورد تغییر کرد
                                                    @else
                                                        مشخصات رکورد
                                                    @endif
                                                </summary>
                                                <table class="table table-sm table-borderless small mb-0 mt-1">
                                                    @foreach ($row['changes'] as $change)
                                                        <tr>
                                                            <td class="text-muted" style="width: 160px;">{{ $change['field'] }}</td>
                                                            <td>
                                                                @if ($row['event'] === 'updated')
                                                                    <span class="text-danger text-decoration-line-through">{{ $change['old'] }}</span>
                                                                    <span class="mx-1">←</span>
                                                                    <span class="text-success fw-semibold">{{ $change['new'] }}</span>
                                                                @else
                                                                    {{ $change['new'] }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </details>
                                        @elseif ($row['event'] === 'updated')
                                            <div class="small text-muted mt-1">تغییر قابل نمایشی ثبت نشده است.</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">هیچ فعالیتی ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($activities->hasPages())
                    <div class="mt-3">
                        {{ $activities->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
