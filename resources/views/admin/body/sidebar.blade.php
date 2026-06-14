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

                <li class="menu-title">خانه</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> داشبورد </span>
                    </a>
                </li>

                <li class="menu-title">مینیو</li>

                <li>
                    <a href="{{ route('all.users') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> کاربران </span>
                    </a>
                </li>  
                
             

                 {{-- <li>
                    <a href="{{ route('all.users.family') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> اعضای فامیل </span>
                    </a>
                 </li> --}}

                 <li>
                    <a href="#catalog" data-bs-toggle="collapse">
                        <span> مدیریت بخش ها </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="catalog">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('all.category') }}" class="tp-link">
                                    <span>  نام درامد ها  </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                 
                    <li>
                    <a href="{{ route('all.income') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> درامد ها  </span>
                    </a>
                 </li>

                      <li>
                    <a href="{{ route('undeposited.income') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span>  واریز نشده  </span>
                    </a>
                 </li>

                      <li>
                    <a href="{{ route('all.receive.payment') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span>  دریافت پرداخت  </span>
                    </a>
                 </li>


                 <li>
                    <a href="{{ route('all.member.financial.report') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> گزارش مالی اعضا </span>
                    </a>
                 </li>

                       <li>
                    <a href="{{ route('all.credits') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span>   کریدت ها  </span>
                    </a>
                 </li>

                <li>
                    <a href="{{ route('all.aid') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span>   کمک ها  </span>
                    </a>
                </li>
                 

                   <li>
                    <a href="{{ route('all.expense') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> مصارف </span>
                    </a>
                 </li>

                     <li>
                    <a href="#Reports" data-bs-toggle="collapse">
                        <span> Reports </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Reports">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('all.report') }}" class="tp-link">
                                    <span>  All Reports  </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                      <li>
                    <a href="#Role" data-bs-toggle="collapse">
                        <span> Role & Permission </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Role">
                        <ul class="nav-second-level">
    <li>
        <a href="{{ route('all.permission') }}" class="tp-link">All Permission</a>
    </li>
    <li>
        <a href="{{ route('all.roles') }}" class="tp-link">All Roles</a>
    </li>

    <li>
        <a href="{{ route('add.roles.permission') }}" class="tp-link">Role In Permission</a>
    </li>
    
</ul>

                        
                    </div>
                </li>



            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
