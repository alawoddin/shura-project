      <div class="topbar-custom">
          <div class="container-fluid">
              <div class="d-flex justify-content-between">
                  <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                      <li>
                          <button class="button-toggle-menu nav-link ps-0">
                              <i data-feather="menu" class="noti-icon"></i>
                          </button>
                      </li>
                      <li class="d-none d-lg-block"></li>
                  </ul>

                  <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                      <li class="d-none d-sm-flex">
                          <button type="button" class="btn nav-link" data-toggle="fullscreen">
                              <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                          </button>
                      </li>

                      <li class="dropdown notification-list topbar-dropdown">
                          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                              aria-haspopup="false" aria-expanded="false">
                              <i data-feather="bell" class="noti-icon"></i>
                              <span class="badge bg-danger rounded-circle noti-icon-badge">9</span>
                          </a>
                          <div class="dropdown-menu dropdown-menu-end dropdown-lg">

                              <!-- item-->
                              <div class="dropdown-item noti-title">
                                  <h5 class="m-0">
                                      <span class="float-end">
                                          <a href="" class="text-dark">
                                              <small>حذف همه</small>
                                          </a>
                                      </span>اطلاع رسانی
                                  </h5>
                              </div>


                              <!-- All-->
                              <a href="javascript:void(0);"
                                  class="dropdown-item text-center text-primary notify-item notify-all">
                                  مشاهده همه
                                  <i class="fe-arrow-right"></i>
                              </a>

                          </div>
                      </li>

                      @php
        $profileData = Auth::user();
    @endphp  

                      <li class="dropdown notification-list topbar-dropdown">
                          <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#"
                              role="button" aria-haspopup="false" aria-expanded="false">
                              <img src="{{ !empty($profileData->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
                                  alt="user-image" class="rounded-circle">
                              <span class="pro-user-name ms-1">
                                  {{ $profileData->name }} <i class="mdi mdi-chevron-down"></i>
                              </span>
                          </a>
                          <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                              <!-- item-->
                              <div class="dropdown-header noti-title">
                                  <h6 class="text-overflow m-0">خوش آمدید</h6>
                              </div>

                              <!-- item-->
                              <a href="{{ route('user.profile') }}" class="dropdown-item notify-item">
                                  <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                                  <span>اکانت من</span>
                              </a>

                              <!-- item-->
                              <a href="{{ route('user.change.password') }}" class="dropdown-item notify-item">
                                  <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                                  <span>تغییر رمز</span>
                              </a>


                              <div class="dropdown-divider"></div>

                              <!-- item-->
                              <a href="{{ route('client.logout') }}" class="dropdown-item notify-item">
                                  <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                                  <span>خروج</span>
                              </a>

                          </div>
                      </li>

                  </ul>
              </div>

          </div>

      </div>
