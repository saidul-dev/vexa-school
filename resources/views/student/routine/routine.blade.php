@extends('student.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Routines') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Academic') }}</a></li>
                        <li><a href="#">{{ get_phrase('Routines') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            @include('student.routine.routine_list')
        </div>
    </div>
</div>
@endsection