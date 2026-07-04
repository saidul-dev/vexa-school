@extends('admin.navigation')
   
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ get_phrase('Dashboard') }}</div>
                <div class="card-body">
                    {{ get_phrase('You are Admin.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection