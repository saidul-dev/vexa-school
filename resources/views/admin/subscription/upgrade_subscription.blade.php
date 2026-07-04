@extends('admin.navigation')

@section('content')
<style>
	.packageBox{
		min-width: 0;
	}
	.total_std{
		font-size: 12px;
	}
	.packageFeatures li{
		padding-bottom: 1px;
	}
	.packageFeatures{
		padding-bottom: 15px;
	}
	
</style>
@php
$subscription = DB::table('subscriptions')->latest()->first();


@endphp
<div class="mainSection-title">
	<div class="row">
	  <div class="col-12">
	    <div
	      class="d-flex justify-content-between align-items-center flex-wrap gr-15"
	    >
	      <div class="d-flex flex-column">
	        <h4>{{ get_phrase('Package List') }}</h4>
	        <ul class="d-flex align-items-center eBreadcrumb-2">
	          <li><a href="#">{{ get_phrase('Home') }}</a></li>
	          <li><a href="#">{{ get_phrase('Package List') }}</a></li>
	        </ul>
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- Start Package List -->
<div class="row">
	<div class="col-12">
	  <div class="eSection-packageList">
	    <div class="row flex-wrap">
	    @foreach($packages as $package)
			@if ( $subscription->active == '1')	
			@php
				$packagesPrice = $package->price;
				
				$subscriptionsPrice = $subscription->paid_amount;
				
				$upgradePrice = $packagesPrice - $subscriptionsPrice ;
				
				
				$subscribeStudent = $subscription->studentLimit;

				$packageStudent = $package->studentLimit;	

				if ($packageStudent == 'Unlimited' || $subscribeStudent == 'Unlimited' ) {
					
				}else{
					$upgradeStudent = $packageStudent - $subscribeStudent;	
				}
				
			@endphp

				@if ($packageStudent == 'Unlimited' && $packagesPrice < $subscriptionsPrice && $subscribeStudent == 'Unlimited')
						
				@else
				
				@if ($upgradePrice > 0 && ($packagesPrice > $subscriptionsPrice && $package->interval == 'life_time' || $subscribeStudent < $packageStudent && $package['id'] != $subscription->package_id))
					<div class="col-lg-3 col-md-6">
						
						<div class="packageBox text-center">
						<h4 class="packageTitle">{{ $package->name }}</h4>
						<p class="packagePrice">
							@if ($subscription->active == 1)
							<span>{{currency($upgradePrice)}}</span>/
							{{ currency($package->price) }}
							@else
							<span>{{ currency($package->price) }}</span>/
							@endif
							<br>
							@if($package['interval'] == 'life_time')
							{{ get_phrase('life time') }}
							@else
							<?php if($package['interval'] == 'Days'): ?>
								{{ $package['days'].' '.$package['interval'] }}
							<?php else: ?>
								{{ $package['interval'] }}
							<?php endif; ?>
							@endif
						</p>
						@if ($subscribeStudent == 'Unlimited' || $packageStudent == 'Unlimited' )
						<p class="total_std">Students: Unlimited</p>
						@else
						<p class="total_std">Upgrade Students: +{{$upgradeStudent}}</p>
						@endif
							@php
								$packages_features = json_decode($package->features);
							@endphp 

							<ul class="packageFeatures">
								@foreach ($packages_features as $packages_feature)
								<li class="mainFeature">{{ $packages_feature }}</li>
								@endforeach
								
							</ul>
							<p class="total_std mb-2">{{$package->description }}</p>
						<a href="{{ route('admin.subscription.payment',['package_id'=>$package['id'] ]) }}" class="packageSubs_btn">{{ get_phrase('Upgrade Subscribe') }}</a>
						
						</div>
					</div>	
					@endif
				@endif
			@endif
		@endforeach 
	    </div>
	  </div>
	</div>
</div>
@endsection
