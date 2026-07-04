<div class="tab-pane fade " id="v-pills-stripe" role="tabpanel" aria-labelledby="v-pills-stripe-tab" tabindex="0">

    <form action="{{ route('stripe.post',['success_url'=> 'student.student_fee_success_payment_student','cancle_url' => 'student.student_fee_fail_payment_student','payment_method'=>'stripe']) }}" method="post" class="paypal-form form">
        @csrf
        <hr class="border mb-4">
        <input type="hidden" name="expense_type" value="{{ $fee_details['title'] }}">
        <input type="hidden" name="expense_id" value="{{ $fee_details['id'] }}">
        <input type="hidden" name="amount" value="{{ $fee_details['total_amount'] }}">
        <button type="submit" class="paypal_btn">
            {{ get_phrase('Pay by Stripe ') }}
        </button>
    </form>

</div>
