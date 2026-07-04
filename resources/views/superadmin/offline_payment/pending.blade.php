@extends('superadmin.navigation')
   
@section('content')

<?php use App\Models\User; ?>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>{{ get_phrase('Pending Request') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
			@if(count($payment_histories) > 0)
				<table class="table eTable">
					<thead>
	                    <th>#</th>
	                    <th>{{ get_phrase('User Name') }}</th>
	                    <th>{{ get_phrase('Price') }}</th>
	                    <th>{{ get_phrase('Payment For') }}</th>
	                    <th>{{ get_phrase('Payment Document') }}</th>
	                    <th>{{ get_phrase('Status') }}</th>
	                    <th>{{ get_phrase('Action') }}</th>
	                </thead>
	                <tbody>
	                	@foreach($payment_histories as $payment_history)
	                	<?php $user = User::find($payment_history->user_id); ?>
	                		<tr>
	                			<td>{{ $loop->index + 1 }}</td>
	                			<td>{{ $user->name }}</td>
	                			<td>{{ $payment_history->amount }}</td>
	                			<td>{{ ucwords($payment_history->payment_type) }}</td>
	                			<td>{{ $payment_history->document_image }}</td>
	                			<td><span class="bg bg-info">{{ get_phrase('Pending') }}</span></td>
	                			<td>
		                			<div class="dropdown text-center">
			                            <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
			                            <div class="dropdown-menu dropdown-menu-right">
			                                <!-- item-->
			                                <a href="javascript:;" class="dropdown-item" onclick="confirmModal('{{ route('superadmin.offline_payment.status', ['status' => 'approve', 'id' => $payment_history->id]) }}', 'undefined');">{{ get_phrase('Approve') }}</a>
			                                <!-- item-->
                                            <a href="javascript:;" class="dropdown-item" onclick="confirmModal('{{ route('superadmin.offline_payment.status', ['status' => 'suspended', 'id' => $payment_history->id]) }}', 'undefined');">{{ get_phrase('Suspended') }}</a>
                                            <!-- item-->
                                            <a href="javascript:;" class="dropdown-item" onclick="confirmModal('{{ route('superadmin.offline_payment.delete', ['id' => $payment_history->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
			                            </div>
			                        </div>
			                    </td>
	                		</tr>
	                	@endforeach
	                </tbody>
				</table>
			@else
				<div class="empty_box center">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                </div>
			@endif
		</div>
	</div>
</div>
@endsection