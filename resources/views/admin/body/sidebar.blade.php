<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt="" height="24">
                    </span>
                </a>
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                {{-- ========================================================= --}}
                {{-- 1. داشبورد --}}
                {{-- ========================================================= --}}
                <li class="menu-title">خانه</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> داشبورد </span>
                    </a>
                </li>


                {{-- ========================================================= --}}
                {{-- 2. تنظیمات پایه --}}
                {{-- ========================================================= --}}
                <li class="menu-title">تنظیمات پایه</li>

                @if (Auth::guard('web')->user()->can('all.category'))
                    <li>
                        <a href="#catalog" data-bs-toggle="collapse">
                            <i data-feather="grid"></i>
                            <span> مدیریت بخش ها </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse" id="catalog">
                            <ul class="nav-second-level">

                                <li>
                                    <a href="{{ route('all.category') }}" class="tp-link">
                                        <span> نام درامد ها </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.ethnic') }}" class="tp-link">
                                        <span> شاخه های قومی </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.representatives') }}" class="tp-link">
                                        <span> نماینده‌ها </span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif


                {{-- ========================================================= --}}
                {{-- 3. اعضا و افراد --}}
                {{-- ========================================================= --}}
                <li class="menu-title">اعضا و افراد</li>

                @if (Auth::guard('web')->user()->can('all.users'))
                    <li>
                        <a href="{{ route('all.users') }}" class="tp-link">
                            <i data-feather="users"></i>
                            <span> کاربران </span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('all.key.people') }}" class="tp-link">
                        <i data-feather="award"></i>
                        <span> افراد کلیدی </span>
                    </a>
                </li>


                {{-- ========================================================= --}}
                {{-- 4. امور مالی --}}
                {{-- ========================================================= --}}
                <li class="menu-title">امور مالی</li>

                @if (Auth::guard('web')->user()->can('all.income'))
                    <li>
                        <a href="{{ route('all.income') }}" class="tp-link">
                            <i data-feather="trending-up"></i>
                            <span> درامد ها </span>
                        </a>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('all.undeposited'))
                    <li>
                        <a href="{{ route('undeposited.income') }}" class="tp-link">
                            <i data-feather="clock"></i>
                            <span> واریز نشده </span>
                        </a>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('all.recieve.payment'))
                    <li>
                        <a href="{{ route('all.receive.payment') }}" class="tp-link">
                            <i data-feather="credit-card"></i>
                            <span> دریافت پرداخت </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('unpaid.payments') }}" class="tp-link">
                            <i data-feather="alert-circle"></i>
                            <span> پرداخت‌های پرداخت نشده </span>
                        </a>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('all.financial.report'))
                    <li>
                        <a href="{{ route('all.member.financial.report') }}" class="tp-link">
                            <i data-feather="file-text"></i>
                            <span> گزارش مالی اعضا </span>
                        </a>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('all.credits'))
                    <li>
                        <a href="{{ route('all.credits') }}" class="tp-link">
                            <i data-feather="dollar-sign"></i>
                            <span> کریدت ها </span>
                        </a>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('all.aid'))
                    <li>
                        <a href="{{ route('all.aid') }}" class="tp-link">
                            <i data-feather="heart"></i>
                            <span> کمک ها </span>
                        </a>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('all.expense'))
                    <li>
                        <a href="{{ route('all.expense') }}" class="tp-link">
                            <i data-feather="trending-down"></i>
                            <span> مصارف </span>
                        </a>
                    </li>
                @endif


                {{-- ========================================================= --}}
                {{-- 5. گزارشات --}}
                {{-- ========================================================= --}}
                @if (Auth::guard('web')->user()->can('all.report'))
                    <li class="menu-title">گزارشات</li>

                    <li>
                        <a href="#Reports" data-bs-toggle="collapse">
                            <i data-feather="bar-chart-2"></i>
                            <span> گزارشات </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse" id="Reports">
                            <ul class="nav-second-level">

                                <li>
                                    <a href="{{ route('all.report') }}" class="tp-link">
                                        <span> تمام گزارشات </span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif


                {{-- ========================================================= --}}
                {{-- 6. مدیریت سیستم --}}
                {{-- ========================================================= --}}
                @if (Auth::guard('web')->user()->canManageAccess())

                    <li class="menu-title">مدیریت سیستم</li>

                    {{-- نقش ها و مجوزها --}}
                    <li>
                        <a href="#Role" data-bs-toggle="collapse">
                            <i data-feather="shield"></i>
                            <span> مجوز ها و نقش ها </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse" id="Role">
                            <ul class="nav-second-level">

                                <li>
                                    <a href="{{ route('all.permission') }}" class="tp-link">
                                        مجوز ها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.roles') }}" class="tp-link">
                                        نقش ها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('add.roles.permission') }}" class="tp-link">
                                        نقش در مجوز ها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.roles.permission') }}" class="tp-link">
                                        تمام نقش ها در مجوز ها
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>


                    {{-- مدیریت ادمین ها --}}
                    <li>
                        <a href="#sidebarBaseui" data-bs-toggle="collapse">
                            <i data-feather="settings"></i>
                            <span> مدیریت ادمین ها </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse" id="sidebarBaseui">
                            <ul class="nav-second-level">

                                <li>
                                    <a href="{{ route('all.admin') }}" class="tp-link">
                                        همه ادمین ها
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>

                @endif

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
