
<div class="topbar-custom shura-header">
    <div class="container-fluid">
        <div class="shura-header-inner">

            <div class="shura-header-start">
                <button class="button-toggle-menu shura-icon-btn"
                    type="button" aria-label="منو">
                    <i data-feather="menu" class="noti-icon"></i>
                </button>

                <div class="shura-header-title d-none d-md-block">
                    <strong>پنل مدیریت شورا</strong>
                    <small>{{ now()->format('Y/m/d') }}</small>
                </div>
            </div>

            <div class="shura-header-end">

                <button type="button"
                    class="btn shura-icon-btn d-none d-sm-inline-flex"
                    data-toggle="fullscreen" aria-label="تمام صفحه">
                    <i data-feather="maximize"
                        class="align-middle fullscreen noti-icon"></i>
                </button>

                {{-- اعلان‌های پرداخت --}}
                @php
                    $notificationList = $pendingNotifications ?? collect();
                    $latestNotification = $notificationList->first();
                @endphp

                <style>
                    .payment-notification-menu {
                        width: 360px;
                        max-width: calc(100vw - 24px);
                        padding: 0;
                        overflow: hidden;
                        border: 0;
                        border-radius: 14px;
                        box-shadow: 0 8px 28px rgba(0, 0, 0, .14);
                    }

                    .payment-notification-heading {
                        padding: 14px 16px;
                        background: #f8fafc;
                        border-bottom: 1px solid #e9edf2;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    .payment-notification-list {
                        max-height: 320px;
                        overflow-y: auto;
                        padding: 10px 12px;
                    }

                    .payment-notification-card {
                        padding: 13px;
                        border: 1px solid #e9edf2;
                        border-radius: 10px;
                        margin-bottom: 9px;
                        background: #fff;
                    }

                    .payment-notification-name {
                        font-weight: 700;
                        color: #263449;
                        overflow-wrap: anywhere;
                    }

                    .payment-notification-detail {
                        font-size: 12px;
                        color: #718096;
                        margin-top: 5px;
                    }

                    .payment-notification-amount {
                        color: #198754;
                        font-weight: 700;
                        margin-top: 7px;
                    }

                    .payment-notification-actions {
                        padding: 10px 12px;
                        border-top: 1px solid #e9edf2;
                        background: #fff;
                    }

                    .payment-notification-list::-webkit-scrollbar {
                        width: 5px;
                    }

                    .payment-notification-list::-webkit-scrollbar-thumb {
                        background: #cbd5e1;
                        border-radius: 10px;
                    }
                </style>

                <div class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle shura-icon-btn"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        href="#"
                        role="button"
                        aria-haspopup="true"
                        aria-expanded="false">

                        <i data-feather="bell" class="noti-icon"></i>

                        @if (($pendingPaymentCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-circle noti-icon-badge">
                                {{ $pendingPaymentCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end payment-notification-menu">

                        <div class="payment-notification-heading">
                            <strong>اعلان‌های پرداخت</strong>
                            <span class="badge bg-danger">
                                {{ $pendingPaymentCount ?? 0 }} جدید
                            </span>
                        </div>

                        <div class="payment-notification-list">
                            @if ($latestNotification)

                                {{-- جدیدترین اعلان --}}
                                <div class="payment-notification-card">
                                    <div class="small text-primary mb-2">
                                        جدیدترین اعلان
                                    </div>

                                    <div class="payment-notification-name">
                                        {{ $latestNotification->users->name ?? 'عضو نامشخص' }}
                                    </div>

                                    <div class="payment-notification-detail">
                                        نوع پرداخت:
                                        {{ $latestNotification->category->name ?? '-' }}
                                    </div>

                                    <div class="payment-notification-amount">
                                        {{ number_format($latestNotification->amount, 2) }}
                                        افغانی
                                    </div>

                                    <div class="payment-notification-detail mb-3">
                                        تاریخ: {{ $latestNotification->date }}
                                    </div>

                                    <form action="{{ route('unpaid.payments.mark.reviewed') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_id"
                                            value="{{ $latestNotification->id }}">

                                        <button type="submit"
                                            class="btn btn-sm btn-primary w-100">
                                            بررسی شد
                                        </button>
                                    </form>
                                </div>

                                {{-- سایر اعلان‌ها --}}
                                @if ($notificationList->count() > 1)
                                    <div class="collapse" id="allPaymentNotifications">
                                        @foreach ($notificationList->skip(1) as $payment)
                                            <div class="payment-notification-card">
                                                <div class="payment-notification-name">
                                                    {{ $payment->users->name ?? 'عضو نامشخص' }}
                                                </div>

                                                <div class="payment-notification-detail">
                                                    نوع پرداخت:
                                                    {{ $payment->category->name ?? '-' }}
                                                </div>

                                                <div class="payment-notification-amount">
                                                    {{ number_format($payment->amount, 2) }}
                                                    افغانی
                                                </div>

                                                <div class="payment-notification-detail mb-3">
                                                    تاریخ: {{ $payment->date }}
                                                </div>

                                                <form action="{{ route('unpaid.payments.mark.reviewed') }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="payment_id"
                                                        value="{{ $payment->id }}">

                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-primary w-100">
                                                        بررسی شد
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            @else
                                <div class="text-center text-muted py-4">
                                    اعلان جدیدی وجود ندارد
                                </div>
                            @endif
                        </div>

                        @if ($notificationList->count() > 1)
                            <div class="payment-notification-actions">
                                <button type="button"
                                    class="btn btn-light btn-sm w-100"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#allPaymentNotifications"
                                    aria-expanded="false"
                                    aria-controls="allPaymentNotifications"
                                    onclick="this.innerText =
                                        this.getAttribute('aria-expanded') === 'true'
                                        ? 'بستن اعلان ها'
                                        : 'نمایش همه اعلان ها';">
                                    نمایش همه اعلان‌ها ({{ $notificationList->count() }})
                                </button>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- پروفایل کاربر --}}
                @php
                    $profileData = Auth::user();
                @endphp

                <div class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle shura-user-chip"
                        data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true"
                        aria-expanded="false">

                        <img src="{{ !empty($profileData->photo)
                            ? url('upload/user_images/' . $profileData->photo)
                            : url('upload/no_image.jpg') }}"
                            alt="user-image" class="rounded-circle">

                        <span class="pro-user-name">
                            {{ $profileData->name }}
                            <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end profile-dropdown shura-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">خوش آمدید</h6>
                        </div>

                        <a href="{{ route('admin.profile') }}"
                            class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>اکانت من</span>
                        </a>

                        <a href="{{ route('admin.change.password') }}"
                            class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>تغییر رمز</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('admin.logout') }}"
                            class="dropdown-item notify-item">
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>خروج</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
