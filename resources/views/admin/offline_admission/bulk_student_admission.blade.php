<form method="POST" class="col-md-12 ajaxForm" action="{{ route('admin.offline_admission.bulk_create') }}" id = "student_admission_form" enctype="multipart/form-data">
    @csrf 
    <div class="row justify-content-md-center">
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3 mb-lg-0">
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value)" required>
                <option value="">{{ get_phrase('Select a class') }}</option>
                @foreach($data['classes'] as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3 mb-lg-0" id = "section_content">
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value="">{{ get_phrase('Select section') }}</option>
            </select>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3 mb-lg-0">
            <select name="department_id" id="department_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select a department') }}</option>
                @foreach($data['departments'] as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <br>
    <div id = "first-row">
        <div class="row">
            <div class="col-xl-11 col-lg-11 col-md-12 col-sm-12 mb-3 mb-lg-0">
                <div class="row justify-content-md-center">
                    <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                        <input type="text" name="name[]" class="form-control eForm-control"  value="" placeholder="Name" required>
                    </div>
                    <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                        <input type="email" name="email[]" class="form-control eForm-control"  value="" placeholder="Email" required>
                    </div>
                    <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                        <input type="password" name="password[]" class="form-control eForm-control"  value="" placeholder="Password" required>
                    </div>
                    <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                        <select name="gender[]" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value="">{{ get_phrase('Select gender') }}</option>
                            <option value="Male">{{ get_phrase('Male') }}</option>
                            <option value="Female">{{ get_phrase('Female') }}</option>
                            <option value="Others">{{ get_phrase('Others') }}</option>
                        </select>
                    </div>

                    <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                        <select name="parent_id[]" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value="">{{ get_phrase('Select a parent') }}</option>
                            @foreach($data['parents'] as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>
            </div>
            <div class="col-xl-1 col-lg-1 col-md-12 col-sm-12 mb-3 mb-lg-0">
                <div class="row justify-content-md-center">
                    <div class="form-group col">
                        <button type="button" class="btn btn-icon btn-success" onclick="appendRow()"> <i class="bi bi-plus"></i> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-secondary col-md-4 col-sm-12 mb-4 mt-2">{{ get_phrase('Add students') }}</button>
    </div>
</form>

<div class="display-none-view" id = "blank-row ">
    <div class="row student-row pt-3">
        <div class="col-xl-11 col-lg-11 col-md-12 col-sm-12 mb-3 mb-lg-0">
            <div class="row justify-content-md-center">
                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <input type="text" name="name[]" class="form-control eForm-control"  value="">
                </div>
                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <input type="email" name="email[]" class="form-control eForm-control"  value="">
                </div>
                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <input type="password" name="password[]" class="form-control eForm-control"  value="" placeholder="Password">
                </div>

                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <select name="gender[]" class="form-control eForm-control">
                        <option value="">{{ get_phrase('Select gender') }}</option>
                        <option value="Male">{{ get_phrase('Male') }}</option>
                        <option value="Female">{{ get_phrase('Female') }}</option>
                        <option value="Others">{{ get_phrase('Others') }}</option>
                    </select>
                </div>

                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <select name="parent_id[]" class="form-control eForm-control" required>
                        <option value="">{{ get_phrase('Select a parent') }}</option>
                        @foreach($data['parents'] as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xl-1 col-lg-1 col-md-12 col-sm-12 mb-3 mb-lg-0">
            <div class="row justify-content-md-center">
                <div class="form-group col">
                    <button type="button" class="btn btn-icon btn-danger" onclick="removeRow(this)"> <i class="bi bi-x"></i> </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  
    "use strict";
    
    var blank_field = $('#blank-row').html();

    function appendRow() {
        $('#first-row').append(blank_field);
    }

    function removeRow(elem) {
        $(elem).closest('.student-row').remove();
    }

    
    var form;
    $(".ajaxForm").submit(function(e) {
        form = $(this);
        ajaxSubmit(e, form, refreshForm);
    });
    var refreshForm = function () {
        form.trigger("reset");
    }
</script>