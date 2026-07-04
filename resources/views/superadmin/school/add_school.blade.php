@extends('superadmin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Create School') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Schools') }}</a></li>
                        <li><a href="#">{{ get_phrase('Create School') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
        	<div class="title">
				<h3>{{ get_phrase('School Form') }}</h3>
				<p>{{ get_phrase('Provide all the information required for your school.').' '.get_phrase('Also provide a admin information with email and passwoard.').' '.get_phrase('So that admin can access the created school.') }}</p>
			</div>

			<div class="eMain">
				<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.school.create') }}">
					@csrf 
					<div class="row">
						<div class="col-md-6 pb-3">
							<div class="eForm-layouts">
								<p class="column-title">{{ get_phrase('SCHOOL INFO') }}</p>
								<div class="fpb-7">
									<label for="school_name" class="eForm-label">{{ get_phrase('School Name') }}</label>
									<input type="text" class="form-control eForm-control" id="school_name" name="school_name" required>
								</div>
								<div class="fpb-7">
									<label for="school_address" class="eForm-label">{{ get_phrase('School Address') }}</label>
									<input type="text" class="form-control eForm-control" id="school_address" name="school_address" required>
								</div>
								<div class="fpb-7">
									<label for="school_email" class="eForm-label">{{ get_phrase('School Email') }}</label>
									<input type="text" class="form-control eForm-control" id="school_email" name="school_email" required>
								</div>
								<div class="fpb-7">
									<label for="school_phone" class="eForm-label">{{ get_phrase('School Phone') }}</label>
									<input type="tel" class="form-control eForm-control" id="school_phone" name="school_phone" required>
								</div>
								<div class="fpb-7">
					                <label for="school_info" class="eForm-label">{{ get_phrase('School info') }}</label>
					                <textarea class="form-control eForm-control" id="school_info" name = "school_info" rows="4" required></textarea>
					            </div>
								<div class="fpb-7">
					                <label for="school_logo" class="eForm-label">{{ get_phrase('School Logo') }}</label>
					                <input class="form-control eForm-control-file" type="file" id="school_logo" name="school_logo" accept="image/*">
					            </div>
							</div>
						</div>
						<div class="col-md-6 pb-3">
							<div class="eForm-layouts">
								<p class="column-title">{{ get_phrase('ADMIN INFO') }}</p>
								<div class="fpb-7">
									<label for="admin_name" class="eForm-label">Admin Name</label>
									<input type="text" class="form-control eForm-control" id="admin_name" name="admin_name" required>
								</div>
								<div class="fpb-7">
					                <label for="gender" class="eForm-label">{{ get_phrase('Gender') }}</label>
					                <select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove">
					                    <option value="">{{ get_phrase('Select a gender') }}</option>
					                    <option value="Male">{{ get_phrase('Male') }}</option>
					                    <option value="Female">{{ get_phrase('Female') }}</option>
					                </select>
					            </div>
								<div class="fpb-7">
					                <label for="blood_group" class="eForm-label">{{ get_phrase('Blood group') }}</label>
					                <select name="blood_group" id="blood_group" class="form-select eForm-select eChoice-multiple-with-remove" required>
					                    <option value="">{{ get_phrase('Select a blood group') }}</option>
					                    <option value="a+">{{ get_phrase('A+') }}</option>
					                    <option value="a-">{{ get_phrase('A-') }}</option>
					                    <option value="b+">{{ get_phrase('B+') }}</option>
					                    <option value="b-">{{ get_phrase('B-') }}</option>
					                    <option value="ab+">{{ get_phrase('AB+') }}</option>
					                    <option value="ab-">{{ get_phrase('AB-') }}</option>
					                    <option value="o+">{{ get_phrase('O+') }}</option>
					                    <option value="o-">{{ get_phrase('O-') }}</option>
					                </select>
					            </div>
								<div class="fpb-7">
									<label for="admin_address" class="eForm-label">{{ get_phrase('Admin Address') }}</label>
									<input type="text" class="form-control eForm-control" id="admin_address" name="admin_address" required>
								</div>
								<div class="fpb-7">
									<label for="admin_phone" class="eForm-label">{{ get_phrase('Admin Phone Number') }}</label>
									<input type="tel" name="admin_phone" class="form-control eForm-control" id="admin_phone" required>
								</div>
								<div class="fpb-7">
					                <label for="photo" class="eForm-label">{{ get_phrase('Photo') }}</label>
					                <input class="form-control eForm-control-file" type="file" id="photo" name="photo" accept="image/*" required>
					            </div>
								<div class="fpb-7">
									<label for="admin_email" class="eForm-label">{{ get_phrase('Admin Email') }}</label>
									<input type="text" class="form-control eForm-control" id="admin_email" name="admin_email" required>
								</div>
								<div class="fpb-7">
									<label for="admin_password" class="eForm-label">{{ get_phrase('Admin Password') }}</label>
									<input type="password" class="form-control eForm-control" id="admin_password" name="admin_password" required>
								</div>
								<div class="pt-2">
									<button type="submit" class="btn-form float-end">{{ get_phrase('Submit') }}</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
        </div>
    </div>
</div>
@endsection