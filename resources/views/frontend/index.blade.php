<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ get_settings('system_title') }}</title>

    @include('frontend.include_top')

</head>

<body data-bs-spy="scroll" data-bs-target=".header-area" data-bs-offset="50" tabindex="0">

    @yield('content')

    @include('external_plugin')
    
    @include('frontend.include_buttom')
    
</body>
</html>