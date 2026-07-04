@extends('install.index')
   
@section('content')
<?php if(isset($db_connection) && $db_connection != "") { ?>
  <div class="row ins-seven">
    <div class="col-md-8 col-md-offset-2">
      <div class="alert alert-danger">
        <strong>{{ $db_connection }}</strong>
      </div>
    </div>
  </div>
<?php } ?>
<div class="row ins-two">
  <div class="col-md-8 col-md-offset-2">
    <div class="card">
      <div class="card-body">
        <div class="panel panel-default ins-three" data-collapsed="0">
          <!-- panel body -->
          <div class="panel-body ins-four">
            <p class="ins-four">
              {{ phrase('Below you should enter your database connection details.').' '.phrase('If you’re not sure about these, contact your host.') }}
            </p>
            <br>
            <div class="row">
              <div class="col-md-12">
                <form class="form-horizontal form-groups" method="post"
                  action="{{ route('step3') }}">
                  @csrf 
                  <hr>
                  <div class="form-group">
            				<label class="col-sm-3 control-label">{{ phrase('Database Name') }}</label>
            				<div class="col-sm-5">
            					<input type="text" class="form-control eForm-control" name="dbname" placeholder=""
                        required autofocus>
            				</div>
                    <div class="col-sm-4 ins-six">
                      {{ phrase('The name of the database you want to use with this application') }}
                    </div>
            			</div>
                  <hr>
                  <div class="form-group">
            				<label class="col-sm-3 control-label">{{ phrase('Username') }}</label>
            				<div class="col-sm-5">
            					<input type="text" class="form-control eForm-control" name="username" placeholder=""
                        required>
            				</div>
                    <div class="col-sm-4 ins-six">
                      {{ phrase('Your database Username') }}
                    </div>
            			</div>
                  <hr>
                  <div class="form-group">
            				<label class="col-sm-3 control-label">{{ phrase('Password') }}</label>
            				<div class="col-sm-5">
            					<input type="password" class="form-control eForm-control" name="password" placeholder="">
            				</div>
                    <div class="col-sm-4 ins-six">
                      {{ phrase('Your database Password') }}
                    </div>
            			</div>
                  <hr>
                  <div class="form-group">
            				<label class="col-sm-3 control-label">{{ phrase('Database Host') }}</label>
            				<div class="col-sm-5">
            					<input type="text" class="form-control eForm-control" name="hostname" placeholder=""
                        required>
            				</div>
                    <div class="col-sm-4 ins-six">
                      {{ phrase("If 'localhost' does not work, you can get the hostname from web host") }}
                    </div>
            			</div>
                  <hr>
                  <div class="form-group">
            				<label class="col-sm-3 control-label"></label>
            				<div class="col-sm-7">
            					<button type="submit" class="btn btn-info">{{ phrase('Continue') }}</button>
            				</div>
            			</div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection