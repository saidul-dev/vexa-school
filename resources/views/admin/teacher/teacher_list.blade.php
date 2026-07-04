@extends('admin.navigation')
   
@section('content')

<?php use App\Models\School; ?>

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Teachers') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Users') }}</a></li>
              <li><a href="#">{{ get_phrase('Teacher') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.teacher.open_modal') }}', '{{ get_phrase('Create Teacher') }}')">{{ get_phrase('Create Teacher') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- Start Teacher area -->
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
              <form action="{{ route('admin.teacher') }}">
                <div
                  class="search-input d-flex justify-content-start align-items-center"
                >
                  <span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 16 16"
                    >
                      <path
                        id="Search_icon"
                        data-name="Search icon"
                        d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                        fill="#797c8b"
                      />
                    </svg>
                  </span>
                  <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search Teacher"
                    class="form-control"
                  />
                </div>
              </form>
              <!-- Export Button -->
              @if(count($teachers) > 0)
              <div class="position-relative">
                <button
                  class="eBtn-3 dropdown-toggle"
                  type="button"
                  id="defaultDropdown"
                  data-bs-toggle="dropdown"
                  data-bs-auto-close="true"
                  aria-expanded="false"
                >
                  <span class="pr-10">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="12.31"
                      height="10.77"
                      viewBox="0 0 10.771 12.31"
                    >
                      <path
                        id="arrow-right-from-bracket-solid"
                        d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                        transform="translate(0 12.31) rotate(-90)"
                        fill="#00a3ff"
                      />
                    </svg>
                  </span>
                  {{ get_phrase('Export') }}
                </button>
                <ul
                  class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
                >
                  <li>
                      <a class="dropdown-item" id="pdf" href="javascript:;" onclick="Export()">{{ get_phrase('PDF') }}</a>
                  </li>
                  <li>
                      <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('teacher_list')">{{ get_phrase('Print') }}</a>
                  </li>
                </ul>
              </div>
              @endif
            </div>
            @if(count($teachers) > 0)
            <!-- Table -->
            <div class="table-responsive">
              <table class="table eTable eTable-2">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ get_phrase('Name') }}</th>
                    <th scope="col">{{ get_phrase('Email') }}</th>
                    <th scope="col">{{ get_phrase('User Info') }}</th>
                    <th scope="col">{{ get_phrase('Account Status') }}</th>
                    <th scope="col">{{ get_phrase('Options') }}</th>
                </thead>
                <tbody>
                    @foreach($teachers as $key => $teacher)
                    <?php 
                        $info = json_decode($teacher->user_information);
                        $user_image = $info->photo;
                        if(!empty($info->photo)){
                            $user_image = 'uploads/user-images/'.$info->photo;
                        }else{
                            $user_image = 'uploads/user-images/thumbnail.png';
                        }
                    ?>
                      <tr>
                        <th scope="row">
                          <p class="row-number">{{ $teachers->firstItem() + $key }}</p>
                        </th>
                        <td>
                          <div
                            class="dAdmin_profile d-flex align-items-center min-w-200px"
                          >
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="{{ asset('assets') }}/{{ $user_image }}"
                              />
                            </div>
                            <div class="dAdmin_profile_name">
                              <h4>{{ $teacher->name }}</h4>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p>{{ $teacher->email }}</p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone }}</p>
                            <p>
                              <span>{{ get_phrase('Address') }}:</span> {{ $info->address }}
                            </p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-100px">
                            @if(!empty($teacher->account_status == 'disable'))
                            <span class="eBadge ebg-soft-danger">{{get_phrase('Disabled')}}</span>
                            @else
                            <span class="eBadge ebg-soft-success">{{get_phrase('Enable')}}</span>
                            @endif
                          </div>
                        </td>
                        <td>
                          <div class="adminTable-action">
                            <button
                              type="button"
                              class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                              data-bs-toggle="dropdown"
                              aria-expanded="false"
                            >
                              {{ get_phrase('Actions') }}
                            </button>
                            <ul
                              class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                            >
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.teacher_edit_modal', ['id' => $teacher->id]) }}', '{{ get_phrase('Edit Admin') }}')">{{ get_phrase('Edit') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.teacher.delete', ['id' => $teacher->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('{{ route('admin.teacher.teacher_profile', ['id' => $teacher->id]) }}','{{ get_phrase('Teacher Profile') }}')">{{ get_phrase('Profile') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="{{ route('admin.teacher.documents', ['id' => $teacher->id]) }}">{{ get_phrase('Documents') }}</a>
                              </li>
                              @if(!empty($teacher->account_status == 'disable'))
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_enable', ['id' => $teacher->id]) }}', 'undefined');">{{ get_phrase('Enable') }}</a>
                              </li>
                              @else
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_disable', ['id' => $teacher->id]) }}', 'undefined');">{{ get_phrase('Disable') }}</a>
                              </li>
                              @endif
                            </ul>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
              {!! $teachers->appends(request()->all())->links() !!}
            </div>
            @else
            <div class="empty_box center">
              <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
              <br>
              <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

@if(count($teachers) > 0)
<!-- Table -->
<div class="table-responsive teacher_list display-none-view" id="teacher_list">
  <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">{{ get_phrase('Teachers List') }}</h4>
  <table class="table eTable eTable-2">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">{{ get_phrase('Name') }}</th>
        <th scope="col">{{ get_phrase('Email') }}</th>
        <th scope="col">{{ get_phrase('User Info') }}</th>
    </thead>
    <tbody>
        @foreach($teachers as $key => $teacher)
        <?php 
            $info = json_decode($teacher->user_information);
            $user_image = $info->photo;
            if(!empty($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            }
        ?>
          <tr>
            <th scope="row">
              <p class="row-number">{{ $teachers->firstItem() + $key }}</p>
            </th>
            <td>
              <div
                class="dAdmin_profile d-flex align-items-center min-w-200px"
              >
                <div class="dAdmin_profile_img">
                  <img
                    class="img-fluid"
                    width="50"
                    height="50"
                    src="{{ asset('assets') }}/{{ $user_image }}"
                  />
                </div>
                <div class="dAdmin_profile_name">
                  <h4>{{ $teacher->name }}</h4>
                </div>
              </div>
            </td>
            <td>
              <div class="dAdmin_info_name min-w-250px">
                <p>{{ $teacher->email }}</p>
              </div>
            </td>
            <td>
              <div class="dAdmin_info_name min-w-250px">
                <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone }}</p>
                <p>
                  <span>{{ get_phrase('Address') }}:</span> {{ $info->address }}
                </p>
              </div>
            </td>
          </tr>
        @endforeach
    </tbody>
  </table>
  {!! $teachers->appends(request()->all())->links() !!}
</div>
@endif

<script type="text/javascript">

  "use strict";

  function Export() {

      // Choose the element that our invoice is rendered in.
      const element = document.getElementById("teacher_list");

      // clone the element
      var clonedElement = element.cloneNode(true);

      // change display of cloned element
      $(clonedElement).css("display", "block");

      // Choose the clonedElement and save the PDF for our user.
    var opt = {
      margin:       1,
      filename:     'teacher_list_{{ date("y-m-d") }}.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 }
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(clonedElement).save();

      // remove cloned element
      clonedElement.remove();
  }

  function printableDiv(printableAreaDivId) {
    var printContents = document.getElementById(printableAreaDivId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
  }

</script>

<!-- End Teacher area -->
@endsection