@php
use App\Models\PaymentMethods;

$user_info = json_decode(auth()->user()->user_information);

$flutterwave=PaymentMethods::where(array('name' => 'flutterwave', 'school_id' => auth()->user()->school_id ))->first()->toArray();

$flutterwave_keys=json_decode($flutterwave['payment_keys']);
$flutterwave_key;
$flutterwave_secret;
if($flutterwave['mode']=="test")
{
    $flutterwave_key=$flutterwave_keys->test_key;
    $flutterwave_secret=$flutterwave_keys->test_secret_key;
}
elseif($flutterwave['mode']=="live")
{
    $flutterwave_key=$flutterwave_keys->public_live_key;
    $flutterwave_secret=$flutterwave_keys->secret_live_key;

}

@endphp
<div class="tab-pane fade " id="v-pills-flutterwave" role="tabpanel" aria-labelledby="v-pills-flutterwave-tab" tabindex="0">

    <form>
        <script src="https://checkout.flutterwave.com/v3.js"></script>
        <button type="button" id="start-payment-button" onclick="makePayment()" class="paypal_btn">
            {{ get_phrase('Pay by Flutterwave ') }}
        </button>
    </form>
    <script>
        function makePayment() {
            FlutterwaveCheckout({
              public_key: "{{ $flutterwave_key }}",
              tx_ref: "fee_id-{{ random(8) }}",
              amount: {{ $fee_details['total_amount'] }},
              currency: "{{ school_currency() }}",
              payment_options: "card, banktransfer, ussd",
              redirect_url: "{{ route('flutterwave.payment', ['user_id' => auth()->user()->id, 'expense_id' => $fee_details['id']]) }}",
              meta: {
                consumer_id: "{{ auth()->user()->id }}",
                consumer_mac: "92a3-912ba-1192a",
              },
              customer: {
                email: "{{ auth()->user()->email }}",
                phone_number: "{{ $user_info->phone }}",
                name: "{{ auth()->user()->name }}",
              },
              customizations: {
                expense_type: "{{ $fee_details['title'] }}",
                expense_id: "{{ $fee_details['id'] }}",
              },
            });
        }
    </script>

</div>
