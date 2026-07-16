<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.expenses.update', ['id' => $expense_details->id]) }}">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="title" class="eForm-label">{{ get_phrase('Title') }}</label>
            <input type="text" class="form-control eForm-control" id="title" name="title" value="{{ $expense_details['title'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="date" class="eForm-label">{{ get_phrase('Date') }}</label>
            <input type="text" class="form-control eForm-control inputDate" id="date" name = "date" value="{{ date('m/d/Y', $expense_details['date']) }}" required>
        </div>

        <div class="fpb-7">
            <label for="amount" class="eForm-label">{{ get_phrase('Amount').' ('.school_currency().')' }}</label>
            <input type="text" class="form-control eForm-control" id="amount" name = "amount" value="{{ $expense_details['amount'] }}" required>
        </div>

        <div class="fpb-7">
            <label for="account_head_id" class="eForm-label">{{ get_phrase('Expense head') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="account_head_id" id = "account_head_id_on_create" required>
                <option value="">{{ get_phrase('Select an expense head') }}</option>
                @foreach ($expense_heads as $expense_head)
                    <option value="{{ $expense_head->id }}" {{ $expense_details->account_head_id == $expense_head['id'] ? 'selected':'' }}>{{ $expense_head->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="fpb-7">
            <label for="payment_account_head_id" class="eForm-label">{{ get_phrase('Paid from') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="payment_account_head_id" id = "payment_account_head_id_on_create" required>
                <option value="">{{ get_phrase('Select an account') }}</option>
                @foreach ($asset_heads as $asset_head)
                    <option value="{{ $asset_head->id }}" {{ $expense_details->payment_account_head_id == $asset_head['id'] ? 'selected':'' }}>{{ $asset_head->name }}</option>
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
