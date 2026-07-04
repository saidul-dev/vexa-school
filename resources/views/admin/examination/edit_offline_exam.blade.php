<?php

use App\Models\ExamCategory;

$category_details = ExamCategory::where('name', $exam->name)->first();


$class_rooms = DB::table('class_rooms')->where('school_id', auth()->user()->school_id)->get();


?>

<div class="eoff-form">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.offline_exam.update', ['id' => $exam->id]) }}">
        @csrf
        <div class="form-row">

            <div class="fpb-7">
                <label for="exam_category_id" class="eForm-label">{{ get_phrase('Exam Name') }}</label>
                <select name="exam_category_id" id="exam_category_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select exam category name') }}</option>
                    @foreach($exam_categories as $exam_category)
                        <option value="{{ $exam_category->id }}" {{ $exam_category->id == $exam->exam_category_id ?  'selected':'' }}>{{ $exam_category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fpb-7">
                <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSubjectOnExamEdit(this.value)">
                    <option value="">Select a class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $exam->class_id == $class->id ?  'selected':'' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fpb-7">
                <label for="subject_id" class="eForm-label">{{ get_phrase('Subject') }}</label>
                <select name="subject_id" id="subject_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                    <option value="">Select a subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ?  'selected':'' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fpb-7">
                <label for="class_room_id" class="eForm-label">{{ get_phrase('Class room') }}</label>
                <select name="class_room_id" id = "class_room_id_on_routine_creation" class="form-select eForm-select eChoice-multiple-with-remove"  required>
                    <option value="">{{ get_phrase('Select a class room') }}</option>
                    <?php foreach($class_rooms as $class_room): ?>
                        <option value="{{ $class_room->id }}" {{ $exam->room_number == $class_room->id ?  'selected':'' }}>{{ $class_room->name }}</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fpb-7">
                <label for="starting_date" class="eForm-label">{{ get_phrase('Starting date') }}<span class="required">*</span></label>
                <input type="date" class="form-control eForm-control" id="eInputDate" name="starting_date" value="{{ date('Y-m-d', $exam->starting_time) }}">
            </div>

            <div class="fpb-7">
                <label for="starting_time" class="eForm-label">{{ get_phrase('Starting time') }}<span class="required">*</span></label>
                <input type="time" class="form-control eForm-control" id="starting_time" name="starting_time" value="{{ date('H:i', $exam->starting_time) }}">
            </div>

            <div class="fpb-7">
                <label for="ending_date" class="eForm-label">{{ get_phrase('Ending date') }}<span class="required">*</span></label>
                <input type="date" class="form-control eForm-control" id="eInputDate" name="ending_date" value="{{ date('Y-m-d', $exam->ending_time) }}">
            </div>

            <div class="fpb-7">
                <label for="ending_time" class="eForm-label">{{ get_phrase('Ending time') }}<span class="required">*</span></label>
                <input type="time" class="form-control eForm-control" id="ending_time" name="ending_time" value="{{ date('H:i', $exam->ending_time) }}">
            </div>

            <div class="fpb-7">
                <label for="total_marks" class="eForm-label">{{ get_phrase('Total marks') }}<span class="required">*</span></label>
                <div>
                    <input class="form-control eForm-control" id="total_marks" type="number" min="1" name="total_marks" value="{{ $exam->total_marks }}" >
                </div>
            </div>

            <div class="fpb-7">
                <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
            </div>
        </div>
    </form>
</div>


<script type="text/javascript">

  "use strict";


    function classWiseSubjectOnExamEdit(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#subject_id').html(response);
            }
        });
    }

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

</script>
