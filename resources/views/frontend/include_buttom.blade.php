<!-- Jquery Js -->
<script src="{{ asset('frontend/assets/js/jquery-3.6.1.min.js') }}"></script>
<!-- Bootstarp Js -->
<script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
<!-- Main Js -->
<script src="{{ asset('frontend/assets/js/custom.js') }}"></script>

<!--Toaster Script-->
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

<script>

"use strict";

    @if(Session::has('message'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.success("{{ session('message') }}");
    @endif

    @if(Session::has('error'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.error("{{ session('error') }}");
    @endif

    @if(Session::has('info'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.info("{{ session('info') }}");
    @endif

    @if(Session::has('warning'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.warning("{{ session('warning') }}");
    @endif
</script>