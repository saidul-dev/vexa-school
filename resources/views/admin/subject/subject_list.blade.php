<?php use App\Models\Classes; ?>

@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Subjects') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Academic') }}</a></li>
              <li><a href="#">{{ get_phrase('Subjects') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.subject.open_modal') }}', '{{ get_phrase('Create Subject') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add subject') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8 col-12 offset-md-2">
        <div class="eSection-wrap">
            <form method="GET" class="d-block ajaxForm" action="{{ route('admin.subject_list') }}">
                <div class="row mt-3 d-flex justify-content-between">
                    
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                            <option value="">{{ get_phrase('Select a class') }}</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $class_id == $class->id ?  'selected':'' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 d-flex justify-content-end">
                        <button class="eBtn eBtn btn-secondary" type="submit" id = "filter_routine">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>

            @if(count($subjects) > 0)
            <div class="table-responsive">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Name') }}</th>
                            <th>{{ get_phrase('Class') }}</th>
                            <th class="text-end">{{ get_phrase('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $key => $subject)
                            <?php $class = Classes::get()->where('id', $subject->class_id)->first(); ?>
                             <tr>
                                <td>{{ $subjects->firstItem() + $key }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $class->name }}</td>
                                <td class="text-start">
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
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.subject', ['id' => $subject->id]) }}', '{{ get_phrase('Edit Subject') }}')">{{ get_phrase('Edit') }}</a>
                                          </li>
                                          <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.subject.delete', ['id' => $subject->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                          </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $subjects->appends(request()->all())->links() !!}
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