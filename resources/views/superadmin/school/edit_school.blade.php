<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.school.update', ['id' => $school->id]) }}">
         @csrf 
        <div class="form-row">
            <div class="fpb-7">
                <label for="title" class="eForm-label">{{ get_phrase('Title') }}</label>
                <input type="text" class="form-control eForm-control" value="{{ $school->title }}" id="title" name = "title" required>
            </div>
            <div class="fpb-7">
                <label for="address" class="eForm-label">{{ get_phrase('School address') }}</label>
                <textarea class="form-control eForm-control" id="address" name = "address" rows="2" required>{{ $school->address }}</textarea>
            </div>
            <div class="fpb-7">
                <label for="title" class="eForm-label">{{ get_phrase('School phone') }}</label>
                <input type="number" min="0" class="form-control eForm-control" value="{{ $school->phone }}" id="phone" name = "phone" required>
            </div>
            <div class="fpb-7">
                <label for="school_info" class="eForm-label">{{ get_phrase('School info') }}</label>
                <textarea class="form-control eForm-control" id="school_info" name = "school_info" rows="2" required>{{ $school->school_info }}</textarea>
            </div>
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Update school') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    "use strict";
    
    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });
</script>