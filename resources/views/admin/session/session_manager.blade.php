@extends('admin.navigation')
   
@section('content')

<?php 

use App\Models\Session;

$current_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$running_session = Session::find($current_session);

?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Session Manager') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Settings') }}</a></li>
                        <li><a href="#">{{ get_phrase('Session Manager') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.create.session') }}', '{{ get_phrase('Create Session') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add Session') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-12">
		<div class="eSection-wrap">
			<div class="eMain">
				<div class="row">
					<div class="col-md-6 pb-3">
						<div class="eAlert eBtn-blueish" role="alert">
							{{ get_phrase('Active session ') }}
                        	<span class="text-white eBtn-green p-1" id="active_session">{{ $running_session->session_title }}</span>
						</div>
						<div class="row">
							<div class="col-9">
								<select class="form-select eForm-select eChoice-multiple-with-remove" id="session_dropdown">
			                        <option value = "">{{ get_phrase('Select a session') }}</option>
			                        @foreach($sessions as $session)
			                        	<option value="{{ $session->id }}" {{ $session->status == 1 ? 'selected':'' }}>{{ $session->session_title }}</option>
			                    	@endforeach
			                	</select>
			                </div>
			                <div class="col-3">
		                		<button type="button" class="eBtn eBtn btn-secondary" onclick="makeSessionActive()"> <i class="mdi mdi-check"></i>{{ get_phrase('Activate') }}</button>
		                	</div>
		                </div>
					</div>
					<div class="col-md-6 pb-3">
						<table id="basic-table" class="table eTable">
			                <thead>
			                    <tr>
			                        <th>{{ get_phrase('Session title') }}</th>
			                        <th>{{ get_phrase('Status') }}</th>
			                        <th class="text-center">{{ get_phrase('Options') }}</th>
			                    </tr>
			                </thead>
			                <tbody class="table_body">
			                    <?php
			                    foreach($sessions as $session):?>
			                    <tr>
			                        <td><strong>{{ $session['session_title'] }}</strong></td>
			                        <td>
			                            <?php if($session['status'] == 1): ?>
			                                <span class="eBadge ebg-success">{{ get_phrase('Active') }}</span>
			                            <?php else: ?>
			                                <span class="eBadge ebg-danger">{{ get_phrase('Deactive') }}</span>
			                            <?php endif; ?>
			                        </td>
			                        <td class="text-start">
			                        	<div class="adminTable-action">
					                        <button
					                          type="button"
					                          class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
					                          data-bs-toggle="dropdown"
					                          aria-expanded="false"
					                        >
					                          {{ get_phrase('Actions') }}
					                        </button>
					                        <ul
					                          class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
					                        >
					                          <li>
					                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.session', ['id' => $session->id]) }}', '{{ get_phrase('Edit Session') }}')">{{ get_phrase('Edit') }}</a>
					                          </li>
					                          <li>
					                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.session.delete', ['id' => $session->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
					                          </li>
					                        </ul>
					                    </div>
			                        </td>
			                    </tr>
				                <?php endforeach; ?>
				            </tbody>
				        </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

  "use strict";

	function makeSessionActive() {
	    var session_id = $('#session_dropdown').val();
	    let url = "{{ route('admin.session_manager.active_session', ['id' => ":session_id"]) }}";
    	url = url.replace(":session_id", session_id);
	    $.ajax({
	        type : 'GET',
	        url: url,
	        processData: false,
	        contentType: false,
	        dataType: 'json',
	        success : function(response) {
	            (response.status === true) ? toastr.success(response.notification) : toastr.error(response.notification);
	            location.reload();
	        }
	    });
	}
</script>

@endsection