<!-- paypal -->
<div class="tab-pane fade " id="v-pills-paypal" role="tabpanel" aria-labelledby="v-pills-paypal-tab" tabindex="0">

    <form action="{{ route('superadmin.paypal.subscription',['success_url'=> 'admin_subscription_fee_success_payment','cancle_url' => 'admin_subscription_fee_fail_payment','payment_method'=>'paypal']) }}" method="post" class="paypal-form form">
        @csrf
        <hr class="border mb-4">
        <input type="hidden" name="expense_type" value="<?=$selected_package['name']?>">
        <input type="hidden" name="expense_id" value="{{ $selected_package['id'] }}">
        <input type="hidden" name="amount" value="{{ $selected_package['price'] }}">
        <input type="hidden" name="days" value="{{ $selected_package['days'] }}">
        <button type="submit" class="paypal_btn">
            {{ get_phrase('Pay by Paypal ') }}
        </button>
    </form>

</div>
