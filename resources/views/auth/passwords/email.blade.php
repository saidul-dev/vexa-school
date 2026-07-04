@extends('layouts.signin_page')

@section('content')

<style type="text/css">

.login-image{
    width: inherit; 
    height: 100%; 
    position: fixed; 
    background-image: url('{{ asset("assets/images/login.png") }}');
    background-size: cover; 
    background-position: center;
}

</style>

<div class="row h-100">
    <div class="col-lg-6 d-none d-lg-block p-0 h-100">
        <div class="bg-image login-image">
        </div>
    </div>
    <div class="col-lg-6 p-0 h-100 position-relative">
        <div class="parent-elem">
            <div class="middle-elem">
                <div class="primary-form">
                    <div class="form-logo mb-5">
                        <img height="60px" src="{{ asset('assets/uploads/logo/'.get_settings('dark_logo')) }}">
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="subtitle mb-2">
                                {{ get_phrase('Enter your email address to reset your password.') }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="login-form">
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="email" class="form-control border-end @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" placeholder="{{ 'Your email address' }}">

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-3 mt-3 fw-600">{{get_phrase('Reset password')}}</button>
                                    <a href="{{route('login')}}" class="btn btn-secondary text-white w-100 py-3 mt-3 fw-600"><i class="bi bi-chevron-left text-10px fw-bolder"></i> {{get_phrase('Back')}}</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
