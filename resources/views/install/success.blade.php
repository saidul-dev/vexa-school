@extends('install.index')
   
@section('content')
<div class="row ins-two">
  <div class="col-md-8 col-md-offset-2">
    <div class="card">
      <div class="card-body">
        <div class="panel panel-default ins-three" data-collapsed="0">
    			<!-- panel body -->
    			<div class="panel-body ins-four">
            <h3>{{ phrase('Success') }}!!</h3>
            <br>
            <p class="ins-four">
              <strong>{{ phrase('Installation was successfull.').' '.phrase('Please login to continue..') }}</strong>
            </p>
            <br>
            <table>
              <tbody>
                <tr>
                  <td class="ins-eight"><strong>{{ phrase('Administrator Email') }} |</strong></td>
                  <td class="ins-eight">{{ $admin_email }}</td>
                </tr>
                <tr>
                  <td class="ins-eight"><strong>{{ phrase('Password') }} |</strong></td>
                  <td class="ins-eight">{{ phrase('Your chosen password') }}</td>
                </tr>
              </tbody>
            </table>
            <br>
            <p>
              <a href="{{ route('login') }}" class="btn btn-info">
                <i class="entypo-login"></i> &nbsp; {{ phrase('Log In') }}
              </a>
            </p>
    			</div>
    		</div>
      </div>
    </div>
  </div>
</div>
@endsection