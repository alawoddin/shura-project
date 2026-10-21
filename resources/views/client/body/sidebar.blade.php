<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            <div class="logo-box">
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm shura-brand">ش</span>
                    <span class="logo-lg shura-brand">شورا</span>
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm shura-brand">ش</span>
                    <span class="logo-lg shura-brand">شورا</span>
                </a>
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
