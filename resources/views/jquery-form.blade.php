<script src="{{ asset('assets/vendors/jquery-form/jquery.form.min.js') }}"></script>

<script>
	 var formElement;
    if($('.jqueryAjaxForm:not(.initialized)').length > 0){
        $('.jqueryAjaxForm:not(.initialized)').jqueryAjaxForm({
            beforeSend: function(data, form) {
                var formElement = $(form);
            },
            uploadProgress: function(event, position, total, percentComplete) {
            },
            complete: function(xhr) {

                setTimeout(function(){

                }, 400);

                if($('.jqueryAjaxForm.resetable').length > 0){
                    $('.jqueryAjaxForm.resetable')[0].reset();
                }
            },
            error: function(e)
            {
                console.log(e);
            }
        });
        $('.jqueryAjaxForm:not(.initialized)').addClass('initialized');
    }
</script>