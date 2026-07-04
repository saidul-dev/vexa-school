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
                            <div class="login-form">
                                <form method="POST" action="{{ route('password.update') }}">
                                    @csrf
                                    <div class="form-row">
                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group mt-3">
                                        <label for="email" class="form-label">{{ get_phrase('Email') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light-block"><i class="bi bi-envelope"></i></span>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror mb-0" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group mt-3">
                                            <label for="password" class="form-label">{{ get_phrase('Password') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                                <input id="password" placeholder="********" type="password" class="form-control @error('password') is-invalid @enderror mb-0" name="password" required autocomplete="new-password">

                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group mt-3">
                                        <label for="password-confirm" class="form-label">{{ get_phrase('Confirm Password') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                                <input id="password-confirm" placeholder="********" type="password" class="form-control eForm-control mb-0" name="password_confirmation" required autocomplete="new-password">
                                            </div>
                                        </div>

                                        <div class="row mb-0">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    {{ get_phrase('Reset Password') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
