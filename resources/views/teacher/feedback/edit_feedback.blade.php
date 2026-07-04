@php
use App\Http\Controllers\CommonController;
use App\Models\User;
use App\Models\Section;
    $student_details = (new CommonController)->get_student_academic_info($feedback->student_id);
    $parent_details = User::find($student_details->parent_id);
@endphp
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('teacher.feedback.update_feedback', ['id' => $feedback->id]) }}">
    @csrf 
    <div class="fpb-7">
        <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
            <option value="">{{ get_phrase('Select a class') }}</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ $student_details['class_id'] == $class->id ?  'selected':'' }}>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="fpb-7">
        <label for="section_id" class="eForm-label">{{ get_phrase('Section') }}</label>
        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="sectionWiseStudent(this.value)">
            <?php if($student_details['section_id'] != "") {
                $sections = Section::get()->where('class_id', $student_details['class_id']); ?>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ $student_details['section_id'] == $section->id ?  'selected':'' }}>{{ $section->name }}</option>
                @endforeach
            <?php } else { ?>
                <option value="">{{ get_phrase('First select a class') }}</option>
            <?php } ?>
        </select>
    </div>

    <div class="fpb-7">
        <label for="student_id" class="eForm-label">{{ get_phrase('Students') }}</label>
        <select name="student_id" id="student_id_1" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="studentWiseParent(this.value)">
            <option value="{{ $feedback->student_id }}" {{ $feedback->student_id == $student_details->id ?  'selected':'' }}>{{ $student_details->name }}</option>
        </select>
    </div>

    <div class="fpb-7">
        <label for="parent_id" class="eForm-label">{{ get_phrase('Parent') }}</label>
        <select name="parent_id" id="parent_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
            <option value="{{ $feedback->parent_id }}" {{ $feedback->parent_id == $parent_details->id ?  'selected':'' }}>{{ $parent_details->name }}</option>
        </select>
    </div>

    <div class="fpb-7">
        <label for="title" class="eForm-label">{{ get_phrase('Title') }}</label>
        <input type="text" class="form-control eForm-control" id="title" value="{{ $feedback->title }}" name = "title" required>
    </div>

    <div class="fpb-7">
        <label for="feedback_text" class="eForm-label">{{ get_phrase('Write Feedback') }}</label>
        <textarea class="form-control eForm-control" id="feedback_text" name="feedback_text" required>{{ $feedback->feedback_text }}</textarea>
    </div>

    <div class="fpb-7 pt-2">
        <button type="submit" class="btn-form">{{ get_phrase('Update Feedback') }}</button>
    </div>
</form>

<script type="text/javascript">
    "use strict";


    function classWiseSection(classId) {
        let url = "{{ route('class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
            }
        });
    }

    function sectionWiseStudent(sectionId) {
        let url = "{{ route('section_wise_students', ['id' => ":sectionId"]) }}";
        url = url.replace(":sectionId", sectionId);
        $.ajax({
            url: url,
            success: function(response){
                $('#student_id_1').html(response);
            }
        });
    }

    function studentWiseParent(studentId) {
        let url = "{{ route('student_wise_parent', ['id' => ":studentId"]) }}";
        url = url.replace(":studentId", studentId);
        $.ajax({
            url: url,
            success: function(response){
                $('#parent_id').html(response);
            }
        });
    }


</script>
