<div class="eoff-form">
	<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.noticeboard') }}">
		@csrf 
		<div class="form-row">
			<div class="fpb-7">
	            <label for="notice_title" class="eForm-label">{{ get_phrase('Notice title') }}</label>
	            <input type="text" class="form-control eForm-control" id="notice_title" name = "notice_title" required>
	        </div>

	        <div class="fpb-7">
	            <label for="start_date" class="eForm-label">{{ get_phrase('Start date') }}<span class="required"></span></label>
	            <input type="text" class="form-control eForm-control inputDate" id="start_date" name="start_date" value="{{ date('m/d/Y') }}" />
	        </div>

			<div class="fpb-7">
		        <input type="checkbox" name="time_details" id="time_details" value="1" onclick="toggleTimeFields(this.id)">
		        <label for="time_details">{{ get_phrase('Setup additional date & time') }}?</label>
		    </div>


		    <div class="time-details-stuffs">

		    	<div class="fpb-7">
		            <label for="start_time" class="eForm-label">{{ get_phrase('Start time') }}<span class="required"></span></label>
		            <input type="time" class="form-control eForm-control" id="start_time" name="start_time" value="{{ date('H:i', strtotime(date('H:i'))) }}">
		        </div>

		        <div class="fpb-7">
		            <label for="end_date" class="eForm-label">{{ get_phrase('End date') }}<span class="required"></span></label>
		            <input type="text" class="form-control eForm-control inputDate" id="end_date" name="end_date" value="{{ date('m/d/Y') }}" />
		        </div>

		        <div class="fpb-7">
		            <label for="end_time" class="eForm-label">{{ get_phrase('End time') }}<span class="required"></span></label>
		            <input type="time" class="form-control eForm-control" id="end_time" name="end_time" value="{{ date('H:i', strtotime(date('H:i'))) }}">
		        </div>
		    </div>

			<div class="fpb-7">
				<label for="notice" class="eForm-label">{{ get_phrase('Notice') }}</label>
				<textarea name="notice" class="form-control eForm-control" rows="8" cols="80" required></textarea>
			</div>

			<div class="fpb-7">
				<label for="show_on_website" class="eForm-label">{{ get_phrase('Show on website') }}</label>
				<select name="show_on_website" id="show_on_website" class="form-select eForm-select eChoice-multiple-with-remove">
					<option value="1">{{ get_phrase('Show') }}</option>
					<option value="0">{{ get_phrase('Do not need to show') }}</option>
				</select>
			</div>

			<div class="fpb-7">
                <label for="image" class="eForm-label">{{ get_phrase('Upload notice photo') }}</label>
                <input class="form-control eForm-control-file" type="file" id="image" name="image" accept="image/*">
            </div>

			<div class="fpb-7 pt-2">
				<button class="btn-form" type="submit">{{ get_phrase('Save notice') }}</button>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">

  "use strict";

	function toggleTimeFields(elem) {
  	if($("#"+elem).is(':checked')){
    		$('.time-details-stuffs').slideUp();
  	} else {
  		$('.time-details-stuffs').slideDown();
  	}
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