<?php

use App\Models\PaymentMethods;

$razorpay=get_settings('razorpay');

        $razorpay_keys=json_decode($razorpay);
        $razorpay_key;
        $razorpay_secret;
        if($razorpay_keys->mode=="test")
        {
            $razorpay_key=$razorpay_keys->test_key;
            $razorpay_secret=$razorpay_keys->test_secret_key;
        }
        elseif($razorpay_keys->mode=="live")
        {
            $razorpay_key=$razorpay_keys->live_key;
            $razorpay_secret=$razorpay_keys->live_secret_key;

        }


?>



<div class="tab-pane fade " id="v-pills-razorpay" role="tabpanel" aria-labelledby="v-pills-razorpay-tab" tabindex="0">

    <form action="{{ route('superadmin.razorpay.subscription',['success_url'=> 'admin_subscription_fee_success_payment','cancle_url' => 'admin_subscription_fee_fail_payment','payment_method'=>'razorpay']) }}" method="post" class="paypal-form form">
        @csrf
        <hr class="border mb-4">
        <input type="hidden" name="expense_type" value="<?=$selected_package	['name']?>">
        <input type="hidden" name="expense_id" value="{{ $selected_package	['id'] }}">
        <input type="hidden" name="amount" value="{{ $selected_package	['price'] }}">
        <input type="hidden" name="days" value="{{ $selected_package['days'] }}">


        <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="<?= $razorpay_key?>" data-amount={{ $selected_package ['price']*100 }} data-buttontext="Pay by razorpay" data-name={{ $selected_package ['name'] }} data-description={{ $user_info['name'] }} data-image="https://www.itsolutionstuff.com/frontTheme/images/logo.png" data-prefill.name={{ $user_info['name'] }} data-prefill.email={{ $user_info['email'] }} data-theme.color="<?= $razorpay_keys->theme_color?>">
        </script>

    </form>
    <style>
        .razorpay-payment-button {


            display: flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            max-width: 175px;
            height: 55px;
            margin-top: 50px;
            margin-left: auto;
            padding: 12px 18px;
            border: 1px solid transparent;
            border-radius: 5px;
            background-color: #00a3ff;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            transition: all 0.3s;
        }
    </style>

</div>
