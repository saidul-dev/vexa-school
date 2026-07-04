<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('accountant.expense_category.update', ['id' => $expense_category->id]) }}">
  @csrf 
  <div class="form-row">
    <div class="fpb-7">
      <label for="name" class="eForm-label">{{ get_phrase('Expense category name') }}</label>
      <input type="text" class="form-control eForm-control" id="name" name = "name" value="{{ $expense_category->name }}" required>
    </div>

    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit">{{ get_phrase('Update category') }}</button>
    </div>
  </div>
</form>
