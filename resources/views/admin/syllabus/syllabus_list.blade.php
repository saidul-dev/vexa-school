<?php 

use App\Models\Session;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\Section;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

?>

@extends('admin.navigation')
   
@section('content')

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Syllabus') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Academic') }}</a></li>
              <li><a href="#">{{ get_phrase('Syllabus') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.syllabus.open_modal') }}', '{{ get_phrase('Create syllabus') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add syllabus') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">

            <form method="GET" class="d-block ajaxForm" action="{{ route('admin.syllabus.syllabus_list') }}">

                <div class="row mt-3">

                    <div class="col-md-3"></div>

                    <div class="col-md-2">
                        <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                            <option value="">{{ get_phrase('Select a class') }}</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $class_id == $class->id ?  'selected':'' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="section_id" class="eForm-label">{{ get_phrase('Section') }}</label>
                        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                           <?php $sections = Section::where(['class_id' => $class_id])->get(); ?>
                            <?php foreach($sections as $section): ?>
                                <option value="{{ $section->id }}" {{ $section_id == $section->id ?  'selected':'' }}>{{ $section->name }}</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 pt-2">
                        <button class="eBtn eBtn btn-secondary mt-4" type="submit" id = "filter_routine">{{ get_phrase('Filter') }}</button>
                    </div>

                </div>
            </form>

            <?php $syllabuses = Syllabus::where(['class_id' => $class_id, 'section_id' => $section_id, 'session_id' => $active_session,'school_id' => auth()->user()->school_id])->get(); ?>
            @if(count($syllabuses) > 0)
            <div class="table-responsive tScrollFix pb-2 class_routine_content">
                <table id="basic-datatable" class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Title') }}</th>
                            <th>{{ get_phrase('Syllabus') }}</th>
                            <th>{{ get_phrase('Subject') }}</th>
                            <th class="text-end">{{ get_phrase('Option') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($syllabuses as $syllabus):?>
                            <tr>
                                <td>{{ $syllabus['title'] }}</td>
                                <td><a href="{{ asset('assets/uploads/syllabus') }}/{{ $syllabus['file'] }}" class="btn btn-primary btn-sm bi bi-download" download>{{ get_phrase(' Download') }}</a></td>
                                <td>
                                    <?php $subjects = Subject::find($syllabus['subject_id']); ?>
                                    {{ $subjects->name }}
                                </td>
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
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.syllabus_edit_modal', ['id' => $syllabus['id']]) }}', '{{ get_phrase('Edit syllabus') }}')">{{ get_phrase('Edit') }}</a>
                                          </li>
                                          <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.syllabus.delete', ['id' => $syllabus['id']]) }}', 'undefined')">{{ get_phrase('Delete') }}</a>
                                          </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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

<script type="text/javascript">

  "use strict";


    function classWiseSection(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
            }
        });
    }

    function filter_class_syllabus(){
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        if(class_id != "" && section_id!= ""){
            getFilteredClassSyllabus();
        }else{
            toastr.error('{{ get_phrase('Please select a class and section') }}');
        }
    }

    var getFilteredClassSyllabus = function() {
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        if(class_id != "" && section_id!= ""){
            let url = "{{ route('admin.syllabus.syllabus_list') }}";
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {class_id : class_id, section_id : section_id},
                success: function(response){
                    $('.class_routine_content').html(response);
                }
            });
        }
    }

</script>