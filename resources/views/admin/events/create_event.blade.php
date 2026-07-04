<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.event') }}">
    @csrf 
	<div class="fpb-7">
        <label for="title" class="eForm-label">{{ get_phrase('Event title') }}</label>
        <input type="text" class="form-control eForm-control" id="title" name = "title" required>
    </div>

    <div class="fpb-7">
        <label for="timestamp" class="eForm-label">{{ get_phrase('Date') }}</label>
        <input type="text" class="form-control eForm-control inputDate" id="timestamp" name = "timestamp" value="{{ date('m/d/Y') }}" required>
    </div>

    <div class="fpb-7">
        <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
        <select name="status" id="status" class="form-select eForm-control">
            <option value="1">{{ get_phrase('Active') }}</option>
            <option value="0">{{ get_phrase('Inactive') }}</option>
        </select>
    </div>

    <div class="fpb-7 pt-2">
    	<button class="btn-form" type="submit">{{ get_phrase('Save event') }}</button>
    </div>

</form>

<script type="text/javascript">

    "use strict";
    
    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

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