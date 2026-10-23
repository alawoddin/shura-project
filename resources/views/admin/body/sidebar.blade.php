
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">

            {{-- Logo --}}
            <div class="logo-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm shura-brand">ش</span>
                    <span class="logo-lg shura-brand">شورا</span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm shura-brand">ش</span>
                    <span class="logo-lg shura-brand">شورا</span>
                </a>
            </div>

            @php
                $adminUser = Auth::guard('web')->user();

                $isReportRoute = request()->routeIs(
                    'all.report',
                    'all.member.financial.report'
                );

                $isSettingsRoute = request()->routeIs(
                    'all.category',
                    'all.ethnic',
                    'all.representatives'
                );

                $isAccessRoute = request()->routeIs(
                    'all.permission',
                    'all.roles',
                    'add.roles.permission',
                    'all.roles.permission',
                    'all.admin'
                );
            @endphp

            <ul id="side-menu">

                {{-- 1. Dashboard --}}

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="tp-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-feather="home"></i>
                        <span>داشبورد</span>
                    </a>
                </li>


                {{-- 2. Members --}}

                @if ($adminUser->can('all.users'))
                    <li>
                        <a href="{{ route('all.users') }}"
                           class="tp-link {{ request()->routeIs('all.users') ? 'active' : '' }}">
                            <i data-feather="users"></i>
                            <span>کاربران</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('all.key.people') }}"
                       class="tp-link {{ request()->routeIs('all.key.people') ? 'active' : '' }}">
                        <i data-feather="award"></i>
                        <span>افراد کلیدی</span>
                    </a>
                </li>


                {{-- 3. Finance --}}

                @if ($adminUser->can('all.income'))
                    <li>
                        <a href="{{ route('add.income') }}"
                           class="tp-link {{ request()->routeIs('all.income') ? 'active' : '' }}">
                            <i data-feather="trending-up"></i>
                            <span>درآمدها</span>
                        </a>
                    </li>
                @endif

                @if ($adminUser->can('all.recieve.payment'))
                    <li>
                        <a href="{{ route('all.receive.payment') }}"
                           class="tp-link {{ request()->routeIs('all.receive.payment') ? 'active' : '' }}">
                            <i data-feather="credit-card"></i>
                            <span>دریافت پول</span>
                        </a>
                    </li>
                @endif

                @if ($adminUser->can('all.undeposited'))
                    <li>
                        <a href="{{ route('undeposited.income') }}"
                           class="tp-link {{ request()->routeIs('undeposited.income') ? 'active' : '' }}">
                            <i data-feather="clock"></i>
                            <span>واریز نشده</span>
                        </a>
                    </li>
                @endif

                @if ($adminUser->can('all.expense'))
                    <li>
                        <a href="{{ route('all.expense') }}"
                           class="tp-link {{ request()->routeIs('all.expense') ? 'active' : '' }}">
                            <i data-feather="trending-down"></i>
                            <span>مصارف</span>
                        </a>
                    </li>
                @endif

                @if ($adminUser->can('all.aid'))
                    <li>
                        <a href="{{ route('all.aid') }}"
                           class="tp-link {{ request()->routeIs('all.aid') ? 'active' : '' }}">
                            <i data-feather="heart"></i>
                            <span>کمک‌ها</span>
                        </a>
                    </li>
                @endif

                @if ($adminUser->can('all.credits'))
                    <li>
                        <a href="{{ route('all.credits') }}"
                           class="tp-link {{ request()->routeIs('all.credits') ? 'active' : '' }}">
                            <i data-feather="dollar-sign"></i>
                            <span>کریدت‌ها</span>
                        </a>
                    </li>
                @endif


                {{-- 4. Reports --}}
                @if (
                    $adminUser->can('all.financial.report') ||
                    $adminUser->can('all.report')
                )

                    <li>
                        <a href="#sidebarReports"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isReportRoute ? 'true' : 'false' }}"
                           class="{{ $isReportRoute ? '' : 'collapsed' }}">
                            <i data-feather="bar-chart-2"></i>
                            <span>گزارش‌ها</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div id="sidebarReports"
                             class="collapse {{ $isReportRoute ? 'show' : '' }}">
                            <ul class="nav-second-level">

                                @if ($adminUser->can('all.financial.report'))
                                    <li>
                                        <a href="{{ route('all.member.financial.report') }}"
                                           class="tp-link {{ request()->routeIs('all.member.financial.report') ? 'active' : '' }}">
                                            گزارش مالی اعضا
                                        </a>
                                    </li>
                                @endif

                                @if ($adminUser->can('all.report'))
                                    <li>
                                        <a href="{{ route('all.report') }}"
                                           class="tp-link {{ request()->routeIs('all.report') ? 'active' : '' }}">
                                            تمام گزارش‌ها
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </li>
                @endif


                {{-- 5. Basic Settings --}}
                @if ($adminUser->can('all.category'))

                    <li>
                        <a href="#sidebarSettings"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isSettingsRoute ? 'true' : 'false' }}"
                           class="{{ $isSettingsRoute ? '' : 'collapsed' }}">
                            <i data-feather="grid"></i>
                            <span>مدیریت بخش‌ها</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div id="sidebarSettings"
                             class="collapse {{ $isSettingsRoute ? 'show' : '' }}">
                            <ul class="nav-second-level">

                                <li>
                                    <a href="{{ route('all.category') }}"
                                       class="tp-link {{ request()->routeIs('all.category') ? 'active' : '' }}">
                                        نام درآمدها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.ethnic') }}"
                                       class="tp-link {{ request()->routeIs('all.ethnic') ? 'active' : '' }}">
                                        شاخه‌های قومی
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.representatives') }}"
                                       class="tp-link {{ request()->routeIs('all.representatives') ? 'active' : '' }}">
                                        نماینده‌ها
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif


                {{-- 6. System Management --}}
                @if ($adminUser->canManageAccess())

                    <li>
                        <a href="#sidebarAccess"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isAccessRoute ? 'true' : 'false' }}"
                           class="{{ $isAccessRoute ? '' : 'collapsed' }}">
                            <i data-feather="shield"></i>
                            <span>نقش‌ها و دسترسی‌ها</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div id="sidebarAccess"
                             class="collapse {{ $isAccessRoute ? 'show' : '' }}">
                            <ul class="nav-second-level">

                                <li>
                                    <a href="{{ route('all.permission') }}"
                                       class="tp-link {{ request()->routeIs('all.permission') ? 'active' : '' }}">
                                        مجوزها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.roles') }}"
                                       class="tp-link {{ request()->routeIs('all.roles') ? 'active' : '' }}">
                                        نقش‌ها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('add.roles.permission') }}"
                                       class="tp-link {{ request()->routeIs('add.roles.permission') ? 'active' : '' }}">
                                        اختصاص مجوز به نقش
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.roles.permission') }}"
                                       class="tp-link {{ request()->routeIs('all.roles.permission') ? 'active' : '' }}">
                                        نقش‌ها و مجوزها
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('all.admin') }}"
                                       class="tp-link {{ request()->routeIs('all.admin') ? 'active' : '' }}">
                                        مدیریت ادمین‌ها
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif


                {{-- 7. Support and Maintenance --}}

                <li>
                    <a href="{{ route('admin.backup') }}"
                       class="tp-link {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                        <i data-feather="database"></i>
                        <span>پشتیبان‌گیری</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.activity.logs') }}"
                       class="tp-link {{ request()->routeIs('admin.activity.logs') ? 'active' : '' }}">
                        <i data-feather="activity"></i>
                        <span>گزارش فعالیت‌ها</span>
                    </a>
                </li>

            </ul>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
