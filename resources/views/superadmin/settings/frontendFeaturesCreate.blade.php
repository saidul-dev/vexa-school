
    <!---Icon Piker -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/font-awesome-icon-picker/fontawesome-iconpicker.min.css') }}">
<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.system.frontendFeaturesadd') }}">
    @csrf 
    <div class="form-row">
        <div class="fpb-7">
            <label for="features_subtitle" class="eForm-label">{{ get_phrase('Features List Image') }}</label>
            <input type="text" name="icon" class="eForm-control form-control icon-picker" placeholder="{{get_phrase('Features List Icon')}}">
        </div>
        <div class="fpb-7">
            <label for="features_list_title" class="eForm-label">{{ get_phrase('Features List Title') }}</label>
            <input type="text" class="form-control eForm-control" id="features_list_title" name = "title" required>
        </div>
        <div class="fpb-7">
            <label for="features_list_description" class="eForm-label">{{ get_phrase('Short Description') }}</label>
            <textarea class="form-control eForm-control"  id="features_list_description" name = "description"></textarea>
        </div>
        
    </div>
    <button type="submit" class="btn-form">{{ get_phrase('Submit') }}</button>
</form>
<!--Fontawsome Icon-Piker-->
<script src="{{ asset('assets/js/font-awesome-icon-picker/fontawesome-iconpicker.min.js') }}"></script>

<script>
        // fontawsome Icon Piker
        $(document).ready(function() {
    $(function() {
       $('.icon-picker').iconpicker();
     });
     });

</script>