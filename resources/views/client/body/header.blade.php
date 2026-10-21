<div class="topbar-custom shura-header">
    <div class="container-fluid">
        <div class="shura-header-inner">
            <div class="shura-header-start">
                <button class="button-toggle-menu shura-icon-btn" type="button" aria-label="منو">
                    <i data-feather="menu" class="noti-icon"></i>
                </button>
                <div class="shura-header-title d-none d-md-block">
                    <strong>پنل عضو شورا</strong>
                    <small>{{ now()->format('Y/m/d') }}</small>
                </div>
            </div>

            <div class="shura-header-end">
                <button type="button" class="btn shura-icon-btn d-none d-sm-inline-flex" data-toggle="fullscreen" aria-label="تمام صفحه">
                    <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                </button>

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
