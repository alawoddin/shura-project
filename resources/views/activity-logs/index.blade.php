@extends('admin.dashboard')

@section('admin')

<div class="content">
    <div class="container-fluid my-0">

        {{-- عنوان صفحه --}}
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">گزارش فعالیت‌ها</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    {{-- سربرگ کارت --}}
                    <div class="card-header">
                        <h5 class="card-title mb-0">تاریخچه فعالیت کاربران</h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
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

                                        @php
                                            /*
                                             * ترجمه نوع فعالیت
                                             */
                                            $actions = [
                                                'created' => 'ایجاد رکورد جدید',
                                                'updated' => 'ویرایش رکورد',
                                                'deleted' => 'حذف رکورد',
                                            ];

                                            /*
                                             * ترجمه نام مدل‌ها و بخش‌های سیستم
                                             * کلیدها باید با نام کلاس مدل‌ها مطابقت داشته باشند.
                                             */
                                            $models = [
                                                'User' => 'کاربران',
                                                'Category' => 'دسته‌بندی‌ها',
                                                'Income' => 'درآمدها',
                                                'Expense' => 'مصارف',
                                                'ReceivePayment' => 'دریافت پرداخت‌ها',
                                                'Payment' => 'پرداخت‌ها',
                                                'Customer' => 'مشتریان',
                                                'Supplier' => 'تأمین‌کنندگان',
                                                'Product' => 'محصولات',
                                                'Order' => 'سفارش‌ها',
                                                'Role' => 'نقش‌های کاربری',
                                                'Permission' => 'مجوزهای دسترسی',
                                            ];

                                            /*
                                             * استخراج نام کوتاه مدل
                                             */
                                            $modelName = $activity->subject_type
                                                ? class_basename($activity->subject_type)
                                                : null;

                                            /*
                                             * تعیین نام فارسی بخش
                                             */
                                            $sectionName = $modelName
                                                ? ($models[$modelName] ?? 'سایر بخش‌ها')
                                                : 'فعالیت عمومی';

                                            /*
                                             * تعیین شرح فعالیت
                                             */
                                            $actionName = $actions[$activity->description]
                                                ?? $activity->description
                                                ?? 'فعالیت نامشخص';

                                            /*
                                             * رنگ نشانگر نوع فعالیت
                                             */
                                            $badgeClass = match ($activity->description) {
                                                'created' => 'bg-success',
                                                'updated' => 'bg-warning text-dark',
                                                'deleted' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp

                                        <tr>
                                            {{-- شماره ردیف --}}
                                            <td class="text-center">
                                                {{ $activities->firstItem() + $key }}
                                            </td>

                                            {{-- کاربر انجام‌دهنده --}}
                                            <td class="text-center">
                                                {{ $activity->causer?->name ?? 'سیستم' }}
                                            </td>

                                            {{-- شرح فعالیت --}}
                                            <td class="text-center">
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $actionName }}
                                                </span>
                                            </td>

                                            {{-- بخش مربوطه --}}
                                            <td class="text-center">
                                                {{ $sectionName }}
                                            </td>

                                            {{-- شناسه رکورد --}}
                                            <td class="text-center">
                                                {{ $activity->subject_id ?? '—' }}
                                            </td>

                                            {{-- تاریخ و زمان --}}
                                            <td class="text-center">
                                                {{ $activity->created_at?->format('Y/m/d H:i') ?? '—' }}
                                            </td>
                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                هیچ فعالیتی ثبت نشده است.
                                            </td>
                                        </tr>

                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- صفحه‌بندی --}}
                        {{-- @if ($activities->hasPages())
                            <div class="mt-3">
                                {{ $activities->links() }}
                            </div>
                        @endif --}}

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection