<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('admin.disable_reason.create') }}">
        @csrf
        <div class="form-row">
            <div class="fpb-7">
                <label for="disable_reason" class="eForm-label">{{ get_phrase('Reason') }}</label>
                <input type="text" class="form-control eForm-control" id="disable_reason" name = "disable_reason"
                    required>
            </div>
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Create') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });

    $(function() {
        $('.inputDate').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format("YYYY"), 10),
            },
            function(start, end, label) {
                var years = moment().diff(start, "years");
            }
        );
    });
</script>
