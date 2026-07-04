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
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="section_id"class="eForm-label">{{ get_phrase('Section') }}</label>
                        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                            <option value="">{{ get_phrase('First select a class') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3 pt-2">
                        <button class="eBtn eBtn btn-secondary mt-4" type="submit" id = "filter_routine">{{ get_phrase('Filter') }}</button>
                    </div>

                    <div class="card-body class_routine_content">
                        <div class="empty_box center">
                            <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        </div>
                    </div>

                </div>
            </form>
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