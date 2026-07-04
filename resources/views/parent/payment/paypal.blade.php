             <!-- paypal -->
             <div class="tab-pane fade " id="v-pills-paypal" role="tabpanel" aria-labelledby="v-pills-paypal-tab" tabindex="0" >

                <form action="{{ route('payment.paypal.pay',['success_url'=>'parent.student_fee_success_payment','cancle_url' =>'parent.student_fee_fail_payment','payment_method'=>'paypal']) }}" method="post" class="paypal-form form">
                    @csrf
                    <hr class="border mb-4">
                    <input type="hidden" name="expense_type" value="<?=$fee_details['title']?>">
                    <input type="hidden" name="expense_id" value="{{ $fee_details['id'] }}">
                    <input type="hidden" name="amount" value="{{ $fee_details['total_amount'] }}">
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <button type="submit" class="paypal_btn">
                        {{ get_phrase('Pay by Paypal ') }}
                    </button>
                </form>

                </div>
