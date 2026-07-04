@extends('superadmin.navigation')
   
@section('content')

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Language Settings') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Settings') }}</a></li>
                        <li><a href="#">{{ get_phrase('Language settings') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap pb-2">
        	<ul class="nav nav-tabs eNav-Tabs-custom"id="myTab"role="tablist" >

        		<?php if(isset($edit_profile)):?>
        		<li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="upcoming-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#editphrase"
                    type="button"
                    role="tab"
                    aria-controls="editphrase"
                    aria-selected="false"
                  >
                  {{ get_phrase('Edit phrase ') }}
                    <span></span>
                  </button>
                </li>
                <?php endif;?>

                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link {{ empty($edit_profile) ? 'active':'' }}"
                    id="upcoming-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#languagelist"
                    type="button"
                    role="tab"
                    aria-controls="languagelist"
                    aria-selected="false"
                  >
                  {{ get_phrase('Language list ') }}<p class="badge bg-success ">
                    {{ count($languages) }}
                </p>
                    <span></span>
                  </button>
                </li>


                <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="archive-tab"
                      data-bs-toggle="tab"
                      data-bs-target="#addlanguage"
                      type="button"
                      role="tab"
                      aria-controls="addlanguage"
                      aria-selected="false"
                    >
                    {{ get_phrase('Add language') }}
                      <span></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content eNav-Tabs-content" id="nav-tabContent">

            	<?php if (isset($edit_profile)):
					$current_editing_language	=	$edit_profile;
				?>
            	<div class="tab-pane fade {{ !empty($edit_profile) ? 'show active':'' }} pt-3  " id="editphrase" role="tabpanel" aria-labelledby="editphrase-tab">
					<div class="row">
						<?php foreach ($phrases as $phrase): ?>
						<div class="col-md-3">
	                      <div class="eCard eCard-2">
	                        <div class="eCard-body">
	                          <p class="eCard-text text-center">
	                          	<label for="text" class="eForm-label">{{ $phrase->phrase }}</label>
	                            <input type="text" class="form-control eForm-control" name="updated_phrase" id = "phrase-<?php echo $phrase->id; ?>" value="<?php echo $phrase->translated; ?>">
	                          </p>
	                          <div class="d-flex flex-column align-items-start align-items-md-center">
	                            <a href="javascript:void(0)" class="eBtn eBtn-blue" id="btn-<?php echo $phrase->phrase; ?>" onclick="updatePhrase('<?php echo $phrase->phrase; ?>', '{{ $current_editing_language }}', '<?php echo $phrase->id; ?>')">{{ get_phrase('Update') }}</a>
	                          </div>
	                        </div>
	                      </div>
	                    </div>
	                    <?php endforeach; ?>
						
					</div>
            	</div>
            	<?php endif;?>

            	<div class="tab-pane fade {{ empty($edit_profile) ? 'show active':'' }} " id="languagelist" role="tabpanel" aria-labelledby="languagelist-tab">
            		<div class="table-responsive-sm">
						<table class="table eTable">
							<thead>
								<tr>
									<th>{{ get_phrase('Language') }}</th>
									<th>{{ get_phrase('Option') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($languages as $language)
								<tr>
									<td>{{ ucwords($language->name) }}</td>
									<td>
										<a href="{{ route('superadmin.language.manage', ['language' => $language->name]) }}">
											<button type="button" class="btn-form btn-sm">{{ get_phrase('Edit phrase') }}</button>
										</a>
										@if($language->name != 'english')
										<button type="button" class="btn-form btn-danger btn-sm" onclick="confirmModal('{{ route('superadmin.language.delete', ['name' => $language->name]) }}', 'undefined')">{{ get_phrase('Delete language') }}</button>
										@else
										<button type="button" class="btn-form btn-danger btn-sm" onclick="notify()">{{ get_phrase('Delete language') }}</button>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
            	</div>

            	<div class="tab-pane fade pt-3  " id="addlanguage" role="tabpanel" aria-labelledby="addlanguage-tab">
            		<div class="row">
						<div class="col-xl-5">
							<form class="" action="{{ route('superadmin.language.add') }}" method="post">
								@csrf
								<div class="fpb-7">
									<label for="language" class="eForm-label">{{ get_phrase('Add new language') }}</label>
									<input type="text" id="language" required name="language" class="form-control eForm-control" placeholder="{{ get_phrase('No special character or space is allowed') }}">
									<p class="eCard-text">
			                            {{ get_phrase('Valid examples').' : French, Spanish, Bengali etc' }}
			                        </p>
								</div>
								<button type="submit" class="btn-form">{{ get_phrase('Save') }}</button>
							</form>
						</div>
					</div>
            	</div>
            </div>
        </div>
    </div>
</div>
@endsection

<script type="text/javascript">
	
	"use strict";

	function notify() {
		toastr.warning('{{ get_phrase('System default language can not be removed') }}');
	}

	function updatePhrase(phrase, language, phrase_id) {
		$('#btn-'+phrase).text('...');
		var updatedValue = $('#phrase-'+phrase_id).val();
		let url = "{{ route('superadmin.language.update_phrase', ['language' => ":language"]) }}";
		url = url.replace(":language", language);
		$.ajax({
			type : "POST",
			url  : url,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data : {updatedValue : updatedValue, currentEditingLanguage : language, phrase : phrase},
			success : function(response) {
				$('#btn-'+phrase).html('<i class = "bi bi-check-circle"></i>');
				toastr.success('{{ get_phrase('Phrase updated') }}');
			}
		});
	}

</script>