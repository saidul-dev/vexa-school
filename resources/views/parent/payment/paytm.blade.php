
                    <div class="tab-pane fade " id="v-pills-paytm" role="tabpanel" aria-labelledby="v-pills-paytm-tab" tabindex="0">

                        <form action="{{ route('paytm.payment',['success_url'=> 'student.student_fee_success_payment_student','cancle_url' => 'student.student_fee_fail_payment_student','payment_method'=>'paytm']) }}" method="post" class="paypal-form form">
                            @csrf
                            <hr class="border mb-4">
                            <input type="hidden" name="expense_type" value="{{ $fee_details['title'] }}">
                            <input type="hidden" name="expense_id" value="{{ $fee_details['id'] }}">
                            <input type="hidden" name="amount" value="{{ $fee_details['total_amount'] }}">
                            <input type="hidden" name="user_id" value="<?=auth()->user()->id?>">
                            <input type="hidden" name="school_id" value="<?=auth()->user()->school_id?>">
                            <button type="submit" class="paypal_btn">
                                {{ get_phrase('Pay by Paytm ') }}
                            </button>
                        </form>

                    </div>


