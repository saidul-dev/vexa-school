@php
use App\Models\User;

    $user = Auth()->user();
    $menu_permission = (empty($user->menu_permission) || $user->menu_permission == 'null') ? []:json_decode($user->menu_permission, true);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- New -->
    <title>{{ get_phrase('Admin').' | '.get_settings('system_title') }}</title>
    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- all the css files -->
    <link rel="shortcut icon" href="{{ asset('assets/uploads/logo/'.get_settings('favicon')) }}" />
    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css') }}"
    />

    <!--Custom css-->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/css/swiper-bundle.min.css') }}"
    />
    
    
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}" />
    <!-- Datepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <!--Light box Image Gallery-->
    <link rel="stylesheet" type="text/css" href="/ekattor8_v9/Ekattor8/public/assets/css/lightbox.css">
    <!-- SummerNote Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/summernote-lite.min.css') }}"/>

    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css') }}"
    />

    <!--Toaster css-->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/css/toastr.min.css') }}"
    />

    <!-- Calender css -->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/calender/main.css') }}"
    />

    <script src="{{ asset('assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>


</head>

<body>

    <div class="sidebar">
        <div class="logo-details mt-4 mb-3">
            <div class="img_wrapper">
                <img height="40px" class="" src="{{ asset('assets/uploads/logo/'.get_settings('white_logo')) }}" alt="" />
            </div>
            <span class="logo_name">{{ get_settings('navbar_title') }}</span>
        </div>
        <div class="closeIcon">
          <span>
            <img src="{{ asset('assets/images/close.svg') }}">
          </span>
        </div>
      <ul class="nav-links">
        <!-- sidebar title -->
        <li class="nav-links-li {{ request()->is('admin/dashboard') ? 'showMenu':'' }}">
            <div class="iocn-link">
              <a href="{{ route('admin.dashboard') }}">
                <div class="sidebar_icon">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
                  <g>
                    <path d="M117.333,234.667C52.532,234.667,0,182.135,0,117.333S52.532,0,117.333,0s117.333,52.532,117.333,117.333   C234.596,182.106,182.106,234.596,117.333,234.667z M117.333,64C87.878,64,64,87.878,64,117.333s23.878,53.333,53.333,53.333   s53.333-23.878,53.333-53.333S146.789,64,117.333,64z"/>
                    <path d="M394.667,234.667c-64.801,0-117.333-52.532-117.333-117.333S329.865,0,394.667,0S512,52.532,512,117.333   C511.929,182.106,459.439,234.596,394.667,234.667z M394.667,64c-29.455,0-53.333,23.878-53.333,53.333   s23.878,53.333,53.333,53.333S448,146.789,448,117.333S424.122,64,394.667,64z"/>
                    <path d="M117.333,512C52.532,512,0,459.468,0,394.667s52.532-117.333,117.333-117.333s117.333,52.532,117.333,117.333   C234.596,459.439,182.106,511.929,117.333,512z M117.333,341.333C87.878,341.333,64,365.211,64,394.667S87.878,448,117.333,448   s53.333-23.878,53.333-53.333S146.789,341.333,117.333,341.333z"/>
                    <path d="M394.667,512c-64.801,0-117.333-52.532-117.333-117.333s52.532-117.333,117.333-117.333S512,329.865,512,394.667   C511.929,459.439,459.439,511.929,394.667,512z M394.667,341.333c-29.455,0-53.333,23.878-53.333,53.333S365.211,448,394.667,448   S448,424.122,448,394.667S424.122,341.333,394.667,341.333z"/>
                  </g>
                  </svg>

                </div>
                <span class="link_name">{{ get_phrase('Dashboard') }}</span>
              </a>
            </div>
        </li>
        <!-- Sidebar menu -->
          @if(empty($user->menu_permission) || in_array('admin.admin', $menu_permission) || in_array('admin.teacher', $menu_permission) || in_array('admin.accountant', $menu_permission) || in_array('admin.librarian', $menu_permission) || in_array('admin.parent', $menu_permission) || in_array('admin.student', $menu_permission) || in_array('admin.teacher.permission', $menu_permission)) 
          <li class="nav-links-li {{ request()->is('admin/admin*') || request()->is('admin/teacher*') || request()->is('admin/accountant*') || request()->is('admin/librarian*') || request()->is('admin/parent*') || request()->is('admin/student') || request()->is('admin/permission*') ? 'showMenu':'' }}">
              <div class="iocn-link">
                  <a href="#">
                      <div class="sidebar_icon">
                          <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,24a1.5,1.5,0,0,1-1.489-1.335,3.031,3.031,0,0,0-6.018,0,1.5,1.5,0,0,1-2.982-.33,6.031,6.031,0,0,1,11.982,0,1.5,1.5,0,0,1-1.326,1.656A1.557,1.557,0,0,1,16.5,24Zm6.167-9.009a1.5,1.5,0,0,0,1.326-1.656A5.815,5.815,0,0,0,18.5,8a1.5,1.5,0,0,0,0,3,2.835,2.835,0,0,1,2.509,2.665A1.5,1.5,0,0,0,22.5,15,1.557,1.557,0,0,0,22.665,14.991ZM2.991,13.665A2.835,2.835,0,0,1,5.5,11a1.5,1.5,0,0,0,0-3A5.815,5.815,0,0,0,.009,13.335a1.5,1.5,0,0,0,1.326,1.656A1.557,1.557,0,0,0,1.5,15,1.5,1.5,0,0,0,2.991,13.665ZM12.077,16a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,12.077,16Zm6-9a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,18.077,7Zm-12,0a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,6.077,7Z"/></svg>
                      </div>
                      <span class="link_name">{{ get_phrase('Users') }}</span>
                  </a>
                  <span class="arrow">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="4.743"
                      height="7.773"
                      viewBox="0 0 4.743 7.773"
                    >
                      <path
                        id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                        d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                        fill="#fff"
                        opacity="1"
                      />
                    </svg>
                  </span>
              </div>
              <ul class="sub-menu">
                  @if(empty($user->menu_permission) || in_array('admin.admin', $menu_permission))
                    <li><a class="{{ (request()->is('admin/admin*')) ? 'active' : '' }}" href="{{ route('admin.admin') }}"><span>
                                {{ get_phrase('Admin') }}
                            </span></a></li>
                  @endif
                  @if(empty($user->menu_permission) || in_array('admin.teacher', $menu_permission))
                    <li><a class="{{ (request()->is('admin/teacher*')) ? 'active' : '' }}" href="{{ route('admin.teacher') }}"><span>
                      {{ get_phrase('Teacher') }}
                  </span></a></li>
                  @endif
                  @if(empty($user->menu_permission) || in_array('admin.accountant', $menu_permission))
                    <li><a class="{{ (request()->is('admin/accountant*')) ? 'active' : '' }}" href="{{ route('admin.accountant') }}"><span>
                      {{ get_phrase('Accountant') }}
                  </span></a></li>
                  @endif
                  @if(empty($user->menu_permission) || in_array('admin.librarian', $menu_permission))
                    <li><a class="{{ (request()->is('admin/librarian*')) ? 'active' : '' }}" href="{{ route('admin.librarian') }}"><span>
                      {{ get_phrase('Librarian') }}
                  </span></a></li>
                  @endif
                  @if(empty($user->menu_permission) || in_array('admin.parent', $menu_permission))
                    <li><a class="{{ (request()->is('admin/parent*')) ? 'active' : '' }}" href="{{ route('admin.parent') }}"><span>
                      {{ get_phrase('Parent') }}
                  </span></a></li>
                  @endif
                  @if(empty($user->menu_permission) || in_array('admin.student', $menu_permission))
                    <li><a class="{{ (request()->is('admin/student')) ? 'active' : '' }}" href="{{ route('admin.student') }}"><span>
                      {{ get_phrase('Student') }}
                  </span></a></li>
                  @endif
                  @if(empty($user->menu_permission) || in_array('admin.permission', $menu_permission))
                    <li><a class="{{ (request()->is('admin/permission*')) ? 'active' : '' }}" href="{{ route('admin.teacher.permission') }}"><span>
                      {{ get_phrase('Teacher Permission') }}
                  </span></a></li>
                  @endif
              </ul>
          </li>
          @endif

          @if(empty($user->menu_permission) || in_array('admin.offline_admission.single', $menu_permission)) 
          <li class="nav-links-li {{ request()->is('admin/offline_admission*') ? 'showMenu':'' }}">
              <div class="iocn-link">
                  <a class="{{ (request()->is('admin/offline_admission*')) ? 'active' : '' }}" href="{{ route('admin.offline_admission.single', ['type' => 'single']) }}">
                      <div class="sidebar_icon">
                          <svg xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="48" height="48"><path d="M11,14H5a5.006,5.006,0,0,0-5,5v5H3V19a2,2,0,0,1,2-2h6a2,2,0,0,1,2,2v5h3V19A5.006,5.006,0,0,0,11,14Z"/><path d="M8,12A6,6,0,1,0,2,6,6.006,6.006,0,0,0,8,12ZM8,3A3,3,0,1,1,5,6,3,3,0,0,1,8,3Z"/><polygon points="21 10 21 7 18 7 18 10 15 10 15 13 18 13 18 16 21 16 21 13 24 13 24 10 21 10"/></svg>
                      </div>
                      <span class="link_name">
                          {{ get_phrase('Admissions') }}
                      </span>
                  </a>
              </div>
          </li>
        @endif

        @if (addon_status('transport') == 1)
        <li
            class="nav-links-li {{ request()->is('admin/driver/list*') || request()->is('admin/vehicle/list*') || request()->is('admin/assign/student/list*') ? 'showMenu' : '' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="48"
                            height="48" id="Layer_1" data-name="Layer 1">
                            <path
                                d="M288 0C422.4 0 512 35.2 512 80V96l0 32c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32l0 160c0 17.7-14.3 32-32 32v32c0 17.7-14.3 32-32 32H416c-17.7 0-32-14.3-32-32V448H192v32c0 17.7-14.3 32-32 32H128c-17.7 0-32-14.3-32-32l0-32c-17.7 0-32-14.3-32-32l0-160c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h0V96h0V80C64 35.2 153.6 0 288 0zM128 160v96c0 17.7 14.3 32 32 32H272V128H160c-17.7 0-32 14.3-32 32zM304 288H416c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32H304V288zM144 400a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm288 0a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM384 80c0-8.8-7.2-16-16-16H208c-8.8 0-16 7.2-16 16s7.2 16 16 16H368c8.8 0 16-7.2 16-16z" />
                        </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Transport') }}
                    </span>
                </a>
                <span class="arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="4.743" height="7.773"
                        viewBox="0 0 4.743 7.773">
                        <path id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                            d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                            fill="#fff" opacity="1" />
                    </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="{{ request()->is('admin/driver/list') ? 'active' : '' }}"
                        href="{{ route('admin.driver.list') }}"><span>{{ get_phrase('Driver') }}</span></a>
                </li>
                <li>
                    <a class="{{ request()->is('admin/vehicle/list') ? 'active' : '' }}"
                        href="{{ route('admin.vehicle.list') }}"><span>{{ get_phrase('Vehicle') }}</span></a>
                </li>
                <li>
                    <a class="{{ request()->is('admin/assign/student/list') ? 'active' : '' }}"
                        href="{{ route('admin.assign.student.list') }}"><span>{{ get_phrase('Assign student') }}</span></a>
                </li>

            </ul>
        </li>
        @endif

        @if(empty($user->menu_permission) || in_array('admin.exam_category', $menu_permission) || in_array('admin.offline_exam', $menu_permission) || in_array('admin.marks', $menu_permission) || in_array('admin.grade_list', $menu_permission) || in_array('admin.promotion', $menu_permission))
        <li class="nav-links-li {{ request()->is('admin/exam_category*') || request()->is('admin/offline_exam*') || request()->is('admin/marks') || request()->is('admin/grade') || request()->is('admin/promotion*') || request()->is('admin/admit-card-list') || request()->is('admin/admitCardFilter') || request()->is('admin/print-admit-card')? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M18,17.5A1.5,1.5,0,0,1,16.5,19h-1a1.5,1.5,0,0,1,0-3h1A1.5,1.5,0,0,1,18,17.5ZM13.092,14H10.908A1.5,1.5,0,0,1,8,13.5V10a4,4,0,0,1,8,0v3.5a1.5,1.5,0,0,1-2.908.5ZM11,10v1h2V10a1,1,0,0,0-2,0Zm-.569,5.947-.925.941a1.5,1.5,0,0,0-2.139,2.095s.163.187.189.211a2.757,2.757,0,0,0,3.9-.007l1.116-1.134a1.5,1.5,0,1,0-2.138-2.106ZM22,7.157V18.5A5.507,5.507,0,0,1,16.5,24h-9A5.507,5.507,0,0,1,2,18.5V5.5A5.507,5.507,0,0,1,7.5,0h7.343a5.464,5.464,0,0,1,3.889,1.611l1.657,1.657A5.464,5.464,0,0,1,22,7.157ZM18.985,7H17a2,2,0,0,1-2-2V3.015C14.947,3.012,7.5,3,7.5,3A2.5,2.5,0,0,0,5,5.5v13A2.5,2.5,0,0,0,7.5,21h9A2.5,2.5,0,0,0,19,18.5S18.988,7.053,18.985,7Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Examination') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
              @if(empty($user->menu_permission) || in_array('admin.exam_category', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/exam_category*')) ? 'active' : '' }}" href="{{ route('admin.exam_category') }}"><span>{{ get_phrase('Exam Category') }}</span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.offline_exam', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/offline_exam*')) ? 'active' : '' }}" href="{{ route('admin.offline_exam') }}"><span>{{ get_phrase('Offline Exam') }}</span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.marks', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/marks')) ? 'active' : '' }}" href="{{ route('admin.marks') }}"><span>{{ get_phrase('Marks') }}</span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.grade_list', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/grade')) ? 'active' : '' }}" href="{{ route('admin.grade_list') }}"><span>{{ get_phrase('Grades') }}</span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.promotion', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/promotion*')) ? 'active' : '' }}" href="{{ route('admin.promotion') }}"><span>{{ get_phrase('Promotion') }}</span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.admit_card_list', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/admit-card-list*')) ? 'active' : '' }}" href="{{ route('admin.examination.admit_card_list') }}"><span>{{ get_phrase('Admit Card') }}</span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.admit_card_print', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/print-admit-card*')) || request()->is('admin/admitCardFilter') ? 'active' : '' }}" href="{{ route('admin.examination.admit_card_print') }}"><span>{{ get_phrase('Print Admit Card') }}</span></a>
              </li>
              @endif
            </ul>
        </li>
        @endif

        @if(empty($user->menu_permission) || in_array('admin.daily_attendance', $menu_permission) || in_array('admin.class_list', $menu_permission) || in_array('admin.routine', $menu_permission) || in_array('admin.gradebook', $menu_permission) || in_array('admin.syllabus', $menu_permission) || in_array('admin.class_room_list', $menu_permission) || in_array('admin.department_list', $menu_permission))
        <li class="nav-links-li {{ request()->is('admin/attendance*') || request()->is('admin/class_list*') || request()->is('admin/routine*') || request()->is('admin/subject*') || request()->is('admin/syllabus*') || request()->is('admin/gradebook*') || request()->is('admin/class_room*') || request()->is('admin/department*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M7.5,4.5c.151-5.935,8.85-5.934,9,0-.151,5.935-8.85,5.934-9,0ZM24,15.5v1.793c0,2.659-1.899,4.935-4.516,5.411l-5.763,1.139c-1.142,.207-2.285,.21-3.421,.004l-5.807-1.147c-2.595-.472-4.494-2.748-4.494-5.407v-1.793c-.083-3.331,3.222-6.087,6.483-5.411l3.36,.702c.824,.15,1.564,.527,2.16,1.062,.601-.537,1.351-.916,2.191-1.069l3.282-.688c1.653-.301,3.293,.134,4.548,1.181,1.256,1.048,1.976,2.587,1.976,4.223Zm-13.5-.289c0-.726-.518-1.346-1.231-1.476l-3.36-.702c-.707-.126-1.439,.075-2.01,.548-.571,.477-.898,1.176-.898,1.919v1.793c0,1.209,.863,2.243,2.053,2.46l5.447,1.076v-5.618Zm10.5,.289c0-.744-.327-1.443-.897-1.919-.57-.476-1.318-.674-2.05-.54l-3.282,.687c-.753,.137-1.271,.758-1.271,1.483v5.618l5.425-1.072c1.212-.221,2.075-1.255,2.075-2.464v-1.793Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Academic') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
              @if(empty($user->menu_permission) || in_array('admin.daily_attendance', $menu_permission))
              <li><a class="{{ (request()->is('admin/attendance*')) ? 'active' : '' }}" href="{{ route('admin.daily_attendance') }}"><span>
                {{ get_phrase('Daily Attendance') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.class_list', $menu_permission))
              <li><a class="{{ (request()->is('admin/class_list*')) ? 'active' : '' }}" href="{{ route('admin.class_list') }}"><span>
                {{ get_phrase('Class List') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.routine', $menu_permission))
              <li><a class="{{ (request()->is('admin/routine*')) ? 'active' : '' }}" href="{{ route('admin.routine') }}"><span>
                {{ get_phrase('Class Routine') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.subject_list', $menu_permission))
              <li><a class="{{ (request()->is('admin/subject*')) ? 'active' : '' }}" href="{{ route('admin.subject_list') }}"><span>
                {{ get_phrase('Subjects') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.gradebook', $menu_permission))
              <li><a class="{{ (request()->is('admin/gradebook*')) ? 'active' : '' }}" href="{{ route('admin.gradebook') }}"><span>
                {{ get_phrase('Gradebooks') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.syllabus', $menu_permission))
              <li><a class="{{ (request()->is('admin/syllabus*')) ? 'active' : '' }}" href="{{ route('admin.syllabus') }}"><span>
                {{ get_phrase('Syllabus') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.class_room_list', $menu_permission))
              <li><a class="{{ (request()->is('admin/class_room*')) ? 'active' : '' }}" href="{{ route('admin.class_room_list') }}"><span>
                {{ get_phrase('Class Room') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.department_list', $menu_permission))
              <li><a class="{{ (request()->is('admin/department*')) ? 'active' : '' }}" href="{{ route('admin.department_list') }}"><span>
                {{ get_phrase('Department') }}
              </span></a></li>
              @endif
            </ul>
        </li>
        @endif

        @if(empty($user->menu_permission) || in_array('admin.fee_manager.list', $menu_permission) || in_array('admin.offline_payment_pending', $menu_permission) || in_array('admin.expense.list', $menu_permission) || in_array('admin.expense.category_list', $menu_permission)) 
        <li class="nav-links-li {{ request()->is('admin/fee_manager*') || request()->is('admin/offline_payment/pending*')|| request()->is('admin/expense_category*') || request()->is('admin/expenses*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,10c-1.972-.034-1.971-2.967,0-3h1c1.972,.034,1.971,2.967,0,3h-1Zm-3.5,4.413c0-1.476-.885-2.783-2.255-3.331l-2.376-.95c-.591-.216-.411-1.15,.218-1.132h1.181c.181,0,.343,.094,.434,.251,.415,.717,1.334,.962,2.05,.547,.717-.415,.962-1.333,.548-2.049-.511-.883-1.381-1.492-2.363-1.684-.399-1.442-2.588-1.375-2.896,.091-3.161,.875-3.414,5.6-.285,6.762l2.376,.95c.591,.216,.411,1.15-.218,1.132h-1.181c-.181,0-.343-.094-.434-.25-.415-.717-1.334-.961-2.05-.547-.717,.415-.962,1.333-.548,2.049,.511,.883,1.381,1.491,2.363,1.683,.399,1.442,2.588,1.375,2.896-.091,1.469-.449,2.54-1.817,2.54-3.431ZM18.5,1H5.5C2.468,1,0,3.467,0,6.5v11c0,3.033,2.468,5.5,5.5,5.5h3c1.972-.034,1.971-2.967,0-3h-3c-1.379,0-2.5-1.122-2.5-2.5V6.5c0-1.378,1.121-2.5,2.5-2.5h13c1.379,0,2.5,1.122,2.5,2.5v2c.034,1.972,2.967,1.971,3,0v-2c0-3.033-2.468-5.5-5.5-5.5Zm-5.205,18.481c-.813,.813-1.269,1.915-1.269,3.064,.044,.422-.21,1.464,.5,1.455,1.446,.094,2.986-.171,4.019-1.269l6.715-6.715c2.194-2.202-.9-5.469-3.157-3.343l-6.808,6.808Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Accounting') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
              @if(empty($user->menu_permission) || in_array('admin.fee_manager.list', $menu_permission))
              <li><a class="{{ (request()->is('admin/fee_manager')) ? 'active' : '' }}" href="{{ route('admin.fee_manager.list') }}"><span>
                {{ get_phrase('Student Fee Manager') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.offline_payment_pending', $menu_permission))
              <li><a class="{{ (request()->is('admin/offline_payment/pending*')) ? 'active' : '' }}" href="{{ route('admin.offline_payment_pending') }}"><span>
                {{ get_phrase('Offline Payment Request') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.expense.list', $menu_permission))
              <li><a class="{{ (request()->is('admin/expenses*')) ? 'active' : '' }}" href="{{ route('admin.expense.list') }}"><span>
                {{ get_phrase('Expense Manager') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.expense.category_list', $menu_permission))
              <li><a class="{{ (request()->is('admin/expense_category*')) ? 'active' : '' }}" href="{{ route('admin.expense.category_list') }}"><span>
                {{ get_phrase('Expense Category') }}
              </span></a></li>
              @endif  
            </ul>
        </li>
        @endif

        @if(addon_status('online_courses')==1)
        <li class="nav-links-li {{ request()->is('admin/addons/courses*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a class="{{ (request()->is('admin/addons/courses*')) ? 'active' : '' }}" href="{{ route('admin.addons.courses') }}">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="48" height="48"><path d="M11,14H5a5.006,5.006,0,0,0-5,5v5H3V19a2,2,0,0,1,2-2h6a2,2,0,0,1,2,2v5h3V19A5.006,5.006,0,0,0,11,14Z"/><path d="M8,12A6,6,0,1,0,2,6,6.006,6.006,0,0,0,8,12ZM8,3A3,3,0,1,1,5,6,3,3,0,0,1,8,3Z"/><polygon points="21 10 21 7 18 7 18 10 15 10 15 13 18 13 18 16 21 16 21 13 24 13 24 10 21 10"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Online Courses') }}
                    </span>
                </a>
            </div>
        </li>
        @endif

        @if(addon_status('hr_management')==1)

        <?php

        $to=strtotime(date("m/d/Y"))+8600;

        $f= date('m/d/Y', strtotime("-31 days"));
        $form=strtotime(date('m/d/Y'),strtotime($f));
        ?>

        <li class="nav-links-li {{ request()->is('hr/leave_list*') || request()->is('payroll/create/payslip*') || request()->is('attendence/list*') || request()->is('payroll/list*') || request()->is('hr/user/roles*') || request()->is('hr/user/list*')  ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 14.991 17.164">
                            <path id="Exam_Icon" data-name="Exam Icon" d="M6.331,13.015h5.83v1.716H6.331Zm0-3.433H14.66V11.3H6.331Zm0-3.433H14.66V7.866H6.331Zm9.994-3.433H12.844a2.465,2.465,0,0,0-4.7,0H4.666a1.417,1.417,0,0,0-.333.034,1.659,1.659,0,0,0-.841.472,1.723,1.723,0,0,0-.358.549A1.7,1.7,0,0,0,3,4.433V16.448a1.766,1.766,0,0,0,.491,1.219,1.659,1.659,0,0,0,.841.472,2.1,2.1,0,0,0,.333.026h11.66a1.7,1.7,0,0,0,1.666-1.716V4.433A1.7,1.7,0,0,0,16.325,2.716ZM10.5,2.5a.644.644,0,1,1-.625.644A.639.639,0,0,1,10.5,2.5Zm5.83,13.946H4.666V4.433h11.66Z" transform="translate(-3 -1)" />
                        </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Human Resource') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">

                <li>
                    <a class="{{ (request()->is('hr/user/roles*')) ? 'active' : '' }}" href="{{ route('hr.user_role_index') }}">
                        <span>
                            {{ get_phrase('User Roles') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

                <li>
                    <a class="{{ (request()->is('hr/user/list*')) ? 'active' : '' }}" href="{{ route('hr.userlist_index') }}">
                        <span>
                            {{ get_phrase('User List') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>


                <li>
                    <a class="{{ (request()->is('attendence/list*')) ? 'active' : '' }}" href="{{ route('hr.list_of_attendence') }}">
                        <span>
                            {{ get_phrase('Take Attendence'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

                <li>
                    <a class="{{ (request()->is('hr/leave_list*')) ? 'active' : '' }}" href="{{ route('hr.list_of_leaves') }}">
                        <span>
                            {{ get_phrase('Leave'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>
                <li>
                    <a class="{{ (request()->is('payroll/list*')) ? 'active' : '' }}" href="{{ route('hr.list_of_payrolls') }}">
                        <span>
                            {{ get_phrase('Payroll'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

            </ul>
        </li>

        @endif

        @if (addon_status('inventory_manager') == 1)
        <li
            class="nav-links-li {{ request()->is('admin/inventory/list*') || request()->is('admin/category/list*') || request()->is('admin/buy_sell_inventory*') ? 'showMenu' : '' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                            viewBox="0 0 32 32" id="inventory">
                            <path d="M24 15V3H8v12H3v14h26V15h-5zM10 5h12v10H10V5zm17 22H5V17h22v10z"></path>
                            <path d="M12 9h8v1h-8zm0 2h8v1h-8zm7 11h-6v-1h-1v2h8v-2h-1z"></path>
                        </svg></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Inventory') }}
                    </span>
                </a>
                <span class="arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="4.743" height="7.773"
                        viewBox="0 0 4.743 7.773">
                        <path id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                            d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                            fill="#fff" opacity="1" />
                    </svg>
                </span>
            </div>
            <ul class="sub-menu">

                <li>
                    <a class="{{ request()->is('admin/inventory/list') ? 'active' : '' }}"
                        href="{{ route('admin.inventory.list') }}">
                        <span>
                            {{ get_phrase('Inventory Manager') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

                <li>
                    <a class="{{ request()->is('admin/category/list') ? 'active' : '' }}"
                        href="{{ route('admin.category.list') }}">
                        <span>
                            {{ get_phrase('Inventory Category') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>


                <li>
                    <a class="{{ request()->is('admin/buy_sell_inventory') ? 'active' : '' }}"
                        href="{{ route('admin.buy_sell_inventory') }}">
                        <span>
                            {{ get_phrase('Buy & Sell Report') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

            </ul>
        </li>
        @endif

        @if (addon_status('alumni_manager') == 1)
        <li
            class="nav-links-li {{ request()->is('admin/alumni*') || request()->is('admin/alumni/gallery_alumni*') || request()->is('admin/alumni/event_alumni*') ? 'showMenu' : '' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        viewBox="0 0 16 16" id="alumni"  class="bi bi-mortarboard mt-3">
                            <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5ZM8 8.46 1.758 5.965 8 3.052l6.242 2.913L8 8.46Z"/>
                          <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Zm-.068 1.873.22-.748 3.496 1.311a.5.5 0 0 0 .352 0l3.496-1.311.22.748L8 12.46l-3.892-1.556Z"/>
                        </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Alumni') }}
                    </span>
                    {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span>   --}}
                </a>
                <span class="arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="4.743" height="7.773"
                        viewBox="0 0 4.743 7.773">
                        <path id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                            d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                            fill="#fff" opacity="1" />
                    </svg>
                </span>
                
            </div>
            <ul class="sub-menu">

                <li>
                    <a class="{{ request()->is('admin/alumni/alumni_manager') ? 'active' : '' }}"
                        href="{{ route('admin.alumni.index') }}">
                        <span>
                            {{ get_phrase('Manage Alumni') }}
                        </span>
                        {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> --}}
                    </a>
                </li>

                <li>
                    <a class="{{ request()->is('admin/alumni/event_alumni') ? 'active' : '' }}"
                        href="{{ route('admin.alumni.eventindex') }}">
                        <span>
                            {{ get_phrase('Events') }}
                        </span>
                       {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> --}}
                    </a>
                </li>


                <li>
                    <a class="{{ request()->is('admin/alumni/gallery_alumni') ? 'active' : '' }}"
                        href="{{ route('admin.alumni.galleryindex') }}">
                        <span>
                            {{ get_phrase('Gallery') }}
                        </span>
                         {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> --}}
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if (addon_status('sms_center') == 1)
        <li
            class="nav-links-li {{ request()->is('admin/sms_center*') || request()->is('admin/sms_center/sms_settings*') || request()->is('admin/sms_center/sms_sender*') ? 'showMenu' : '' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send mt-3" viewBox="0 0 16 16">
                            <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                          </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Sms Center') }}
                    </span>
                    {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span>   --}}
                </a>
                <span class="arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="4.743" height="7.773"
                        viewBox="0 0 4.743 7.773">
                        <path id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                            d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                            fill="#fff" opacity="1" />
                    </svg>
                </span>

            </div>
            <ul class="sub-menu">
                <li>
                    <a class="{{ request()->is('admin/sms_center/sms_settings') ? 'active' : '' }}"
                        href="{{ route('admin.sms_center.index') }}">
                        <span>
                            {{ get_phrase('Sms Settings') }}
                        </span>
                        {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> --}}
                    </a>
                </li>
                <li>
                    <a class="{{ request()->is('admin/sms_center/sms_sender') ? 'active' : ''  }}"
                        href="{{ route('admin.sms_center.sms_sender') }}">
                        <span>
                            {{ get_phrase('Sms sender') }}
                        </span>
                        {{-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> --}}
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(addon_status('assignments')==1)
        <li class="nav-links-li {{ request()->is('admin/assignments*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="{{ route('admin.assignment_home', ['type' => 'published']) }}">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
                            <g><path d="m385 227.33c28.359 0 54.99 7.538 78 20.71v-232.21c0-8.284-6.716-15-15-15h-314c-8.284 0-15 6.716-15 15v391.004c0 8.284 6.716 15 15 15h97.902c-24.727-97.03 52.723-195.864 153.098-194.504zm-190.997-177.499h193.994c19.902.793 19.887 29.215 0 30h-193.994c-19.902-.793-19.887-29.215 0-30zm0 60h193.994c19.902.793 19.887 29.215 0 30h-193.994c-19.902-.793-19.887-29.215 0-30zm0 60h193.994c19.902.793 19.887 29.215 0 30h-193.994c-19.902-.793-19.887-29.215 0-30zm-15 75c0-8.284 6.716-15 15-15h47.729c19.902.793 19.887 29.215 0 30h-47.729c-8.284 0-15-6.716-15-15z"/><path d="m385 257.33c-70.304 0-127.5 57.196-127.5 127.5 7.004 169.146 248.022 169.097 255-.001 0-70.303-57.196-127.499-127.5-127.499zm58.891 100.745-56.962 72.779c-2.765 3.531-6.964 5.642-11.447 5.75-4.471.113-8.784-1.793-11.714-5.187l-30.616-35.424c-5.417-6.268-4.728-15.74 1.54-21.157 6.271-5.419 15.74-4.727 21.157 1.54l18.693 21.629 45.724-58.421c5.106-6.523 14.532-7.674 21.058-2.567 6.523 5.107 7.672 14.534 2.567 21.058z"/><path d="m0 137.83h90v184h-90z"/><path d="m45 49.83c-24.813 0-45 20.187-45 45v13h90v-13c0-24.813-20.187-45-45-45z"/><path d="m31.153 414.599c5.047 12.231 22.648 12.226 27.692 0l26.155-62.769h-80z"/></g>
                        </svg>
                    </div>
                    <span class="link_name">{{ get_phrase('Assignments') }}</span>
                </a>
            </div>
        </li>
        @endif 


        @if(empty($user->menu_permission) || in_array('admin.book.book_list', $menu_permission) || in_array('admin.book_issue.list', $menu_permission) || in_array('admin.noticeboard.list', $menu_permission) || in_array('admin.subscription', $menu_permission) || in_array('admin.events.list', $menu_permission) || in_array('admin.feedback.feedback_list', $menu_permission))
        <li class="nav-links-li {{ request()->is('admin/book*') || request()->is('admin/book_issue*') || request()->is('admin/noticeboard*') || request()->is('admin/subscription') || request()->is('admin/events/list*') || request()->is('admin/feedback-list*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="48" height="48"><path d="M18.5,3h-.642A4,4,0,0,0,14,0H10A4,4,0,0,0,6.142,3H5.5A5.506,5.506,0,0,0,0,8.5v10A5.506,5.506,0,0,0,5.5,24h13A5.507,5.507,0,0,0,24,18.5V8.5A5.507,5.507,0,0,0,18.5,3ZM5.5,6h13A2.5,2.5,0,0,1,21,8.5V11H3V8.5A2.5,2.5,0,0,1,5.5,6Zm13,15H5.5A2.5,2.5,0,0,1,3,18.5V14h7a2,2,0,0,0,2,2h0a2,2,0,0,0,2-2h7v4.5A2.5,2.5,0,0,1,18.5,21Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Back Office') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
              @if(empty($user->menu_permission) || in_array('admin.book.book_list', $menu_permission))
              <li><a class="{{ (request()->is('admin/book/list')) ? 'active' : '' }}" href="{{ route('admin.book.book_list') }}"><span>
                {{ get_phrase('Book List Manager') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.book_issue.list', $menu_permission))
              <li><a class="{{ (request()->is('admin/book_issue')) ? 'active' : '' }}" href="{{ route('admin.book_issue.list') }}"><span>
                {{ get_phrase('Book Issue Report') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.noticeboard.list', $menu_permission))
              <li><a class="{{ (request()->is('admin/noticeboard*')) ? 'active' : '' }}" href="{{ route('admin.noticeboard.list') }}"><span>
                {{ get_phrase('Noticeboard') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.subscription', $menu_permission))
              <li><a class="{{ (request()->is('admin/subscription')) ? 'active' : '' }}" href="{{ route('admin.subscription') }}"><span>
                {{ get_phrase('Subscription') }}
              </span></a></li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.events.list', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/events/list*')) ? 'active' : '' }}" href="{{ route('admin.events.list') }}"><span>{{ get_phrase('Events') }}
                </span></a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.feedback.feedback_list', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/feedback-list*')) ? 'active' : '' }}" href="{{ route('admin.feedback.feedback_list') }}"><span>{{ get_phrase('Feedback') }}
                </span></a>
              </li>
              @endif
            </ul>
        </li>
        @endif

        @if(empty($user->menu_permission) || in_array('admin.settings.school', $menu_permission) || in_array('admin.settings.session_manager', $menu_permission) || in_array('admin.settings.payment', $menu_permission) || in_array('admin.profile', $menu_permission))
        <li class="nav-links-li {{ request()->is('admin/settings/school*') || request()->is('admin/session_manager*') || request()->is('admin/settings/payment*') || request()->is('admin/live_class_settings*') || request()->is('admin/profile*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
                        <g>
                          <path d="M256,162.923c-51.405,0-93.077,41.672-93.077,93.077s41.672,93.077,93.077,93.077s93.077-41.672,93.077-93.077   C349.019,204.619,307.381,162.981,256,162.923z M256,285.077c-16.059,0-29.077-13.018-29.077-29.077s13.018-29.077,29.077-29.077   c16.059,0,29.077,13.018,29.077,29.077l0,0C285.066,272.054,272.054,285.066,256,285.077z"/>
                          <path d="M469.333,256c-0.032-32.689-7.633-64.927-22.208-94.187l10.965-7.616c14.496-10.104,18.058-30.046,7.957-44.544l0,0   c-10.104-14.496-30.046-18.058-44.544-7.957l-10.987,7.637c-32.574-34.38-75.691-56.904-122.517-64V32c0-17.673-14.327-32-32-32   l0,0c-17.673,0-32,14.327-32,32v13.333c-46.826,7.096-89.944,29.62-122.517,64l-10.987-7.637   c-14.498-10.101-34.44-6.538-44.544,7.957l0,0c-10.101,14.498-6.538,34.44,7.957,44.544l10.965,7.616   c-29.61,59.3-29.61,129.073,0,188.373l-10.965,7.616c-14.496,10.104-18.058,30.046-7.957,44.544l0,0   c10.104,14.496,30.046,18.058,44.544,7.957l10.987-7.637c32.574,34.38,75.691,56.904,122.517,64V480c0,17.673,14.327,32,32,32l0,0   c17.673,0,32-14.327,32-32v-13.333c46.826-7.096,89.944-29.62,122.517-64l10.987,7.637c14.498,10.101,34.44,6.538,44.544-7.957l0,0   c10.101-14.498,6.538-34.44-7.957-44.544l-10.965-7.616C461.7,320.927,469.301,288.689,469.333,256z M256,405.333   c-82.475,0-149.333-66.859-149.333-149.333S173.525,106.667,256,106.667S405.333,173.525,405.333,256   C405.228,338.431,338.431,405.228,256,405.333z"/>
                        </g>
                        </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Settings') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
              @if(empty($user->menu_permission) || in_array('admin.settings.school', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/settings/school*')) ? 'active' : '' }}" href="{{ route('admin.settings.school') }}">
                    <span>
                        {{ get_phrase('School Settings') }}
                    </span>
                </a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.settings.session_manager', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/session_manager*')) ? 'active' : '' }}" href="{{ route('admin.settings.session_manager') }}">
                    <span>
                        {{ get_phrase('Session Manager') }}
                    </span>
                </a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.settings.payment', $menu_permission))
              <li>
                <a class="{{ (request()->is('admin/settings/payment*')) ? 'active' : '' }}" href="{{ route('admin.settings.payment') }}">
                    <span>
                        {{ get_phrase('Payment Settings') }}
                    </span>
                </a>
              </li>
              @endif
              @if(empty($user->menu_permission) || in_array('admin.profile', $menu_permission))
              <li><a class="{{ (request()->is('admin/profile*')) ? 'active' : '' }}" href="{{ route('admin.profile') }}"><span>
                {{ get_phrase('My Account') }}</span></a>
              </li>
              @endif
            </ul>
        </li>
        @endif
      </ul>
    </div>

    <section class="home-section">
      <div class="home-content">
        <div class="home-header">
          <div class="row w-100 justify-content-between align-items-center">
            <div class="col-auto">
              <div class="sidebar_menu_icon">
                <div class="menuList">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="12"
                    viewBox="0 0 15 12"
                  >
                    <path
                      id="Union_5"
                      data-name="Union 5"
                      d="M-2188.5,52.5v-2h15v2Zm0-5v-2h15v2Zm0-5v-2h15v2Z"
                      transform="translate(2188.5 -40.5)"
                      fill="#6e6f78"
                    />
                  </svg>
                </div>
              </div>
            </div>

            <div class="col-auto d-xl-block d-none">
              <div class="header_notification d-flex align-items-center">
                <div class="notification_icon">
                  @php
                $school_data = App\Models\School::where('id', auth()->user()->school_id)->first();
            @endphp  
           
              @if(!empty($school_data->school_logo))
                <img class="" src="{{ asset('assets/uploads/school_logo/'.DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo') ) }}" width="30px" height="30px" style="border-radius: 50%; ">
              @else
                <img class="" src="{{ asset('assets') }}/images/id_logo.png" width="30px" height="30px">
              @endif
                  
                </div>
                <p>
                  {{DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}
                </p>
              </div>
            </div>
            <div class="col-auto d-flex ">
            <div class="message">
              @php
              $last_message = DB::table('message_thrades')
                      ->where(function ($query) {
                          $query->where('reciver_id', auth()->user()->id)
                                ->orWhere('sender_id', auth()->user()->id);
                      })
                      ->orderBy('id', 'desc')
                      ->first(); 
                      
                      $countUnreadThreads = DB::table('chats')
                          ->where('read_status', 0)
                          ->where('reciver_id', auth()->user()->id)
                          ->distinct('message_thrade')
                          ->count('message_thrade');

                      
                @endphp
                @if(!empty($last_message))
                  <a href="{{route('admin.message.all_message', ['id' => $last_message->id])}}" class="message_ico">
                      <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                          <path fill-rule="evenodd" d="M4 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h1v2a1 1 0 0 0 1.707.707L9.414 13H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4Z" clip-rule="evenodd"/>
                          <path fill-rule="evenodd" d="M8.023 17.215c.033-.03.066-.062.098-.094L10.243 15H15a3 3 0 0 0 3-3V8h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1v2a1 1 0 0 1-1.707.707L14.586 18H9a1 1 0 0 1-.977-.785Z" clip-rule="evenodd"/>
                      </svg>
                    @if($countUnreadThreads != 0)
                      <div class="countUnread">
                          <span class="countUnreadThreads">{{$countUnreadThreads}}</span>
                      </div>
                      @endif
                  </a>
                @else
                  <a href="{{route('admin.message.chat_empty')}}" class="message_ico">
                      <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                          <path fill-rule="evenodd" d="M4 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h1v2a1 1 0 0 0 1.707.707L9.414 13H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4Z" clip-rule="evenodd"/>
                          <path fill-rule="evenodd" d="M8.023 17.215c.033-.03.066-.062.098-.094L10.243 15H15a3 3 0 0 0 3-3V8h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1v2a1 1 0 0 1-1.707.707L14.586 18H9a1 1 0 0 1-.977-.785Z" clip-rule="evenodd"/>
                      </svg>
                  </a>
                @endif
            </div>
              @php
                $all_languages = get_all_language();
                $usersinfo = DB::table('users')->where('id', auth()->user()->id)->first();

                $userlanguage = $usersinfo->language;
                
              @endphp
              <div class="adminTable-action" style="margin-right: 20px; margin-top: 14px;">
                <button
                  type="button"
                  class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  style="width: 91px; height: 29px; padding: 0;"
                >
                   <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" class="ep0rzf NMm5M" style="width: 17px"><path d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0 0 14.07 6H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z"></path></svg>
                   
                    @if(!empty($userlanguage))
                   <span style="font-size: 10px;">{{ucwords($userlanguage)}}</span>
                   @else
                   <span style="font-size: 10px;">{{ucwords(get_settings('language'))}}</span>
                   @endif
                </button>
                
                <ul style="min-width: 0;" class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                  <form method="post" id="languageForm" action="{{ route('admin.language') }}">
                    @csrf
                    @foreach ($all_languages as $all_language)
                        <li>
                            <a class="dropdown-item language-item" href="javascript:;" data-language-name="{{ $all_language->name }}">{{ ucwords($all_language->name) }}</a>
                        </li>
                    @endforeach
                    <input type="hidden" name="language" id="selectedLanguageName">
                </form>
                </ul>
              </div>
              <div class="header-menu">
                <ul>
                  <li class="user-profile">
                    <div class="btn-group">
                      <button
                        class="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="defaultDropdown"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="true"
                        aria-expanded="false"
                      >
                        <div class="">
                          <img src="{{ get_user_image(auth()->user()->id) }}" height="42px" />
                        </div>
                        <div class="px-2 text-start">
                          <span class="user-name">{{ auth()->user()->name }}</span>
                          <span class="user-title">{{ get_phrase('Admin') }}</span>
                        </div>
                      </button>
                      <ul
                        class="dropdown-menu dropdown-menu-end eDropdown-menu"
                        aria-labelledby="defaultDropdown"
                      >
                        <li class="user-profile user-profile-inner">
                          <button
                            class="btn w-100 d-flex align-items-center"
                            type="button"
                          >
                            <div class="">
                              <img
                                class="radious-5px"
                                src="{{ get_user_image(auth()->user()->id) }}"
                                height="42px"
                              />
                            </div>
                            <div class="px-2 text-start">
                              <span class="user-name">{{ auth()->user()->name }}</span>
                              <span class="user-title">{{ get_phrase('Admin') }}</span>
                            </div>
                          </button>
                        </li>

                        <li>
                          <a class="dropdown-item" href="{{route('admin.profile')}}">
                            <span>
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="13.275"
                                height="14.944"
                                viewBox="0 0 13.275 14.944"
                              >
                                <g
                                  id="user_icon"
                                  data-name="user icon"
                                  transform="translate(-1368.531 -147.15)"
                                >
                                  <g
                                    id="Ellipse_1"
                                    data-name="Ellipse 1"
                                    transform="translate(1370.609 147.15)"
                                    fill="none"
                                    stroke="#181c32"
                                    stroke-width="2"
                                  >
                                    <ellipse
                                      cx="4.576"
                                      cy="4.435"
                                      rx="4.576"
                                      ry="4.435"
                                      stroke="none"
                                    />
                                    <ellipse
                                      cx="4.576"
                                      cy="4.435"
                                      rx="3.576"
                                      ry="3.435"
                                      fill="none"
                                    />
                                  </g>
                                  <path
                                    id="Path_41"
                                    data-name="Path 41"
                                    d="M1485.186,311.087a5.818,5.818,0,0,1,5.856-4.283,5.534,5.534,0,0,1,5.466,4.283"
                                    transform="translate(-115.686 -149.241)"
                                    fill="none"
                                    stroke="#181c32"
                                    stroke-width="2"
                                  />
                                </g>
                              </svg>
                            </span>
                            {{ get_phrase('My Account') }}
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{route('admin.password', ['edit'])}}">
                            <span>
                              <svg id="Layer_1" width="13.275" height="14.944" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m6.5 16a1.5 1.5 0 1 1 -1.5 1.5 1.5 1.5 0 0 1 1.5-1.5zm3 7.861a7.939 7.939 0 0 0 6.065-5.261 7.8 7.8 0 0 0 .32-3.85l.681-.689a1.5 1.5 0 0 0 .434-1.061v-2h.5a2.5 2.5 0 0 0 2.5-2.5v-.5h1.251a2.512 2.512 0 0 0 2.307-1.52 5.323 5.323 0 0 0 .416-2.635 4.317 4.317 0 0 0 -4.345-3.845 5.467 5.467 0 0 0 -3.891 1.612l-6.5 6.5a7.776 7.776 0 0 0 -3.84.326 8 8 0 0 0 2.627 15.562 8.131 8.131 0 0 0 1.475-.139zm-.185-12.661a1.5 1.5 0 0 0 1.463-.385l7.081-7.08a2.487 2.487 0 0 1 1.77-.735 1.342 1.342 0 0 1 1.36 1.149 2.2 2.2 0 0 1 -.08.851h-1.409a2.5 2.5 0 0 0 -2.5 2.5v.5h-.5a2.5 2.5 0 0 0 -2.5 2.5v1.884l-.822.831a1.5 1.5 0 0 0 -.378 1.459 4.923 4.923 0 0 1 -.074 2.955 5 5 0 1 1 -6.36-6.352 4.9 4.9 0 0 1 1.592-.268 5.053 5.053 0 0 1 1.357.191z"/></svg>
                            </span>
                            {{ get_phrase('Change Password') }}
                          </a>
                        </li>
                        
                        <li>
                          <hr class="my-0">
                        </li>
                
                        <!-- Logout Button -->
                        <li>
                            <a class="btn eLogut_btn" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <span>
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="14.046"
                                    height="12.29"
                                    viewBox="0 0 14.046 12.29"
                                  >
                                    <path
                                      id="Logout"
                                      d="M4.389,42.535H2.634a.878.878,0,0,1-.878-.878V34.634a.878.878,0,0,1,.878-.878H4.389a.878.878,0,0,0,0-1.756H2.634A2.634,2.634,0,0,0,0,34.634v7.023A2.634,2.634,0,0,0,2.634,44.29H4.389a.878.878,0,1,0,0-1.756Zm9.4-5.009-3.512-3.512a.878.878,0,0,0-1.241,1.241l2.015,2.012H5.267a.878.878,0,0,0,0,1.756H11.05L9.037,41.036a.878.878,0,1,0,1.241,1.241l3.512-3.512A.879.879,0,0,0,13.788,37.525Z"
                                      transform="translate(0 -32)"
                                      fill="#fff"
                                    />
                                  </svg>
                                </span>
                                {{ get_phrase('Log out') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="main_content">
            @yield('content')
            <!-- Start Footer -->
            <div class="copyright-text">
              <?php $active_session = DB::table('sessions')->where('id',  get_settings('running_session'))->value('session_title'); ?>
                <p>{{ $active_session }} &copy; <span><a class="text-info" target="_blank" href="{{ get_settings('footer_link') }}">{{ get_settings('footer_text') }}</a></span></p>
            </div>
            <!-- End Footer -->
        </div>
      </div>
      @include('modal')
    </section>

    @include('external_plugin')
    @include('jquery-form')


    <!--Main Jquery-->
    <!--Bootstrap bundle with popper-->
    <script src="{{ asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <!-- Datepicker js -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <!--Custom Script-->
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <!-- Calender js -->
    <script src="{{ asset('assets/calender/main.js') }}"></script>
    <script src="{{ asset('assets/calender/locales-all.js') }}"></script>

    <!-- Dragula js -->
    <script src="{{ asset('assets/js/dragula.min.js') }}"></script>
    <script src="{{ asset('assets/js/onDomChange.js') }}"></script>
    <script src="{{ asset('assets/js/Sortable.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-sortable.js') }}"></script>
    <!-- SummerNote Js -->
    <script src="{{ asset('assets/js/summernote-lite.min.js') }}"></script>

    <!--Toaster Script-->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <!--pdf Script-->
    <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>

    <!--html2canvas Script-->
    <script src="{{ asset('assets/js/html2canvas.min.js') }}"></script>
    <!---Image Gallery--->
    <script src="/ekattor8_v9/Ekattor8/public/assets/js/lightbox-plus-jquery.js"></script>
    <script>

        // JavaScript to handle language selection
  document.addEventListener('DOMContentLoaded', function() {
        let languageLinks = document.querySelectorAll('.language-item');
        
        languageLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let languageName = this.getAttribute('data-language-name');
                document.getElementById('selectedLanguageName').value = languageName;
                document.getElementById('languageForm').submit();
            });
        });
    });


        "use strict";
        
        @if(Session::has('message'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.success("{{ session('message') }}");
		@endif

		@if(Session::has('error'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.error("{{ session('error') }}");
		@endif

		@if(Session::has('info'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.info("{{ session('info') }}");
		@endif

		@if(Session::has('warning'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.warning("{{ session('warning') }}");
		@endif
    </script>

    <script>

        "use strict";
        
        jQuery(document).ready(function(){
        $('input[name="datetimes"]').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('day').subtract(30, 'day'),
            endDate: moment().startOf('day'),
            locale: {
           format: 'M/DD/YYYY '
          }

        });
    });



    </script>

</body>

</html>
