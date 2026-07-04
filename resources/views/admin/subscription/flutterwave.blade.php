@php

$user_info = json_decode(auth()->user()->user_information);

$flutterwave=get_settings('flutterwave');

$flutterwave_keys=json_decode($flutterwave);
$flutterwave_key;
$flutterwave_secret;
if($flutterwave_keys->mode=="test")
{
    $flutterwave_key=$flutterwave_keys->test_key;
    $flutterwave_secret=$flutterwave_keys->test_secret_key;
}
elseif($flutterwave_keys->mode=="live")
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
              tx_ref: "sub_ref-{{ random(8) }}",
              amount: "{{ $selected_package['price'] }}",
              currency: "{{ currency() }}",
              payment_options: "card, banktransfer, ussd",
              redirect_url: "{{ route('superadmin.flutterwave.subscription', ['user_id' => auth()->user()->id, 'package_id' => $selected_package['id']]) }}",
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
                package_type: "{{ $selected_package['name'] }}",
                package_id: "{{ $selected_package['id'] }}",
              },
            });
        }
    </script>
</div>
