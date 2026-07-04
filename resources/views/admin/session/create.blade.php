<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.session_manager.create') }}">
        @csrf 
        <div class="form-row">
            <div class="fpb-7">
                <label for="session_title" class="eForm-label">{{ get_phrase('Session title') }}</label>
                <input type="number" min="0" class="form-control eForm-control" id="session_title" name = "session_title" required>
            </div>
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Create session') }}</button>
            </div>
		</div>
	</form>
</div>