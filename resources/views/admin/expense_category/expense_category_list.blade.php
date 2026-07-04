@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Expense Category') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
                        <li><a href="#">{{ get_phrase('Expense Category') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.expense_category.open_modal') }}', '{{ get_phrase('Create Expense Category') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add Expense Category') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-7 offset-md-2">
        <div class="eSection-wrap">
            @if(count($expense_categories) > 0)
            <div class="expense_category_content">
                <div class="table-responsive tScrollFix">
                    <table id="basic-datatable" class="table eTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ get_phrase('Name') }}</th>
                                <th scope="col" class="text-end">{{ get_phrase('Option') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expense_categories as $key => $expense_category)
                                <tr>
                                    <td>{{ $expense_categories->firstItem() + $key }}</td>
                                    <td>{{ $expense_category->name }}</td>
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
                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.expense_category', ['id' => $expense_category->id]) }}', '{{ get_phrase('Edit Expense Category') }}')">{{ get_phrase('Edit') }}</a>
                                              </li>
                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.expense.category_delete', ['id' => $expense_category->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                              </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $expense_categories->appends(request()->all())->links() !!}
                </div>
            </div>
            @else
            <div class="empty_box center">
              <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
              <br>
              <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection