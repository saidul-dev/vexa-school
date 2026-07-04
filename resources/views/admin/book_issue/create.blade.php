<?php 

use App\Models\BookIssue;

?>
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.book_issue') }}">
    @csrf 
	<div class="form-row">

		<div class="fpb-7">
	        <label for="issue_date" class="eForm-label">{{ get_phrase('Issue date') }}</label>
	        <input type="text" class="form-control eForm-control inputDate" id="issue_date" name = "issue_date" value="{{ date('m/d/Y') }}" required>
	    </div>

	    <div class="fpb-7">
	        <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
	        <select name="class_id" id="class_id_on_modal" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseStudentOnCreate(this.value)">
                <option value="">{{ get_phrase('Select a class') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
	    </div>

	    <div class="fpb-7">
	        <label for="student_id" class="eForm-label"> {{ get_phrase('Student') }}</label>
	        <select name="student_id" id="student_id_on_create" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value="">{{ get_phrase('Select a student') }}</option>
            </select>
	    </div>

	    <div class="fpb-7">
	        <label for="book_id" class="eForm-label"> {{ get_phrase('Book') }}</label>
	        <select name="book_id" id="book_id_on_modal" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select book') }}</option>
                @foreach($books as $book)
	                	<?php $number_of_issued_book = BookIssue::get()->where('book_id', $book['id'])->where('status', 0); ?>
	      						@if($book['copies'] - count($number_of_issued_book) > 0)
                    	<option value="{{ $book['id'] }}">{{ $book['name'] }}</option>
                    @endif
                @endforeach
            </select>
	    </div>

	    <div class="fpb-7 pt-2">
		    <button class="btn-form" type="submit">{{ get_phrase('Save') }}</button>
		</div>
	</div>
</form>

<script type="text/javascript">

  "use strict";

	
	function classWiseStudentOnCreate(classId) {
		let url = "{{ route('admin.class_wise_student', ['id' => ":classId"]) }}";
    	url = url.replace(":classId", classId);
		$.ajax({
			url: url,
			success: function(response){
		  		$('#student_id_on_create').html(response);
			}
		});
	}

	$(function () {
      $('.inputDate').daterangepicker(
        {
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format("YYYY"), 10),
        },
        function (start, end, label) {
          var years = moment().diff(start, "years");
        }
      );
    });

	
  $(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
  });

</script>