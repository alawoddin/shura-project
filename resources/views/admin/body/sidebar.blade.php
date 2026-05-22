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
                    <a href="{{ route('all.category') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> Category </span>
                    </a>
                 </li>

                 
                    <li>
                    <a href="{{ route('all.income') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> Income </span>
                    </a>
                 </li>

                   <li>
                    <a href="{{ route('all.expense') }}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> Expense </span>
                    </a>
                 </li>


            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
