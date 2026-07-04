<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.expenses.update', ['id' => $expense_details->id]) }}">
    @csrf 
    <div class="form-row">
        <div class="fpb-7">
            <label for="date" class="eForm-label">{{ get_phrase('Date') }}</label>
            <input type="text" class="form-control eForm-control inputDate" id="date" name = "date" value="{{ date('m/d/Y', $expense_details['date']) }}" required>
        </div>
        
        <div class="fpb-7">
            <label for="amount" class="eForm-label">{{ get_phrase('Amount').' ('.school_currency().')' }}</label>
            <input type="text" class="form-control eForm-control" id="amount" name = "amount" value="{{ $expense_details['amount'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="expense_category_id" class="eForm-label">{{ get_phrase('Expense category') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="expense_category_id" id = "expense_category_id_on_create" required>
                <option value="">{{ get_phrase('Select an expense category') }}</option>
                @foreach ($expense_categories as $expense_category)
                    <option value="{{ $expense_category->id }}" {{ $expense_details->expense_category_id == $expense_category['id'] ? 'selected':'' }}>{{ $expense_category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Update expense') }}</button>
        </div>
    </div>
</form>

<script type="text/javascript">

  "use strict";

    $(function () {
      $('.inputDate').daterangepicker(
        {
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format("YYYY"), 10),
        },
        function (start, end, label) {
          var years = moment().diff(start, "years");
        }
      );
    });

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });
    
</script>