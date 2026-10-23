
@extends('admin.dashboard')
@section('admin')

<div class="content">
    <div class="container-fluid my-0">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">گزارش فعالیت‌ها</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">تاریخچه فعالیت کاربران</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-bordered dt-responsive nowrap" id="datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">شماره</th>
                                        <th class="text-center">کاربر</th>
                                        <th class="text-center">شرح فعالیت</th>
                                        <th class="text-center">بخش مربوطه</th>
                                        <th class="text-center">شناسه رکورد</th>
                                        <th class="text-center">تاریخ و زمان</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($activities as $key => $activity)
                                        <tr>
                                            <td class="text-center">
                                                {{ $activities->firstItem() + $key }}
                                            </td>

                                            <td class="text-center">
                                                {{ $activity->causer?->name ?? 'سیستم' }}
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $actions = [
                                                        'created' => 'ایجاد رکورد جدید',
                                                        'updated' => 'ویرایش رکورد',
                                                        'deleted' => 'حذف رکورد',
                                                    ];
                                                @endphp

                                                {{ $actions[$activity->description] ?? $activity->description }}
                                            </td>

                                            <td class="text-center">
                                                {{ $activity->subject_type
                                                    ? class_basename($activity->subject_type)
                                                    : 'عمومی' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $activity->subject_id ?? '—' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $activity->created_at?->format('Y/m/d H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                هیچ فعالیتی ثبت نشده است.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>

                        {{-- <div class="mt-3">
                            {{ $activities->links() }}
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
