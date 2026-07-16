@if(count($incomes) > 0)
<div class="table-responsive tScrollFix pb-2" id="income_report">
    <table id="basic-datatable" class="table eTable">
        <thead>
          <tr>
            <th>{{ get_phrase('Date') }}</th>
            <th>{{ get_phrase('Title') }}</th>
            <th>{{ get_phrase('Amount') }}</th>
            <th>{{ get_phrase('Income head') }}</th>
            <th class="text-end">{{ get_phrase('Option') }}</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($incomes as $income): ?>
            <tr>
                <td>{{ date('D, d-M-Y', $income['date']) }}</td>
                <td>{{ $income['title'] }}</td>
                <td>{{ school_currency($income['amount']) }}</td>
                <td>
                    <?php $account_head = \App\Models\AccountHead::find($income['account_head_id']); ?>
                    {{ $account_head->name ?? '' }}
                </td>
                <td class="text-start">
                    <div class="adminTable-action">
                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                          {{ get_phrase('Actions') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('accountant.edit.income', ['id' => $income['id']]) }}', '{{ get_phrase('Edit Income') }}')">{{ get_phrase('Edit') }}</a>
                          </li>
                          <li>
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('accountant.income.delete', ['id' => $income['id']]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                          </li>
                        </ul>
                    </div>
                </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
    </table>
</div>
@else
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        <span class="">{{ get_phrase('No data found') }}</span>
    </div>
@endif
