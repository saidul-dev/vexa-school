<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('teacher.feedback.upload_feedback') }}">
    @csrf 
    <div class="fpb-7">
        <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
            <option value="">{{ get_phrase('Select a class') }}</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="fpb-7">
        <label for="section_id" class="eForm-label">{{ get_phrase('Section') }}</label>
        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="sectionWiseStudent(this.value)" >
            <option value="">{{ get_phrase('Select section') }}</option>
        </select>
    </div>

    <div class="fpb-7">
        <label for="student_id[]" class="eForm-label">{{ get_phrase('Students') }}</label>
        <select name="student_id[]" id="student_id_1" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="studentWiseParent(this.value)">
            <option value="">{{ get_phrase('Select Students') }}</option>
        </select>
    </div>

    <div class="fpb-7">
        <label for="parent_id[]" class="eForm-label">{{ get_phrase('Parent') }}</label>
        <select name="parent_id[]" id="parent_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
            <option value="">{{ get_phrase('Select Parent') }}</option>
        </select>
    </div>

    <div class="fpb-7">
        <label for="title" class="eForm-label">{{ get_phrase('Title') }}</label>
        <input type="text" class="form-control eForm-control" id="title" name = "title" required>
    </div>

    <div class="fpb-7">
        <label for="feedback_text" class="eForm-label">{{ get_phrase('Write Feedback') }}</label>
        <textarea class="form-control eForm-control" id="feedback_text" name = "feedback_text" required></textarea>
    </div>

    <div class="fpb-7 pt-2">
        <button type="submit" class="btn-form">{{ get_phrase('Send Feedback') }}</button>
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
