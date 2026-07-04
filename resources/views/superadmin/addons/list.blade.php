@extends('superadmin.navigation')
   
@section('content')
<?php $index = 0; ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Manage Addons') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Addons') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('superadmin.addon.install') }}', '{{ get_phrase('Install addon') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add new addon') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
        	@if(count($addons) > 0)
        		<table class="table eTable">
					<thead>
	                    <th>#</th>
	                    <th>{{ get_phrase('Bundle name') }}</th>
	                    <th>{{ get_phrase('Feature') }}</th>
	                    <th>{{ get_phrase('Status') }}</th>
	                    <th>{{ get_phrase('Version') }}</th>
	                    <th class="text-center">{{ get_phrase('Action') }}</th>
	                </thead>
	                <tbody>
	                	@foreach($addons as $addon)
	                		@if($addon->parent_id == "")
	                		<?php $index++; ?>
	                		<tr>
	                			<td>{{ $index }}</td>
	                			<td>
	                				<strong>{{ $addon->title }}</strong>
	                			</td>
	                			<td>
	                				<?php $features = explode('-', $addon['features']);?>
	                				@foreach($features as $feature)
	                					{{ $feature }}
	                					<br>
	                				@endforeach		
	                			</td>
	                			<td>
	                				<?php if ($addon->status != '1'): ?>
			                            <span class="eBadge ebg-danger">{{ get_phrase('Deactive') }}</span>
			                        <?php else: ?>
			                            <span class="eBadge ebg-success">{{ get_phrase('Active') }}</span>
			                        <?php endif; ?>
	                			</td>
	                			<td>{{ $addon->version }}</td>
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
	                                    <?php if ($addon->status != '1'): ?>
											<li>
												<a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('superadmin.addon.status', ['id' => $addon->id]) }}', 'undefined');">{{ get_phrase('Active') }}</a>
											</li>
	                                    <?php else: ?>
	                                    	<li>
												<a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('superadmin.addon.status', ['id' => $addon->id]) }}', 'undefined');">{{ get_phrase('Deactive') }}</a>
											</li>
	                                    <?php endif; ?>
	                                      <li>
	                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('superadmin.addon.delete', ['id' => $addon->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
	                                      </li>
	                                    </ul>
	                                </div>
			                    </td>
	                		</tr>
	                		@endif
	                	@endforeach
	                </tbody>
				</table>
			@else
				<div class="empty_box center pb-4">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                    {{ get_phrase('No data found') }}
                </div>
			@endif
        </div>
	</div>
</div>
@endsection