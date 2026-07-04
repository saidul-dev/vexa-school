@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Grades') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Examknation') }}</a></li>
              <li><a href="#">{{ get_phrase('Grades') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.grade.open_modal') }}', '{{ get_phrase('Create Grade') }}')">{{ get_phrase('Add grade') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            @if(count($grades) > 0)
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ get_phrase('Grade') }}</th>
                            <th scope="col">{{ get_phrase('Grade Point') }}</th>
                            <th scope="col">{{ get_phrase('Mark From') }}</th>
                            <th scope="col">{{ get_phrase('Mark Upto') }}</th>
                            <th scope="col">{{ get_phrase('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grades as $grade)
                             <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $grade->name }}</td>
                                <td>{{ $grade->grade_point }}</td>
                                <td>{{ $grade->mark_from }}</td>
                                <td>{{ $grade->mark_upto }}</td>
                                <td class="text-start">
                                    <div class="adminTable-action ms-0">
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
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.grade', ['id' => $grade->id]) }}', '{{ get_phrase('Edit Grade') }}')">{{ get_phrase('Edit') }}</a>
                                          </li>
                                          <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.grade.delete', ['id' => $grade->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                          </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
@endsection