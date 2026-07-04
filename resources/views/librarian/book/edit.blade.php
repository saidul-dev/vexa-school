<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('librarian.book.update', ['id' => $book_details->id]) }}">
    @csrf 
	<div class="form-row">
		<div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Book name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name = "name" value="{{ $book_details['name'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="author" class="eForm-label">{{ get_phrase('Author') }}</label>
            <input type="text" class="form-control eForm-control" id="author" name = "author" value="{{ $book_details['author'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="copies" class="eForm-label">{{ get_phrase('Number of copy') }}</label>
            <input type="number" class="form-control eForm-control" id="copies" name = "copies" min="0" value="{{ $book_details['copies'] }}" required>
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Update book info') }}</button>
        </div>
	</div>
</form>