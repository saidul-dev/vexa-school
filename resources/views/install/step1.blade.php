@extends('install.index')
   
@section('content')
<?php
  $db_file_write_perm = is_writable('config/database.php');
  $routes_file_write_perm = is_writable('routes/web.php');
  $curl_enabled = function_exists('curl_version');
  if ($db_file_write_perm == false || $routes_file_write_perm == false || $curl_enabled == false) {
    $valid = false;
  } else {
    $valid = true;
  }
?>
<div class="row ins-two">
  	<div class="col-md-8 col-md-offset-2">
    	<div class="card">
      		<div class="card-body">
        		<div class="panel panel-default ins-three" data-collapsed="0">
    				<!-- panel body -->
    				<div class="panel-body ins-four">
			            <p class="ins-four">
			              {{ phrase('We ran diagnosis on your server.').' '.phrase('Review the items that have a red mark on it.').' '.phrase('If everything is green, you
			              are good to go to the next step.') }}
			            </p>
		            	<br>
			            <p class="ins-four">
			              <?php if ($db_file_write_perm == true) { ?>
			                <i class="bi bi-check-lg ins-nine"></i>
			              <?php } else { ?>
			                <i class="bi bi-x-lg ins-ten"></i>
			              <?php } ?>
			              <strong>{{ phrase('config/database.php') }} </strong>: {{ phrase('file has write permission') }}
			            </p>
			            <p class="ins-four">
			              <?php if ($routes_file_write_perm == true) { ?>
			                <i class="bi bi-check-lg ins-nine"></i>
			              <?php } else { ?>
			                <i class="bi bi-x-lg ins-ten"></i>
			              <?php } ?>
			              <strong>{{ phrase('routes/web.php') }} </strong>: {{ phrase('file has write permission') }}
			            </p>
			            <p class="ins-four">
			              <?php if ($curl_enabled == true) { ?>
			                <i class="bi bi-check-lg ins-nine"></i>
			              <?php } else { ?>
			                <i class="bi bi-x-lg ins-ten"></i>
			              <?php } ?>
			              <strong>{{ phrase('Curl Enabled') }}</strong>
			            </p>
			            <p class="ins-four">
			              <strong>{{ phrase('To continue the installation process, all the above requirements are needed to be checked') }}</strong>
			            </p>
		            	<br>
			            <?php if ($valid == true) { ?>
			              <p>
			                <?php if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') { ?>
			                  <a href="{{ route('step3') }}" class="btn btn-info">
			                    {{ phrase('Continue') }}
			                  </a>
			                <?php } else { ?>
			                  <a href="{{ route('step2') }}" class="btn btn-info">
			                    {{ phrase('Continue') }}
			                  </a>
			                <?php } ?>
			              </p>
			            <?php } ?>

			            <?php if ($valid != true) { ?>
			              <p>
			                <?php if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') { ?>
			                  <a href="{{ route('step3') }}" class="btn btn-info" disabled>
			                    {{ phrase('Continue') }}
			                  </a>
			                <?php } else { ?>
			                  <a href="{{ route('step2') }}" class="btn btn-info" disabled>
			                    {{ phrase('Continue') }}
			                  </a>
			                <?php } ?>
			                <a href="{{ route('step1') }}" class="btn btn-info" >
			                  <i class="mdi mdi-refresh"></i>{{ phrase('Reload') }}
			                </a>
			              </p>
			            <?php } ?>
    				</div>
    			</div>
      		</div>
    	</div>
  	</div>
</div>
@endsection