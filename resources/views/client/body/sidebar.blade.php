@php
    $clientUser = Auth::user();
@endphp

<div class="app-sidebar-menu client-sidebar">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            <div class="logo-box client-logo-box">
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm client-brand-mark">ش</span>
                    <span class="logo-lg client-brand-text">شورا</span>
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm client-brand-mark">ش</span>
                    <span class="logo-lg client-brand-text">شورا</span>
                </a>
            </div>

            <div class="client-side-profile">
                <img src="{{ !empty($clientUser->photo) ? url('upload/user_images/' . $clientUser->photo) : url('upload/no_image.jpg') }}"
                    alt="profile">
                <div>
                    <strong>{{ $clientUser->name }}</strong>
                    <small>{{ $clientUser->member_type ?? 'عضو' }}</small>
                </div>
            </div>

            <ul id="side-menu">
                <li class="menu-title">خانه</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> داشبورد </span>
                    </a>
                </li>

                <li class="menu-title">حساب من</li>
                <li>
                    <a href="{{ route('user.profile') }}" class="tp-link">
                        <i data-feather="user"></i>
                        <span> پروفایل من </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.change.password') }}" class="tp-link">
                        <i data-feather="lock"></i>
                        <span> تغییر رمز </span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
