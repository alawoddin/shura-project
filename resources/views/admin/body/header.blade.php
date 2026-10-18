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
                          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="{{ route('unpaid.payments') }}" role="button"
                              aria-haspopup="false" aria-expanded="false">
                              <i data-feather="bell" class="noti-icon"></i>
                              @if (($pendingPaymentCount ?? 0) > 0)
                                  <span class="badge bg-danger rounded-circle noti-icon-badge">{{ $pendingPaymentCount }}</span>
                              @endif
                          </a>
                          <div class="dropdown-menu dropdown-menu-end dropdown-lg">

                              <div class="dropdown-item noti-title">
                                  <h5 class="m-0">
                                      <span class="float-end">
                                          <a href="{{ route('unpaid.payments') }}" class="text-dark">
                                              <small>مشاهده همه</small>
                                          </a>
                                      </span>پرداخت‌های جدید
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
                                <a href="{{ route('admin.profile') }}" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                                    <span>اکانت من</span>
                                </a>

                                <!-- item-->
                                <a href="{{ route('admin.change.password') }}" class="dropdown-item notify-item">
                                    <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                                    <span>تغییر رمز </span>
                                </a>


                                <div class="dropdown-divider"></div>

                                <!-- item-->
                                <a href="{{ route('admin.logout') }}" class="dropdown-item notify-item">
                                    <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                                    <span>خروج</span>
                                </a>

                            </div>
                        </li>

                  </ul>
              </div>

          </div>

      </div>
