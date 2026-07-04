@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Class Rooms') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Academic') }}</a></li>
              <li><a href="#">{{ get_phrase('Class Rooms') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.class_room.open_modal') }}', '{{ get_phrase('Create Class Room') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add class room') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-7 offset-md-2">
        <div class="eSection-wrap">
            @if(count($class_rooms) > 0)
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Name') }}</th>
                            <th class="text-end">{{ get_phrase('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($class_rooms as $key => $class_room)
                             <tr>
                                <td>{{ $class_rooms->firstItem() + $key }}</td>
                                <td>{{ $class_room->name }}</td>
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
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.class_room', ['id' => $class_room->id]) }}', '{{ get_phrase('Edit Class Room') }}')">{{ get_phrase('Edit') }}</a>
                                          </li>
                                          <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.class_room.delete', ['id' => $class_room->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                          </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $class_rooms->appends(request()->all())->links() !!}
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