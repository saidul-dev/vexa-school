@extends('install.index')
   
@section('content')
<div class="row ins-two">
  <div class="col-md-8 col-md-offset-2">
    <div class="card">
      <div class="card-body">
        <div class="panel panel-default ins-three" data-collapsed="0">
    			<!-- panel body -->
    			<div class="panel-body ins-four">
            <p class="ins-four">
              {{ phrase('Welcome to Ekattor School Management System Installation. You will need to know the following items before proceeding.') }}
            </p>
            <ol>
              <li>{{ phrase('Codecanyon purchase code') }}</li>
              <li>{{ phrase('Database Name') }}</li>
              <li>{{ phrase('Database Username') }}</li>
              <li>{{ phrase('Database Password') }}</li>
              <li>{{ phrase('Database Hostname') }}</li>
            </ol>
            <p class="ins-four">
              {{ phrase('We are going to use the above information to write database.php file which will connect the application to your database.').' '.phrase('During the installation process, we will check if the files that are needed to be written') }}
              (<strong>{{ phrase('config/database.php') }}</strong>) {{ phrase('have') }}
              <strong>{{ phrase('write permission') }}</strong>. {{ phrase('We will also check if') }} <strong>{{ phrase('curl') }}</strong> {{ phrase('and') }} <strong>{{ phrase('php mail functions') }}</strong>
              {{ phrase('are enabled on your server or not.') }}
            </p>
            <p class="ins-four">
              {{ phrase('Gather the information mentioned above before hitting the start installation button. If you are ready....') }}'
            </p>
            <br>
            <p>
              <a href="{{ route('step1') }}" class="btn btn-info">
                {{ phrase('Start Installation Process') }}
              </a>
            </p>
    			</div>
    		</div>
      </div>
  </div>
</div>
@endsection
