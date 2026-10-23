@php
    $profileData = Auth::user();
@endphp

<div class="topbar-custom client-topbar">
    <div class="container-fluid">
        <div class="client-topbar-inner">
            <div class="client-topbar-start">
                <button class="button-toggle-menu client-top-btn" type="button" aria-label="منو">
                    <i data-feather="menu" class="noti-icon"></i>
                </button>
                <div class="client-topbar-copy d-none d-md-block">
                    <strong>داشبورد عضو</strong>
                    <small>سیستم مدیریت شورا · {{ now()->format('Y/m/d') }}</small>
                </div>
            </div>

            <div class="client-topbar-end">
                <a href="{{ route('user.profile') }}" class="client-top-link d-none d-sm-inline-flex">پروفایل</a>
                <button type="button" class="btn client-top-btn d-none d-sm-inline-flex" data-toggle="fullscreen" aria-label="تمام صفحه">
                    <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                </button>

                <div class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle client-user-chip" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ !empty($profileData->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
                            alt="user-image" class="rounded-circle">
                        <span>
                            {{ $profileData->name }}
                            <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown client-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">خوش آمدید</h6>
                        </div>
                        <a href="{{ route('user.profile') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>اکانت من</span>
                        </a>
                        <a href="{{ route('user.change.password') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>تغییر رمز</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('client.logout') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>خروج</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
