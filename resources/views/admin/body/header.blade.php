<div class="topbar-custom shura-header">
    <div class="container-fluid">
        <div class="shura-header-inner">
            <div class="shura-header-start">
                <button class="button-toggle-menu shura-icon-btn" type="button" aria-label="منو">
                    <i data-feather="menu" class="noti-icon"></i>
                </button>
                <div class="shura-header-title d-none d-md-block">
                    <strong>پنل مدیریت شورا</strong>
                    <small>{{ now()->format('Y/m/d') }}</small>
                </div>
            </div>

            <div class="shura-header-end">
                <button type="button" class="btn shura-icon-btn d-none d-sm-inline-flex" data-toggle="fullscreen" aria-label="تمام صفحه">
                    <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                </button>

                <div class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle shura-icon-btn" data-bs-toggle="dropdown" href="{{ route('unpaid.payments') }}" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <i data-feather="bell" class="noti-icon"></i>
                        @if (($pendingPaymentCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-circle noti-icon-badge">{{ $pendingPaymentCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg shura-dropdown">
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-end">
                                    <a href="{{ route('unpaid.payments') }}" class="text-dark">
                                        <small>مشاهده همه</small>
                                    </a>
                                </span>
                                پرداخت‌های جدید
                            </h5>
                        </div>

                        @if (($pendingPaymentCount ?? 0) > 0)
                            <div class="dropdown-item text-muted">
                                {{ $pendingPaymentCount }} پرداخت جدید نیاز به بررسی دارد
                            </div>
                        @else
                            <div class="dropdown-item text-muted">
                                اعلان جدیدی وجود ندارد
                            </div>
                        @endif
                    </div>
                </div>

                @php
                    $profileData = Auth::user();
                @endphp

                <div class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle shura-user-chip" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ !empty($profileData->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
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
                        <a href="{{ route('admin.profile') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>اکانت من</span>
                        </a>
                        <a href="{{ route('admin.change.password') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>تغییر رمز</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('admin.logout') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>خروج</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
