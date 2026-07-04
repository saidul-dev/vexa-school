<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.add.exam_mark') }}">
  @csrf 
	<div class="form-row">
		<div class="fpb-7">
            <label for="class_id">{{ get_phrase("Class") }}</label>
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSectionForMark(this.value)">
                <option value="">{{ get_phrase("Select a class") }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
            <small id="" class="form-text text-muted">{{ get_phrase("Provide a class") }}</small>
        </div>

        <div class="fpb-7">
            <label for="section_id_for_mark">{{ get_phrase("Section") }}</label>
            <select name="section_id" id="section_id_for_mark" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value="">{{ get_phrase("First select a class") }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="exam_category_id">{{ get_phrase('Exam Type') }}</label>
            <select name="exam_category_id" id="exam_category_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select a exam category') }}</option>
                @foreach($exam_categories as $exam_category)
                    <option value="{{ $exam_category->id }}">{{ $exam_category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="fpb-7">
            <label for="subject_id">{{ get_phrase("Subject") }}</label>
            <select name="subject_id" id="subject_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value="">{{ get_phrase("First select a class") }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="student_id">{{ get_phrase("Student") }}</label>
            <select name="student_id" id="student_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value="">{{ get_phrase("First select a class") }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="mark_from">{{ get_phrase("Marks") }}</label>
            <input type="number" class="form-control eForm-control" id="mark" name = "mark" placeholder="Mark" required>
            <small id="" class="form-text text-muted">{{ get_phrase("Provide grade mark") }}</small>
        </div>

        <div class="form-group">
	        <button class="btn btn-block btn-primary" type="submit">{{ get_phrase('Add exam mark') }}</button>
	    </div>
	</div>
</form>

<script type="text/javascript">

  "use strict";


	function classWiseSectionForMark(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id_for_mark').html(response);
                classWiseSubject(classId);
            }
        });
    }

    function classWiseSubject(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#subject_id').html(response);
                classWiseStudent(classId);
            }
        });
    }

    function classWiseStudent(classId) {
        let url = "{{ route('admin.class_wise_student', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#student_id').html(response);
            }
        });
    }

</script>