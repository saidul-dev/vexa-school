@extends('superadmin.navigation')

@section('content')

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Payment settings'); }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home'); }}</a></li>
                        <li><a href="#">{{ get_phrase('Settings'); }}</a></li>
                        <li><a href="#">{{ get_phrase('Payment settings'); }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>



    <div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>{{ get_phrase('Global Currency'); }}</h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" class="col-12 live-class-settings-form" action="{{ route('superadmin.update_payment_settings') }}" id="live-class-settings-form">
                                @csrf <!-- {{ csrf_field() }} -->

                                <div class="fpb-7">
                                    <label for="global_currency" class="eForm-label">{{ get_phrase('Global Currency'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" id = "global_currency" name="global_currency" required>
                                        <option value="">{{ get_phrase('Select system currency'); }}</option>
                                        <?php
                                        foreach ($currencies as $currency):?>
                                        <option value="{{ $currency['code']; }}"
                                          {{ $global_currency == $currency['code'] ? 'selected':''; }}> {{ $currency['code']; }}
                                        </option>
                                      <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="currency_position" class="eForm-label">{{ get_phrase('Currency Position'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove"  id = "currency_position" name="currency_position" required>
                                        <option value="left" {{ $global_currency_position == 'left' ? 'selected':''; }} >{{ get_phrase('Left'); }}</option>
                                        <option value="right" {{ $global_currency_position == 'right' ? 'selected':''; }} >{{ get_phrase('Right'); }}</option>
                                        <option value="left-space" {{ $global_currency_position == 'left-space' ? 'selected':''; }} >{{ get_phrase('Left with a space'); }}</option>
                                        <option value="right-space" {{ $global_currency_position == 'right-space' ? 'selected':''; }} >{{ get_phrase('Right with a space'); }}</option>
                                      </select>
                                </div>

                                <input type="hidden" id="method" name="method" value="currency">


                                <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form" onclick="">{{ get_phrase('Update Currency'); }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>{{ get_phrase('Offline Payment Instruction') }}</h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.system.update') }}">
                                @csrf
                                <div class="fpb-7">
                                    <label for="system_title" class="eForm-label">{{ get_phrase('Offline Payment Instruction') }}</label>
                                    <textarea class="form-control eForm-control" id="off_pay_ins_text" name = "off_pay_ins_text">{{ get_settings('off_pay_ins_text') }}</textarea>
                                </div> 
                                <div class="fpb-7">
                                    <label class="eForm-label" for="example-fileinput">{{ get_phrase('Offline Payment Instruction Image/PDF') }}</label>
                                    <input class="form-control eForm-control-file" id="formFileSm" type="file" name="off_pay_ins_file">
                                </div>
                            </div>
                                 <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form">{{ get_phrase('Submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@if(addon_status('payment_gateways')==1)

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>
                    {{ get_phrase('Paypal settings') }}
                    <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                </h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" class="col-12 live-class-settings-form" action="{{ route('superadmin.update_payment_settings') }}" id="live-class-settings-form">
                                @csrf <!-- {{ csrf_field() }} -->

                                <div class="fpb-7">
                                    <label for="status" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="status">
                                        <option value="1" {{ $paypal['status'] == 1 ? 'selected':''; }}>{{ get_phrase('Yes') ; }}</option>
                                        <option value="0" {{ $paypal['status'] == 0 ? 'selected':''; }}>{{ get_phrase('No') ; }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="mode" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="mode">
                                        <option value="live" {{ $paypal['mode'] == 'live' ? 'selected':''; }}>{{ get_phrase('Live'); }}</option>
                                        <option value="test" {{ $paypal['mode'] == 'test' ? 'selected':''; }}>{{ get_phrase('Sandbox'); }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="test_client_id" class="eForm-label">{{ get_phrase('Client ID (Sandbox)'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_client_id" name = "test_client_id" placeholder="Sandbox Client Id" value="{{ $paypal['test_client_id'] }}" aria-label="Sandbox Client Id" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="test_secret_key" class="eForm-label">{{ get_phrase('Client Secrect (Sandbox)'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_secret_key" name = "test_secret_key" placeholder="Sandbox Secrect Id" value="{{ $paypal['test_secret_key'] }}" aria-label="Sandbox Secrect Id" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="live_client_id" class="eForm-label">{{ get_phrase('Client ID (Live)'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="live_client_id" name = "live_client_id" placeholder="Live Client Id" value="{{ $paypal['live_client_id'] }}" aria-label="Live Client Id" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="live_secret_key" class="eForm-label">{{ get_phrase('Client Secrect (Live)'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="live_secret_key" name = "live_secret_key" placeholder="Live Secrect Id" value="{{ $paypal['live_secret_key'] }}" aria-label="Live Secrect Id" required>
                                </div>



                                <input type="hidden" id="method" name="method" value="paypal">


                                <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form" onclick="">{{ get_phrase('Update Paypal'); }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>
                    {{ get_phrase('Stripe settings') }}
                    <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                </h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" class="col-12 live-class-settings-form" action="{{ route('superadmin.update_payment_settings') }}" id="live-class-settings-form">
                                @csrf <!-- {{ csrf_field() }} -->

                                <div class="fpb-7">
                                    <label for="status" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="status">
                                        <option value="1" {{ $stripe['status'] == 1 ? 'selected':''; }}>{{ get_phrase('Yes') ; }}</option>
                                        <option value="0" {{ $stripe['status'] == 0 ? 'selected':''; }}>{{ get_phrase('No') ; }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="mode" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="mode">
                                        <option value="live" {{ $stripe['mode'] == 'live' ? 'selected':''; }}>{{ get_phrase('Live'); }}</option>
                                        <option value="test" {{ $stripe['mode'] == 'test' ? 'selected':''; }}>{{ get_phrase('Test'); }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="test_key" class="eForm-label">{{ get_phrase('Test Public Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_key" name = "test_key" placeholder="Test Public Key" value="{{$stripe['test_key'] }}" aria-label="Test Public Key" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="test_secret_key" class="eForm-label">{{ get_phrase('Test Sectect Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_secret_key" name = "test_secret_key" placeholder="Test Sectect Key" value="{{ $stripe['test_secret_key'] }}" aria-label="Test Sectect Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="public_live_key" class="eForm-label">{{ get_phrase('Live Public Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="public_live_key" name = "public_live_key" placeholder="Live Public Key" value="{{ $stripe['public_live_key'] }}" aria-label="Live Public Key" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="secret_live_key" class="eForm-label">{{ get_phrase('Live Secrect Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="secret_live_key" name = "secret_live_key" placeholder="Live Secrect Key" value="{{ $stripe['secret_live_key'] }}" aria-label="Live Secrect Key" required>
                                </div>

                                <input type="hidden" id="method" name="method" value="stripe">




                                <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form" onclick="">{{ get_phrase('Update Stripe '); }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>
                    {{ get_phrase('Razorpay settings') }}
                    <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                </h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" class="col-12 live-class-settings-form" action="{{ route('superadmin.update_payment_settings') }}" id="live-class-settings-form">
                                @csrf <!-- {{ csrf_field() }} -->

                                <div class="fpb-7">
                                    <label for="status" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="status">
                                        <option value="1" {{ $razorpay['status'] == 1 ? 'selected':''; }}>{{ get_phrase('Yes') ; }}</option>
                                        <option value="0" {{ $razorpay['status'] == 0 ? 'selected':''; }}>{{ get_phrase('No') ; }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="mode" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="mode">
                                        <option value="live" {{ $razorpay['mode'] == 'live' ? 'selected':''; }}>{{ get_phrase('Live'); }}</option>
                                        <option value="test" {{ $razorpay['mode'] == 'test' ? 'selected':''; }}>{{ get_phrase('Test'); }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="test_key" class="eForm-label">{{ get_phrase('Test Public Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_key" name = "test_key" placeholder="Test Public Key" value="{{$razorpay['test_key'] }}" aria-label="Test Public Key" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="test_secret_key" class="eForm-label">{{ get_phrase('Test Sectect Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_secret_key" name = "test_secret_key" placeholder="Test Sectect Key" value="{{ $razorpay['test_secret_key'] }}" aria-label="Test Sectect Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="live_key" class="eForm-label">{{ get_phrase('Live Public Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="live_key" name = "live_key" placeholder="Live Public Key" value="{{ $razorpay['live_key'] }}" aria-label="Live Public Key" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="live_secret_key" class="eForm-label">{{ get_phrase('Live Secrect Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="live_secret_key" name = "live_secret_key" placeholder="Live Secrect Key" value="{{ $razorpay['live_secret_key'] }}" aria-label="Live Secrect Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="theme_color" class="eForm-label">{{ get_phrase('Theme Color'); }}</label>
                                    <input type="color" class="form-control eForm-control" id="theme_color" name = "theme_color" placeholder="Live Secrect Key" value="{{ $razorpay['theme_color'] }}" aria-label="Live Secrect Key" required>
                                </div>


                                <input type="hidden" id="method" name="method" value="razorpay">


                                <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form" onclick="">{{ get_phrase('Update razorpay '); }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>
                    {{ get_phrase('Paytm settings') }}
                    <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                </h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" class="col-12 live-class-settings-form" action="{{ route('superadmin.update_payment_settings') }}" id="live-class-settings-form">
                                @csrf <!-- {{ csrf_field() }} -->

                                <div class="fpb-7">
                                    <label for="status" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="status">
                                        <option value="1" {{ $paytm['status'] == 1 ? 'selected':''; }}>{{ get_phrase('Yes') ; }}</option>
                                        <option value="0" {{ $paytm['status'] == 0 ? 'selected':''; }}>{{ get_phrase('No') ; }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="mode" class="eForm-label">{{ get_phrase('Active'); }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="mode">
                                        <option value="live" {{ $paytm['mode'] == 'live' ? 'selected':''; }}>{{ get_phrase('Live'); }}</option>
                                        <option value="test" {{ $paytm['mode'] == 'test' ? 'selected':''; }}>{{ get_phrase('Test'); }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="test_merchant_id" class="eForm-label">{{ get_phrase('Test Merchant Id'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_merchant_id" name = "test_merchant_id" placeholder="Test Merchant Id" value="{{$paytm['test_merchant_id'] }}" aria-label="Test Merchant Id" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="test_merchant_key" class="eForm-label">{{ get_phrase('Test Merchant Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_merchant_key" name = "test_merchant_key" placeholder="Test Merchant Key" value="{{ $paytm['test_merchant_key'] }}" aria-label="Test Merchant Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="live_merchant_id" class="eForm-label">{{ get_phrase('Live Merchant Id'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="live_merchant_id" name = "live_merchant_id" placeholder="Live Merchant Id" value="{{ $paytm['live_merchant_id'] }}" aria-label="Live Merchant Id" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="live_merchant_key" class="eForm-label">{{ get_phrase('Live Merchant Key'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="live_merchant_key" name = "live_merchant_key" placeholder="Live Merchant Key" value="{{ $paytm['live_merchant_key'] }}" aria-label="Live Merchant Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="environment" class="eForm-label">{{ get_phrase('Environment'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="environment" name = "environment" placeholder="Environment" value="{{ $paytm['environment'] }}" aria-label="Environment" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="merchant_website" class="eForm-label">{{ get_phrase('Merchant_Website'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="merchant_website" name = "merchant_website" placeholder="merchant_website" value="{{ $paytm['merchant_website'] }}" aria-label="Environment" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="industry_type" class="eForm-label">{{ get_phrase('Channel'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="channel" name = "channel" placeholder="channel" value="{{ $paytm['channel'] }}" aria-label="Environment" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="industry_type" class="eForm-label">{{ get_phrase('industry_type'); }}</label>
                                    <input type="text" class="form-control eForm-control" id="industry_type" name = "industry_type" placeholder="industry_type" value="{{ $paytm['industry_type'] }}" aria-label="Environment" required>
                                </div>

                                <input type="hidden" id="method" name="method" value="paytm">




                                <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form" onclick="">{{ get_phrase('Update Paytm '); }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="title">
                <h3>
                    {{ get_phrase('Flutterwave settings') }}
                    <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                </h3>
            </div>
            <div class="eMain">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="eForm-layouts">
                            <form method="POST" class="col-12 flutterwave-settings-form" action="{{ route('superadmin.update_payment_settings') }}" id="flutterwave-settings-form">
                                @csrf 

                                <div class="fpb-7">
                                    <label for="status" class="eForm-label">{{ get_phrase('Active') }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="status" id="status">
                                        <option value="1" <?php if ($flutterwave['status'] == 1): ?> selected <?php endif; ?>>{{ get_phrase('yes') }}</option>
                                        <option value="0" <?php if ($flutterwave['status'] == 0): ?> selected <?php endif; ?>>{{ get_phrase('no') }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="mode" class="eForm-label">{{ get_phrase('Active') }}</label>
                                    <select class="form-select eForm-select eChoice-multiple-with-remove" name="mode" id="mode">
                                        <option value="live" <?php if ($flutterwave['mode'] == 'live'): ?> selected <?php endif; ?>>{{ get_phrase('Live') }}</option>
                                        <option value="test" <?php if ($flutterwave['mode'] == 'test'): ?> selected <?php endif; ?>>{{ get_phrase('Test') }}</option>
                                      </select>
                                </div>

                                <div class="fpb-7">
                                    <label for="test_key" class="eForm-label">{{ get_phrase('Test Public Key') }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_key" name = "test_key" placeholder="Test Public Key" value="{{ $flutterwave['test_key'] }}" aria-label="Test Public Key" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="test_secret_key" class="eForm-label">{{ get_phrase('Test Secrect Key') }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_secret_key" name = "test_secret_key" placeholder="Test Secrect Key" value="{{ $flutterwave['test_secret_key'] }}" aria-label="Test Secrect Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="test_encryption_key" class="eForm-label">{{ get_phrase('Test Encryption Key') }}</label>
                                    <input type="text" class="form-control eForm-control" id="test_encryption_key" name = "test_encryption_key" placeholder="Test Encryption Key" value="{{ $flutterwave['test_encryption_key'] }}" aria-label="Test Encryption Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="public_live_key" class="eForm-label">{{ get_phrase('Live Public Key') }}</label>
                                    <input type="text" class="form-control eForm-control" id="public_live_key" name = "public_live_key" placeholder="Live Public Key" value="{{ $flutterwave['public_live_key'] }}" aria-label="Live Public Key" required>
                                </div>


                                <div class="fpb-7">
                                    <label for="secret_live_key" class="eForm-label">{{ get_phrase('Live Secrect Key') }}</label>
                                    <input type="text" class="form-control eForm-control" id="secret_live_key" name = "secret_live_key" placeholder="Live Secrect Key" value="{{ $flutterwave['secret_live_key'] }}" aria-label="Live Secrect Key" required>
                                </div>

                                <div class="fpb-7">
                                    <label for="encryption_live_key" class="eForm-label">{{ get_phrase('Live Encryption Key') }}</label>
                                    <input type="text" class="form-control eForm-control" id="encryption_live_key" name = "encryption_live_key" placeholder="Live Encryption Key" value="{{ $flutterwave['encryption_live_key'] }}" aria-label="Live Encryption Key" required>
                                </div>

                                <input type="hidden" id="method" name="method" value="flutterwave">


                                <div class="fpb-7 pt-2">
                                    <button type="submit" class="btn-form" onclick="">{{ get_phrase('Update') }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endif


@endsection
