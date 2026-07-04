<form method="POST" class="d-block ajaxForm" action="{{ route('superadmin.addon.create') }}" enctype="multipart/form-data">
  @CSRF
  <div class="form-row">
    <div class="fpb-7">
      <label for="addon_zip" class="eForm-label">{{ get_phrase('Upload addons zip file') }}</label>
      <input type="file" class="form-control eForm-control-file" id="addon_zip" name = "file" required>
    </div>
    <div class="fpb-7">
      <label for="purchase_code" class="eForm-label">{{ get_phrase('Purchase Code') }}</label>
      <input type="text" class="form-control eForm-control" id="purchase_code" name = "purchase_code" required>
    </div>
  </div>
  <div class="form-group col-md-12 mt-1">
    <button class="btn-form" type="submit">{{ get_phrase('Install addon') }}</button>
  </div>
</form>

<script type="text/javascript">

    "use strict";
    
    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });
</script>
