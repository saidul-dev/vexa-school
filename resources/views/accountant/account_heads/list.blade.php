@extends('accountant.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Account Heads') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
                        <li><a href="#">{{ get_phrase('Account Heads') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('accountant.account_heads.open_modal') }}', '{{ get_phrase('Create Account Head') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add New Account Head') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            @if(count($account_heads) > 0)
            <div class="table-responsive tScrollFix pb-2">
                <table id="basic-datatable" class="table eTable">
                    <thead>
                      <tr>
                        <th>{{ get_phrase('Name') }}</th>
                        <th>{{ get_phrase('Type') }}</th>
                        <th>{{ get_phrase('Opening balance') }}</th>
                        <th class="text-end">{{ get_phrase('Option') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($account_heads as $account_head)
                        <tr>
                            <td>{{ $account_head->name }} @if($account_head->is_system) <span class="badge bg-secondary">{{ get_phrase('Default') }}</span> @endif</td>
                            <td>{{ ucfirst($account_head->type) }}</td>
                            <td>{{ school_currency($account_head->opening_balance) }} ({{ ucfirst($account_head->opening_balance_type) }})</td>
                            <td class="text-start">
                                <div class="adminTable-action">
                                    <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                      {{ get_phrase('Actions') }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                      <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('accountant.edit.account_heads', ['id' => $account_head->id]) }}', '{{ get_phrase('Edit Account Head') }}')">{{ get_phrase('Edit') }}</a>
                                      </li>
                                      @if(!$account_head->is_system)
                                      <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('accountant.account_heads.delete', ['id' => $account_head->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                      </li>
                                      @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            {{ $account_heads->links() }}
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
