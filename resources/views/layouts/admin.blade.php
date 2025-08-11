<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="session-timeout" content="{{ config('session.timeout', 15) * 60000 }}">
  <meta name="auth-user-id" content="{{ Auth::id() }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
  <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.cluster') }}">

  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">

  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

  <title>Wri Education Consultancy</title>
  <link rel="stylesheet" href="{{ asset('assets/libs/jvectormap/jquery-jvectormap.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/tabler-icons.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
  #main-wrapper.show-sidebar .left-sidebar.with-vertical .brand-logo .ww {
    display: none;
  }

  #main-wrapper.show-sidebar .left-sidebar.with-vertical .brand-logo .logo-img .dark-logo {
    height: 40px;
    width: 30px;
  }

  #main-wrapper.show-sidebar .left-sidebar.with-vertical .brand-logo .logo-img .light-logo {
    height: 40px;
    width: 30px;
  }

  #main-wrapper .left-sidebar.with-vertical .brand-logo .logo-img .dark-logo {
    height: 55px;
    width: 50px;
  }

  #main-wrapper .left-sidebar.with-vertical .brand-logo .logo-img .light-logo {
    height: 70px;
    width: 60px;
  }

  #notificationDropdown:focus,
  #notificationDropdown:focus-visible {
    outline: none !important;
    box-shadow: none !important;
  }

  .badge1 {
    position: absolute;
    top: 20px;
    right: -4px;
    background-color: #dc3545 !important;
    color: white;
    border-radius: 50%;
    font-size: 10px;
    min-width: 16px;
    height: 16px;
    line-height: 16px;
    text-align: center;
  }
</style>

<body>

  <div id="main-wrapper">
    <aside class="left-sidebar with-vertical">
      <div class="brand-logo d-flex align-items-center gap-2 pt-2">
        <a href="index.html" class="text-nowrap logo-img">
          <img src="{{ asset('img/wri.png') }}" class="dark-logo" width="40" height="40" alt="Logo-Dark">
          <img src="{{ asset('img/wri.png') }}" class="light-logo" width="40" height="40" alt="Logo-light">
        </a>
        <div class="ww">WRI Education </br> Consultancy</div>

        <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
          <i class="ti ti-x"></i>
        </a>
      </div>

      <div class="scroll-sidebar" data-simplebar="">
        <nav class="sidebar-nav">
          <ul id="sidebarnav" class="mb-0">

            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('dashboard') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-tachometer-alt fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Dashboard</span>
              </a>
            </li>

            @can('view_applications')
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Application</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="/app/applications" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-list-ul fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Application History</span>
              </a>
            </li>
            @endcan
            @can('view_leads')
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Leads</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.leadform.indexs') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-users-cog fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Leads Table</span>
              </a>
            </li>
            @endcan

            @if (auth()->user()->roles->pluck('name')->intersect(['Administrator', 'Manager', 'Leads Manager'])->isNotEmpty())
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Raw Leads</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.leadform.rawlead') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-users-cog fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Raw Leads</span>
              </a>
            </li>
            @endif

            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Course Finder</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.notice.searchcourse') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-money-bill-wave fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Course Finder</span>
              </a>
            </li>

            @can('view_enquiries')
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Enquiry</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.enquiryhistory.indexs') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-comments fs-6"></i>
                  <span class="text-muted">
                  </span>
                </span>
                <span class="hide-menu ps-1">Enquiry list</span>
              </a>
            </li>
            @endcan

            @if(auth()->user()->roles->isNotEmpty())
            <li class="nav-small-cap">
              <i class="fas fa-home nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Notice</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.notice.index') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-exclamation-triangle fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Notice</span>
              </a>
            </li>
            @endif

            @can('view_data_entries')
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">University</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.dataentrys.indexs') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-list-ol fs-6"></i>
                </span>
                <span class="hide-menu ps-1">University Table</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.dataentry.create') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-plus-square fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Add University</span>
              </a>
            </li>
            @endcan

            @can('view_finances')
            <li class="nav-small-cap">
              <i class="fas fa-file-alt nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Finance</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.Finance.index') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-money-bill-wave fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Finance Table</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('backend.Finance.create') }}" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <i class="fas fa-wallet fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Add Finance</span>
              </a>
            </li>
            @endcan

            @if (auth()->user()->roles->pluck('name')->intersect(['Administrator', 'Manager', 'Leads Manager', 'Applications Manager'])->isNotEmpty())

            <li class="nav-small-cap">
              <i class="fas fa-cog nav-small-cap-icon fs-5"></i>
              <span class="hide-menu">Settings</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow indigo-hover-bg" href="javascript:void(0)" aria-expanded="false">
                <span class="aside-icon p-2 bg-indigo-subtle rounded-1">
                  <i class="fas fa-tools fs-6"></i>
                </span>
                <span class="hide-menu ps-1">Content Management</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="{{ route('backend.notice.index') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Add Notice</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="{{ route('backend.application.indexcomments') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Add Comment</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="{{ route('backend.document.index') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Add Document Status</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="{{ route('backend.product.index') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Add Product Status</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="{{ route('backend.upload.index') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Student Status</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="{{ route('backend.name.index') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Add Counselor</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="{{ route('backend.dataentry.universities') }}" class="sidebar-link">
                    <span class="sidebar-icon"></span>
                    <span class="hide-menu">Add University Image</span>
                  </a>
                </li>
              </ul>
            </li>

          </ul>
        </nav>
        @endif
      </div>

      <div class="fixed-profile mx-3 mt-1">
        <div class="card bg-primary-subtle mb-0 shadow-none">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : asset('assets/images/profile/user-1.jpg') }}" width="45" height="45" class="img-fluid rounded-circle" alt="spike-img">
                <div>
                  <h4 class="mb-0 fs-3 fw-normal">
                    {{ Auth::check() ? Auth::user()->name . ' ' . Auth::user()->last : 'Guest' }}
                  </h4>
                  <span class="text-muted">{{ Auth::check() ? (Auth::user()->roles->first()->name ?? 'User') : 'Guest' }}</span>
                </div>
              </div>
              <a method="POST" href="{{ route('logout') }}" class="position-relative" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Logout">
                <iconify-icon icon="solar:logout-line-duotone" class="fs-8"></iconify-icon>
              </a>
            </div>
          </div>
        </div>
      </div>

    </aside>
    <div class="page-wrapper">

      <div class="body-wrapper">
        <div class="container-fluid">
          <header class="topbar sticky-top">
            @include('backend.script.navbar')
            <div class="with-vertical">
              <nav class="navbar navbar-expand-lg p-0">
                <ul class="navbar-nav">
                  <li class="nav-item nav-icon-hover-bg rounded-circle">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                      <iconify-icon icon="solar:list-bold-duotone" class="fs-7">+</iconify-icon>
                    </a>
                  </li>
                </ul>

                <ul class="navbar-nav quick-links d-none d-lg-flex align-items-center">
                  <li class="nav-item dropdown-hover d-none d-lg-block me-2">
                    <a class="nav-link" href="{{ route('components.chat-widget') }}">Chat</a>
                  </li>
                </ul>
                <ul class="navbar-nav quick-links d-none d-lg-flex align-items-center">
                  <li class="nav-item dropdown-hover d-none d-lg-block me-2">
                    <a class="nav-link" href="{{ route('backend.calendar.index') }}">Calendar</a>
                  </li>
                </ul>


                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                      <div class="nav-icon-hover-bg rounded-circle ">
                        <i class="ti ti-align-justified fs-7"></i>
                      </div>
                    </a>
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

                      <li class="nav-item nav-icon-hover-bg rounded-circle">
                        <a class="nav-link moon dark-layout" href="javascript:void(0)">
                          <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                        </a>
                        <a class="nav-link sun light-layout" href="javascript:void(0)">
                          <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                        </a>
                      </li>

                      <li class="nav-item dropdown nav-icon-hover-bg rounded-circle notification-container">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="notificationDropdown" aria-expanded="false">
                          <iconify-icon icon="solar:bell-line-duotone" class="fs-6"></iconify-icon>
                          <span id="notificationCount" class="notification-badge">0</span>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="notificationDropdown">
                          <div class="d-flex align-items-center py-3 px-7 justify-content-between">
                            <h3 class="mb-0 fs-5">Notifications</h3>
                            <button class="btn btn-link p-0 mark-all-read text-muted">Mark all read</button>
                          </div>

                          <div id="notificationList" class="message-body overflow-hidden overflow-y-auto" data-simplebar>
                          </div>

                          <div class="py-6 px-7 mb-1">
                            <button class="btn btn-primary w-100">
                              See All Notifications
                            </button>
                          </div>
                        </div>
                      </li>

                      <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="reminderDropdown" aria-expanded="false">
                          <iconify-icon icon="solar:alarm-line-duotone" class="fs-6"></iconify-icon>
                          <span id="reminderCount" class="badge1 bg-success rounded-pill position-absolute top-2 start-100 translate-middle">0</span>

                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="reminderDropdown">
                          <div class="d-flex align-items-center py-3 px-7 justify-content-between">
                            <h3 class="mb-0 fs-5">Reminders</h3>
                            <button class="btn btn-link p-0 mark-all-read text-muted">Mark all read</button>
                          </div>
                          <div id="reminderList" class="message-body overflow-hidden overflow-y-auto" data-simplebar>
                          </div>
                          <div class="py-6 px-7 mb-1">
                            <button class="btn btn-primary w-100">
                              See All Reminders
                            </button>
                          </div>
                        </div>
                      </li>


                      <style>
                        #reminderList {
                          max-height: 300px;
                          overflow-y: auto;
                        }

                        .dropdown-menu {
                          display: none;
                        }

                        .dropdown-menu.show {
                          display: block;
                        }
                      </style>

                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="userDropdown" aria-expanded="false">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : asset('assets/images/profile/user-1.jpg') }}" width="40" class="rounded-circle" alt="{{ Auth::check() ? Auth::user()->name . '\'s avatar' : 'Default profile' }}">
                            </div>
                            <span class="d-sm-none d-block"><iconify-icon icon="solar:alt-arrow-down-line-duotone"></iconify-icon></span>
                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name">
                                {{ Auth::check() ? Auth::user()->name . ' ' . Auth::user()->last : 'Guest' }}
                              </h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext">
                                {{ Auth::check() ? (Auth::user()->roles->first()->name ?? 'User') : 'Guest' }}
                              </p>
                            </div>
                          </div>
                        </a>

                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="userDropdown">
                          <div class="profile-dropdown position-relative" data-simplebar="">
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">User Profile</h3>
                            </div>

                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : asset('assets/images/profile/user-1.jpg') }}" alt="user" width="90" class="rounded-circle">
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h4>
                                <span class="text-muted">{{ Auth::check() ? (Auth::user()->roles->first()->name ?? 'User') : 'Guest' }}</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <iconify-icon icon="solar:mailbox-line-duotone" class="fs-4 me-1"></iconify-icon>
                                  {{ Auth::check() ? Auth::user()->email : '' }}
                                </p>
                              </div>
                            </div>

                            <div class="message-body">
                              <a href="{{ route('backend.user.profile') }}" class="dropdown-item px-7 d-flex align-items-center py-6">
                                <span class="btn px-3 py-2 bg-info-subtle rounded-1 text-info shadow-none">
                                  <iconify-icon icon="solar:wallet-2-line-duotone" class="fs-7"></iconify-icon>
                                </span>
                                <div class="w-100 ps-3 ms-1">
                                  <h5 class="mb-0 mt-1 fs-4 fw-normal">My Profile</h5>
                                  <span class="fs-3 d-block mt-1 text-muted">Account Settings</span>
                                </div>
                              </a>

                              @can('view_users')
                              <a href="{{ route('backend.user.index') }}" class="dropdown-item px-7 d-flex align-items-center py-6">
                                <span class="btn px-3 py-2 bg-success-subtle rounded-1 text-success shadow-none">
                                  <iconify-icon icon="solar:shield-minimalistic-line-duotone" class="fs-7"></iconify-icon>
                                </span>
                                <div class="w-100 ps-3 ms-1">
                                  <h5 class="mb-0 mt-1 fs-4 fw-normal">Manage Users</h5>
                                  <span class="fs-3 d-block mt-1 text-muted">User Administration</span>
                                </div>
                              </a>
                              <a href="{{ route('backend.partners.index') }}" class="dropdown-item px-7 d-flex align-items-center py-6">
                              <span class="btn px-3 py-2 bg-success-subtle rounded-1 text-success shadow-none">
                                <iconify-icon icon="mdi:handshake-outline" class="fs-7"></iconify-icon>
                              </span>
                              <div class="w-100 ps-3 ms-1">
                                <h5 class="mb-0 mt-1 fs-4 fw-normal">Manage Partners</h5>
                                <span class="fs-3 d-block mt-1 text-muted">B2B Agents</span>
                              </div>
                            </a>
                              @endcan
                            </div>
                            
                            <div class="py-6 px-7 mb-1">
                              <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Log Out</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>

              <div class="offcanvas offcanvas-start dropdown-menu-nav-offcanvas" data-bs-scroll="true" tabindex="-1" id="mobilenavbar" aria-labelledby="offcanvasWithBothOptionsLabel">
                <nav class="sidebar-nav scroll-sidebar">
                  <div class="offcanvas-header justify-content-between">
                    <img src="{{ asset('img/wri.png') }}" alt="spike-img" class="img-fluid">
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body h-n80" data-simplebar="">
                    <ul id="sidebarnav">
                      <li class="sidebar-item">
                        <a class="sidebar-link gap-2" href="{{ route('components.chat-widget') }}" aria-expanded="false">
                          <iconify-icon icon="solar:chat-round-unread-line-duotone" class="fs-6 text-dark"></iconify-icon>
                          <span class="hide-menu">Chat</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </nav>
              </div>
            </div>
            <div class="app-header with-horizontal">
              <nav class="navbar navbar-expand-xl container-fluid p-0">
                <ul class="navbar-nav">
                  <li class="nav-item d-none d-xl-block">
                    <a href="index.html" class="text-nowrap nav-link">
                      <img src="{{ asset('img/wri.png') }}" class="dark-logo" width="40" height="50" alt="spike-img">
                      <img src="{{ asset('img/wri.png') }}" class="light-logo" width="40" height="50" alt="spike-img">
                    </a>
                  </li>
                </ul>
                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                      <div class="nav-icon-hover-bg rounded-circle ">
                        <i class="ti ti-align-justified fs-7"></i>
                      </div>
                    </a>
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                      <li class="nav-item nav-icon-hover-bg rounded-circle">
                        <a class="nav-link moon dark-layout" href="javascript:void(0)">
                          <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                        </a>
                        <a class="nav-link sun light-layout" href="javascript:void(0)">
                          <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                        </a>
                      </li>

                      <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                        <a class="nav-link position-relative" href="javascript:void(0)" id="drop3" aria-expanded="false">
                          <iconify-icon icon="solar:chat-dots-line-duotone" class="fs-6"></iconify-icon>
                          <div class="pulse">
                            <span class="heartbit border-warning"></span>
                            <span class="point text-bg-warning"></span>
                          </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop3">
                          <div class="d-flex align-items-center py-3 px-7">
                            <h3 class="mb-0 fs-5">Messages</h3>
                            <span class="badge bg-info ms-3">5 new</span>
                          </div>

                          <div class="message-body" data-simplebar="">
                            <a href="javascript:void(0)" class="dropdown-item px-7 d-flex align-items-center py-6">
                              <span class="flex-shrink-0">
                                <img src="../assets/images/profile/user-2.jpg" alt="user" width="45" class="rounded-circle">
                              </span>
                              <div class="w-100 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                  <h5 class="mb-0 fs-3 fw-normal">
                                    Roman Joined the Team!
                                  </h5>
                                  <span class="fs-2 text-nowrap d-block text-muted">9:08 AM</span>
                                </div>
                                <span class="fs-2 d-block mt-1 text-muted">Congratulate him</span>
                              </div>
                            </a>

                            <a href="javascript:void(0)" class="dropdown-item px-7 d-flex align-items-center py-6">
                              <span class="flex-shrink-0">
                                <img src="../assets/images/profile/user-3.jpg" alt="user" width="45" class="rounded-circle">
                              </span>
                              <div class="w-100 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                  <h5 class="mb-0 fs-3 fw-normal">
                                    New message received
                                  </h5>
                                  <span class="fs-2 text-nowrap d-block text-muted">9:08 AM</span>
                                </div>
                                <span class="fs-2 d-block mt-1 text-muted">Salma sent you new
                                  message</span>
                              </div>
                            </a>

                            <a href="javascript:void(0)" class="dropdown-item px-7 d-flex align-items-center py-6">
                              <span class="flex-shrink-0">
                                <img src="../assets/images/profile/user-4.jpg" alt="user" width="45" class="rounded-circle">
                              </span>
                              <div class="w-100 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                  <h5 class="mb-0 fs-3 fw-normal">
                                    New Payment received
                                  </h5>
                                  <span class="fs-2 text-nowrap d-block text-muted">9:08 AM</span>
                                </div>
                                <span class="fs-2 d-block mt-1 text-muted">Check your
                                  earnings</span>
                              </div>
                            </a>

                            <a href="javascript:void(0)" class="dropdown-item px-7 d-flex align-items-center py-6">
                              <span class="flex-shrink-0">
                                <img src="../assets/images/profile/user-5.jpg" alt="user" width="45" class="rounded-circle">
                              </span>
                              <div class="w-100 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                  <h5 class="mb-0 fs-3 fw-normal">
                                    New message received
                                  </h5>
                                  <span class="fs-2 text-nowrap d-block text-muted">9:08 AM</span>
                                </div>
                                <span class="fs-2 d-block mt-1 text-muted">Salma sent you new
                                  message</span>
                              </div>
                            </a>

                            <a href="javascript:void(0)" class="dropdown-item px-7 d-flex align-items-center py-6">
                              <span class="flex-shrink-0">
                                <img src="../assets/images/profile/user-6.jpg" alt="user" width="45" class="rounded-circle">
                              </span>
                              <div class="w-100 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                  <h5 class="mb-0 fs-3 fw-normal">
                                    Roman Joined the Team!
                                  </h5>
                                  <span class="fs-2 text-nowrap d-block text-muted">9:08 AM</span>
                                </div>
                                <span class="fs-2 d-block mt-1 text-muted">Congratulate him</span>
                              </div>
                            </a>
                          </div>

                          <div class="py-6 px-7 mb-1">
                            <button class="btn btn-primary w-100">
                              See All Messages
                            </button>
                          </div>
                        </div>
                      </li>
                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="drop1" aria-expanded="false">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : asset('assets/images/profile/user-1.jpg') }}" width="40" class="rounded-circle" alt="{{ Auth::check() ? Auth::user()->name . '\'s avatar' : 'Default profile' }}">
                            </div>
                            <span class="d-sm-none d-block"><iconify-icon icon="solar:alt-arrow-down-line-duotone"></iconify-icon></span>

                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name">
                                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                              </h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext">
                                {{ Auth::check() ? (Auth::user()->roles->first()->name ?? 'User') : 'Guest' }}
                              </p>
                            </div>
                          </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                          <div class="profile-dropdown position-relative" data-simplebar="">
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">User Profile</h3>

                            </div>

                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : asset('assets/images/profile/user-1.jpg') }}" alt="user" width="90" class="rounded-circle">
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h4>
                                <span class="text-muted">{{ Auth::check() ? (Auth::user()->roles->first()->name ?? 'User') : 'Guest' }}</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <iconify-icon icon="solar:mailbox-line-duotone" class="fs-4 me-1"></iconify-icon>
                                  {{ Auth::check() ? Auth::user()->email : '' }}
                                </p>
                              </div>
                            </div>

                            <div class="message-body">
                              <a href="{{ route('backend.user.profile') }}" class="dropdown-item px-7 d-flex align-items-center py-6">
                                <span class="btn px-3 py-2 bg-info-subtle rounded-1 text-info shadow-none">
                                  <iconify-icon icon="solar:wallet-2-line-duotone" class="fs-7"></iconify-icon>
                                </span>
                                <div class="w-100 ps-3 ms-1">
                                  <h5 class="mb-0 mt-1 fs-4 fw-normal">My Profile</h5>
                                  <span class="fs-3 d-block mt-1 text-muted">Account Settings</span>
                                </div>
                              </a>

                              @can('view_users')
                              <a href="{{ route('backend.user.index') }}" class="dropdown-item px-7 d-flex align-items-center py-6">
                                <span class="btn px-3 py-2 bg-success-subtle rounded-1 text-success shadow-none">
                                  <iconify-icon icon="solar:shield-minimalistic-line-duotone" class="fs-7"></iconify-icon>
                                </span>
                                <div class="w-100 ps-3 ms-1">
                                  <h5 class="mb-0 mt-1 fs-4 fw-normal">Manage Users</h5>
                                  <span class="fs-3 d-block mt-1 text-muted">User Administration</span>
                                </div>
                              </a>
                              @endcan
                            </div>

                            <div class="py-6 px-7 mb-1">
                              <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Log Out</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>
            </div>
          </header>
          <div class="">
            @yield('content')
          </div>
        </div>
      </div>
      <div id="reminderNotificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050; max-width: 350px;">
      </div>

      <template id="reminderNotificationTemplate">
        <div class="toast reminder-notification" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
          <div class="toast-header">
            <strong class="me-auto reminder-title">Reminder</strong>
            <small class="reminder-time">Just now</small>
            <button type="button" class="btn-close reminder-close-btn" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            <div class="reminder-message mb-2"></div>
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-sm btn-secondary me-2 reminder-snooze-btn">Snooze</button>
              <button type="button" class="btn btn-sm btn-primary reminder-complete-btn">Complete</button>
            </div>
          </div>
        </div>
      </template>

      <style>
        .reminder-notification {
          transform: translateX(120%);
          transition: transform 0.3s ease-out;
        }

        .reminder-notification.slide-in {
          transform: translateX(0);
        }

        .reminder-notification.slide-out {
          transform: translateX(120%);
        }
      </style>

    </div>
    <div class="dark-transparent sidebartoggler"></div>
  </div>
  <style>
    .disable-interaction {
      pointer-events: none;
      user-select: none;
    }

    #notice-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    #notice-popup {
      display: none;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #ffffff;
      padding: 28px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      width: 100%;
      max-width: 500px;
      font-family: 'Arial', sans-serif;
      pointer-events: auto;
      position: absolute;
    }
  </style>

  <div class="relative h-[90vh] w-[100vw]">
    <div id="notice-overlay">
      <div id="notice-popup">
        <button id="close-notice" style="position: absolute; top: 10px; right: 10px; background: none; color: grey; border: none; font-size: 22px; cursor: pointer; padding: 0; margin: 0;">×</button>

        <div id="notice-header" style="margin-bottom: 16px; border-bottom: 1px solid #eaeaea; padding-bottom: 12px;">
          <h3 style="margin: 0; font-size: 18px; color: #333333; font-weight: 600;">Important Notice</h3>
        </div>
        <div id="notice-content" style="font-size: 15px; line-height: 1.5; color: #555555; margin-bottom: 20px; text-align: left;">
        </div>
        <img id="notice-image" style="max-width: 100%; height: auto; margin: 20px 0; display: none; border-radius: 8px;">
      </div>
    </div>
  </div>

  <style>
    .loader-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      background: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      transition: opacity 0.5s ease-out;
      z-index: 9999;
    }

    .star-trail text {
      stroke-dasharray: 250;
      stroke-dashoffset: 250;
      animation: draw 1.5s ease-in-out infinite;
    }

    @keyframes draw {
      0% {
        stroke-dashoffset: 250;
      }

      50% {
        stroke-dashoffset: 0;
      }

      100% {
        stroke-dashoffset: 250;
      }
    }
  </style>

  <div class="loader-container" id="loader">
    <svg class="star-trail" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 100">
      <text x="250" y="60" font-size="24" font-weight="bold" fill="none" stroke="url(#gradient)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" text-anchor="middle">
        WRI
      </text>
      <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:#1b5e20; stop-opacity:1" />
          <stop offset="100%" style="stop-color:#66bb6a; stop-opacity:1" />
        </linearGradient>
      </defs>
    </svg>
  </div>
  @stack('scripts')
  <script src="{{ asset('resources/js/app.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/nepali-date-converter/dist/nepali-date-converter.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/nepali-date-converter@3.3.4/dist/nepali-date-converter.umd.min.js"></script>

  <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

  <script defer src="{{ asset('assets/js/iconify-icon.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
  <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
  <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
  <script src="{{ asset('assets/js/theme/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/theme/feather.min.js') }}"></script>
  <script src="{{ asset('assets/js/highlights/highlight.min.js') }}"></script>
  <script src="{{ asset('assets/libs/jvectormap/jquery-jvectormap.min.js') }}"></script>
  <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/js/extra-libs/jvectormap/jquery-jvectormap-us-aea-en.js') }}"></script>
  <script src="{{ asset('assets/js/dashboards/dashboard.js') }}"></script>


  <script>
    setTimeout(() => {
      const loader = document.getElementById('loader');
      if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => {
          loader.style.display = 'none';
        }, 100);
      }
    }, 600);

    if (typeof hljs !== 'undefined') {
      hljs.initHighlightingOnLoad();
    }
    document.querySelectorAll("pre.code-view > code").forEach((codeBlock) => {
      codeBlock.textContent = codeBlock.innerHTML;
    });

    function handleColorTheme(e) {
      document.documentElement.setAttribute("data-color-theme", e);
    }

    document.addEventListener('DOMContentLoaded', function() {

      function updateReminderCountBadge(count) {
        const badge = document.getElementById('reminderCount');
        if (badge) {
          badge.textContent = count;
          if (count === 0 || count === '0' || count < 1) {
            badge.style.display = 'none';
          } else {
            badge.style.display = '';
          }
        } else {
          console.error('Reminder count badge element not found');
        }
      }
      updateReminderCountBadge(0);


      (function() {
        let dismissedReminders = loadDismissedReminders();

        fetchReminders();
        const checkInterval = setInterval(fetchReminders, 30000);
        const dismissedCheckInterval = setInterval(checkDismissedRemindersOnly, 10000);

        document.addEventListener('click', function() {
          logDebug('Page interaction detected, checking dismissed reminders');
          checkDismissedRemindersOnly();
        }, {
          once: true
        });

        function logDebug(message, data) {}

        function loadDismissedReminders() {
          const savedReminders = localStorage.getItem('dismissedReminders');
          const reminders = savedReminders ? JSON.parse(savedReminders) : {};
          logDebug('Loaded dismissed reminders', reminders);
          return reminders;
        }

        function saveDismissedReminders(reminders) {
          localStorage.setItem('dismissedReminders', JSON.stringify(reminders));
          logDebug('Saved dismissed reminders', reminders);
        }

        function fetchReminders() {
          fetch('/lead-reminders')
            .then(response => response.json())
            .then(data => {
              logDebug('Fetched reminders from server', data);
              displayRemindersInList(data);

              // Count only today's overdue reminders (same as display)
              const activeReminders = data.filter(reminder => {
                if (reminder.is_completed) return false;

                const now = new Date();
                const todayStart = new Date(now);
                todayStart.setHours(0, 0, 0, 0);

                const todayEnd = new Date(now);
                todayEnd.setHours(23, 59, 59, 999);

                const reminderDate = new Date(reminder.date_time);
                return (
                  reminderDate >= todayStart &&
                  reminderDate <= todayEnd &&
                  reminderDate <= now
                );
              });

              updateReminderCountBadge(activeReminders.length);
              processRemindersForNotifications(data);
            })
            .catch(error => {
              console.error('Error fetching reminders:', error);
            });
        }

      function displayRemindersInList(reminders) {
  const reminderList = document.getElementById('reminderList');
  if (!reminderList) return;
  reminderList.innerHTML = '';

  const now = new Date();

  // Start and end of today
  const todayStart = new Date(now);
  todayStart.setHours(0, 0, 0, 0);

  const todayEnd = new Date(now);
  todayEnd.setHours(23, 59, 59, 999);

  // Filter reminders that are not completed and are scheduled for today
  const activeReminders = reminders.filter(reminder => {
    if (reminder.is_completed) return false;

    const reminderDate = new Date(reminder.date_time);
    return reminderDate >= todayStart && reminderDate <= todayEnd;
  });

  // Sort reminders: overdue ones first, then upcoming ones, both sorted by time
  activeReminders.sort((a, b) => {
    const aDate = new Date(a.date_time);
    const bDate = new Date(b.date_time);
    const aIsOverdue = aDate < now;
    const bIsOverdue = bDate < now;

    if (aIsOverdue && !bIsOverdue) return -1;
    if (!aIsOverdue && bIsOverdue) return 1;
    return aDate - bDate;
  });

  console.log("Filtered and Sorted Reminders:", activeReminders);

  // Update badge with the filtered list
  updateReminderCountBadge(activeReminders.length);

  if (activeReminders.length === 0) {
    reminderList.innerHTML = `
      <div class="dropdown-item px-7 d-flex align-items-center py-6">
        <div class="w-100 ps-3 text-center">
          <p class="fs-3 text-muted">No active reminders right now</p>
        </div>
      </div>`;
    return;
  }

  // Render each reminder
  activeReminders.forEach(reminder => {
    const reminderElement = document.createElement('div');
    reminderElement.className = 'dropdown-item px-7 d-flex align-items-center py-6';

    const reminderDate = new Date(reminder.date_time);
    const formattedDate = reminderDate.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });

    const timeDiff = now - reminderDate;
    const minutesLate = Math.floor(timeDiff / (1000 * 60));
    const lateText = minutesLate > 0 ? `<span class="badge bg-danger ms-2">${minutesLate}m late</span>` : '';

    reminderElement.innerHTML = `
      <div class="w-100 ps-3">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="mb-0 fs-3 fw-normal">${reminder.lead_name} ${lateText}</h5>
          <span class="fs-2 text-nowrap d-block text-muted">${reminder.user_name}</span>
        </div>
        <span class="fs-2 d-block mt-1 text-muted">Lead ID: ${reminder.lead_id}</span>
        <p class="fs-3 d-block text-dark mt-2 mb-2"><strong>Comment:</strong> ${reminder.comment}</p>
        <p class="fs-3 d-block text-dark mb-2"><strong>Reminder Time:</strong> ${formattedDate}</p>
        <button class="btn btn-sm btn-primary mark-complete-list" data-reminder-id="${reminder.id}">Complete</button>
      </div>`;

    reminderList.appendChild(reminderElement);
  });

  // Attach event listeners to complete buttons
  document.querySelectorAll('.mark-complete-list').forEach(button => {
    button.addEventListener('click', function() {
      const reminderId = this.dataset.reminderId;
      markReminderAsComplete(reminderId, true);
    });
  });
}



        function markReminderAsComplete(reminderId, refetchList = false) {
          fetch(`/lead-reminders/${reminderId}/complete`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                logDebug('Successfully marked reminder as complete', {
                  reminderId
                });
                if (refetchList) {
                  fetchReminders();
                }
              }
            })
            .catch(error => {
              console.error('Error marking reminder as complete:', error);
            });
        }

        const reminderDropdownEl = document.getElementById('reminderDropdown');
        if (reminderDropdownEl) {
          reminderDropdownEl.addEventListener('click', function() {
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu) dropdownMenu.classList.toggle('show');
          });
        }
        document.addEventListener('click', function(event) {
          const reminderDropdownElement = document.getElementById('reminderDropdown');
          if (reminderDropdownElement && !reminderDropdownElement.contains(event.target)) {
            const dropdownMenu = reminderDropdownElement.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('show') && !dropdownMenu.contains(event.target)) {
              dropdownMenu.classList.remove('show');
            }
          }
        });


        function checkDismissedRemindersOnly() {
          dismissedReminders = loadDismissedReminders();
          const now = Date.now();
          let anyReady = false;

          for (const reminderId in dismissedReminders) {
            const reminder = dismissedReminders[reminderId];
            if (!reminder.isCompleted && now >= reminder.reappearTime) {
              anyReady = true;
              delete dismissedReminders[reminderId];
              saveDismissedReminders(dismissedReminders);
              fetch('/lead-reminders')
                .then(response => response.json())
                .then(data => {
                  const fullReminder = data.find(r => r.id == reminderId);
                  if (fullReminder && !fullReminder.is_completed) {
                    showReminderNotification(fullReminder);
                  }
                })
                .catch(error => console.error('Error fetching for reappearing reminder:', error));
              return;
            }
          }
          if (!anyReady) logDebug('No dismissed reminders ready to reappear');
        }

        function processRemindersForNotifications(reminders) {
          dismissedReminders = loadDismissedReminders();
          const now = Date.now();
          let showingDismissedReminder = false;

          for (const reminderId in dismissedReminders) {
            const dismissedInfo = dismissedReminders[reminderId];
            if (!dismissedInfo.isCompleted && now >= dismissedInfo.reappearTime) {
              delete dismissedReminders[reminderId];
              const matchingReminder = reminders.find(r => r.id == reminderId);
              if (matchingReminder && !matchingReminder.is_completed) {
                showReminderNotification(matchingReminder);
                showingDismissedReminder = true;
                saveDismissedReminders(dismissedReminders);
                return;
              }
            }
          }
          saveDismissedReminders(dismissedReminders);
          if (showingDismissedReminder) return;

          for (const reminder of reminders) {
            if (reminder.is_completed || dismissedReminders[reminder.id]) continue;

            const reminderTime = new Date(reminder.date_time);
            const timeDiff = Math.abs(new Date() - reminderTime);
            if (timeDiff < 60000 && !document.getElementById(`reminder-notification-${reminder.id}`)) {
              showReminderNotification(reminder);
              return;
            }
          }
        }

        function showReminderNotification(reminder) {
          if (document.getElementById(`reminder-notification-${reminder.id}`)) return;

          const template = document.getElementById('reminderNotificationTemplate');
          if (!template) {
            console.error("Reminder template not found");
            return;
          }

          const notificationElement = template.content.cloneNode(true).querySelector('.reminder-notification');
          notificationElement.id = `reminder-notification-${reminder.id}`;
          notificationElement.querySelector('.reminder-message').innerHTML =
            `<strong>Lead: ${reminder.lead_name}</strong><br>${reminder.comment}`;

          notificationElement.querySelector('.reminder-complete-btn').dataset.reminderId = reminder.id;
          notificationElement.querySelector('.reminder-snooze-btn').dataset.reminderId = reminder.id;
          notificationElement.querySelector('.reminder-close-btn').dataset.reminderId = reminder.id;

          document.getElementById('reminderNotificationContainer').appendChild(notificationElement);

          if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toast = new bootstrap.Toast(notificationElement, {
              autohide: false,
              animation: true
            });
            toast.show();
            notificationElement.classList.add('slide-in');

            notificationElement.querySelector('.reminder-complete-btn').addEventListener('click', handleReminderCompleteToast);
            notificationElement.querySelector('.reminder-snooze-btn').addEventListener('click', handleReminderSnoozeToast);
            notificationElement.querySelector('.reminder-close-btn').addEventListener('click', handleReminderSnoozeToast);
            playNotificationSound();
          } else {
            console.error("Bootstrap Toast is not available.");
          }
        }

        function playNotificationSound() {
          try {
            const audio = new Audio('/assets/sounds/notification.mp3');
            audio.play().catch(e => console.warn("Audio play failed:", e));
          } catch (error) {
            console.error('Error playing notification sound:', error);
          }
        }

        function handleReminderCompleteToast(event) {
          const reminderId = this.dataset.reminderId;
          const notificationElement = this.closest('.reminder-notification');
          markReminderAsComplete(reminderId, true);

          dismissedReminders = loadDismissedReminders();
          dismissedReminders[reminderId] = {
            id: reminderId,
            isCompleted: true,
            reappearTime: Date.now() + (24 * 60 * 60 * 1000)
          };
          saveDismissedReminders(dismissedReminders);

          if (notificationElement && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            notificationElement.classList.add('slide-out');
            setTimeout(() => {
              const toast = bootstrap.Toast.getInstance(notificationElement);
              if (toast) toast.dispose();
              notificationElement.remove();
            }, 300);
          }
        }

        function handleReminderSnoozeToast() {
          const reminderId = this.dataset.reminderId;
          const notificationElement = this.closest('.reminder-notification');
          const reappearTime = Date.now() + (2 * 60 * 1000);

          dismissedReminders = loadDismissedReminders();
          dismissedReminders[reminderId] = {
            id: reminderId,
            isCompleted: false,
            reappearTime: reappearTime,
            dismissedAt: Date.now()
          };
          saveDismissedReminders(dismissedReminders);

          if (notificationElement && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            notificationElement.classList.add('slide-out');
            setTimeout(() => {
              const toast = bootstrap.Toast.getInstance(notificationElement);
              if (toast) toast.dispose();
              notificationElement.remove();
            }, 300);
          }
        }
      })();


      (function() {
        let seenNoticesInSession = JSON.parse(localStorage.getItem('seenNoticesInSession') || '[]');
        let currentNoticeId = null;

        function checkUnseenNotices() {
          fetch('/notices/unseen')
            .then(response => response.json())
            .then(data => {
              if (data.notices && data.notices.length > 0) {
                const latestNotice = [...data.notices].sort((a, b) => b.id - a.id)[0];
                if (seenNoticesInSession.includes(latestNotice.id)) return;

                const noticeContentEl = document.getElementById('notice-content');
                const noticeImageEl = document.getElementById('notice-image');
                const noticePopupEl = document.getElementById('notice-popup');
                const noticeOverlayEl = document.getElementById('notice-overlay');

                if (!noticeContentEl || !noticeImageEl || !noticePopupEl || !noticeOverlayEl) {
                  console.error("Notice popup elements not found.");
                  return;
                }

                currentNoticeId = latestNotice.id;
                noticeContentEl.innerHTML = '<h3>' + latestNotice.title + '</h3><p>' + latestNotice.description + '</p>';
                if (latestNotice.image_url) {
                  noticeImageEl.src = latestNotice.image_url;
                  noticeImageEl.style.display = 'block';
                  noticeImageEl.alt = latestNotice.title;
                } else {
                  noticeImageEl.style.display = 'none';
                }
                noticeOverlayEl.style.display = 'block';
                noticePopupEl.style.display = 'block';
                document.body.classList.add('disable-interaction');
              }
            })
            .catch(error => console.error('Error fetching notices:', error));
        }

        function closeNoticePopup() {
          if (!currentNoticeId) {
            hideNoticePopup();
            return;
          }
          fetch('/notices/mark-as-seen/' + currentNoticeId, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            })
            .then(response => {
              if (!response.ok) throw new Error('Network response error');
              return response.json();
            })
            .then(data => {
              if (!seenNoticesInSession.includes(currentNoticeId)) {
                seenNoticesInSession.push(currentNoticeId);
                localStorage.setItem('seenNoticesInSession', JSON.stringify(seenNoticesInSession));
              }
              hideNoticePopup();
            })
            .catch(error => {
              console.error('Error marking notice as seen:', error);
              hideNoticePopup();
            });
        }

        function hideNoticePopup() {
          const noticePopupEl = document.getElementById('notice-popup');
          const noticeOverlayEl = document.getElementById('notice-overlay');
          if (noticePopupEl) noticePopupEl.style.display = 'none';
          if (noticeOverlayEl) noticeOverlayEl.style.display = 'none';
          document.body.classList.remove('disable-interaction');
        }

        const closeNoticeBtn = document.getElementById('close-notice');
        if (closeNoticeBtn) closeNoticeBtn.addEventListener('click', closeNoticePopup);

        window.clearSeenNotices = function() {
          localStorage.removeItem('seenNoticesInSession');
        };
        const logoutLinks = document.querySelectorAll('a[href*="logout"], button[id*="logout"], input[value*="Logout"], form[action*="logout"] button[type="submit"]');
        logoutLinks.forEach(link => link.addEventListener('click', window.clearSeenNotices));

        if (document.querySelector('form[action*="login"]')) {
          if (localStorage.getItem('justLoggedOut') === 'true') {
            localStorage.removeItem('justLoggedOut');
            window.clearSeenNotices();
          }
        }
        if (window.location.pathname.includes('logout')) {
          localStorage.setItem('justLoggedOut', 'true');
          window.clearSeenNotices();
        }

        checkUnseenNotices();
      })();

    });
  </script>
</body>
</html>