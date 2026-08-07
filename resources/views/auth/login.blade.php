@extends('layouts.signin_page')

@section('content')

<style type="text/css">

.login-showcase{
    width: inherit;
    height: 100%;
    position: fixed;
    background: linear-gradient(135deg, #0A1C3A 0%, #06B6D4 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
}

.login-showcase:before,
.login-showcase:after{
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}

.login-showcase:before{
    width: 460px;
    height: 460px;
    top: -160px;
    right: -140px;
}

.login-showcase:after{
    width: 320px;
    height: 320px;
    bottom: -120px;
    left: -100px;
    background: rgba(255,255,255,0.05);
}

.login-showcase-inner{
    position: relative;
    z-index: 1;
    max-width: 480px;
    margin: 0 auto;
    padding: 40px;
}

.login-showcase-logo img{
    height: 42px;
    margin-bottom: 46px;
}

.login-showcase-title{
    font-size: 34px;
    font-weight: 700;
    line-height: 44px;
    color: #fff;
    margin-bottom: 16px;
}

.login-showcase-subtitle{
    font-size: 16px;
    line-height: 26px;
    color: rgba(255,255,255,0.75);
    margin-bottom: 36px;
}

.login-feature-list{
    list-style: none;
    padding: 0;
    margin: 0;
}

.login-feature-list li{
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 15px;
    font-weight: 500;
    color: #fff;
    padding: 14px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.login-feature-list li:last-child{
    border-bottom: 0;
}

.login-feature-icon{
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: rgba(255,255,255,0.12);
    color: #fff;
    font-size: 16px;
}

.login-trustbar{
    position: relative;
    z-index: 1;
    display: flex;
    gap: 34px;
    margin-top: 40px;
}

.login-trustbar div{
    color: #fff;
}

.login-trustbar h4{
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 2px;
}

.login-trustbar span{
    font-size: 13px;
    color: rgba(255,255,255,0.7);
}

.login-form form input:not([type=checkbox]){
    border-radius: 10px;
}

.login-form form .input-group-text{
    border-radius: 10px 0 0 10px;
}

.login-form form .input-group input{
    border-radius: 0 10px 10px 0 !important;
}

.login-form form input:focus{
    border-color: #06B6D4;
}

.login-form form button{
    background: linear-gradient(135deg, #0A1C3A 0%, #06B6D4 100%);
    border: none;
    border-radius: 10px;
    transition: .3s;
}

.login-form form button:hover{
    opacity: .92;
    transform: translateY(-1px);
}

.form-logo-mobile{
    text-align: center;
    margin-bottom: 36px;
}

.form-logo-mobile img{
    height: 46px;
}

.login-back-link{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #797C8B;
    margin-bottom: 28px;
    transition: .3s;
}

.login-back-link:hover{
    color: #06B6D4;
}

</style>

<div class="row h-100">
    <div class="col-12 col-lg-6 d-none d-lg-block p-0 h-100">
        <div class="login-showcase">
            <div class="login-showcase-inner">
                <div class="login-showcase-logo">
                    <img src="{{ asset('assets/uploads/logo/'.get_settings('light_logo')) }}" alt="{{ get_settings('system_title') }}">
                </div>
                <h2 class="login-showcase-title">{{ get_phrase('The Smarter Way to Run Your School') }}</h2>
                <p class="login-showcase-subtitle">{{ get_phrase('Manage admissions, attendance, exams, fees and more, all from one powerful dashboard.') }}</p>
                <ul class="login-feature-list">
                    <li><span class="login-feature-icon"><i class="bi bi-people-fill"></i></span>{{ get_phrase('Complete student & admission management') }}</li>
                    <li><span class="login-feature-icon"><i class="bi bi-calendar-check-fill"></i></span>{{ get_phrase('Attendance, exams & results in one place') }}</li>
                    <li><span class="login-feature-icon"><i class="bi bi-cash-coin"></i></span>{{ get_phrase('Fee collection & accounting built in') }}</li>
                    <li><span class="login-feature-icon"><i class="bi bi-bell-fill"></i></span>{{ get_phrase('Real-time SMS & notice board') }}</li>
                    <li><span class="login-feature-icon"><i class="bi bi-shield-lock-fill"></i></span>{{ get_phrase('Secure, cloud-based & always available') }}</li>
                </ul>
                <div class="login-trustbar">
                    <div>
                        <h4>{{ count($schools ?? []) ?: '1+' }}</h4>
                        <span>{{ get_phrase('Schools') }}</span>
                    </div>
                    <div>
                        <h4>24/7</h4>
                        <span>{{ get_phrase('Support') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 p-0 h-100 position-relative">
        <div class="parent-elem">
            <div class="middle-elem">
                <div class="primary-form">
                    <a href="{{ route('landingPage') }}" class="login-back-link"><i class="bi bi-arrow-left"></i> {{ get_phrase('Go to website') }}</a>
                    <div class="form-logo-mobile d-lg-none">
                        <img src="{{ asset('assets/uploads/logo/'.get_settings('dark_logo')) }}" alt="{{ get_settings('system_title') }}">
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="login-form">
                                <form method="post" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="email" class="form-label">{{ get_phrase('Email') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="email" class="form-control" id="email"
                                                  placeholder="Your email address">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="form-label">{{ get_phrase('Password') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                <input type="password" name="password" class="form-control border-end" id="password"
                                                  placeholder="Input your password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="w-100 d-flex justify-content-end">
                                                <a href="{{ route('password.request') }}" class="float-end">{{ get_phrase('Forgot password') }}</a>
                                                <i class="bi bi-record-fill text-5px mx-1 px-1"></i>
                                                <a href="{{ get_settings('help_link') }}" target="_blank" class="float-end me-1">{{ get_phrase('Help') }}</a>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">{{ get_phrase('Login') }}</button>
                                        <div class="form-group mt-2">
                                            <div class="d-flex justify-content-between gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 demo-login-btn" data-email="superadmin@example.com" data-password="1234">{{ get_phrase('Super Admin') }}</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 demo-login-btn" data-email="admin@example.com" data-password="1234">{{ get_phrase('Admin') }}</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 demo-login-btn" data-email="student@example.com" data-password="1234">{{ get_phrase('Student') }}</button>
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

<script>
document.querySelectorAll('.demo-login-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('email').value = btn.dataset.email;
        document.getElementById('password').value = btn.dataset.password;
    });
});
</script>
@endsection
