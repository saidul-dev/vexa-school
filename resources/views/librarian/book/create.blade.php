<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('librarian.create.book') }}">
    @csrf 
	<div class="form-row">
		<div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Book name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name = "name" required>
        </div>

        <div class="fpb-7">
            <label for="author" class="eForm-label">{{ get_phrase('Author') }}</label>
            <input type="text" class="form-control eForm-control" id="author" name = "author" required>
        </div>

        <div class="fpb-7">
            <label for="copies" class="eForm-label">{{ get_phrase('Number of scopy') }}</label>
            <input type="number" class="form-control eForm-control" id="copies" name = "copies" min="0" required>
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Save book') }}</button>
        </div>
	</div>
</form>