@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Disable-List') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Users') }}</a></li>
                            <li><a href="#">{{ get_phrase('Disable-List') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="javascript:;" class="export_btn"
                            onclick="rightModal('{{ route('admin.disable_reason.open_modal') }}', '{{ get_phrase('Create Disable Reason') }}')">{{ get_phrase('Create Disable Reason') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Teacher area -->
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="eSection-wrap-2">
                <div class="search-filter-area d-flex justify-content-center align-items-center flex-wrap gr-15">
                    @if (count($reasons) > 0)
                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table eTable eTable-2">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('Reason') }}</th>
                                        <th scope="col">{{ get_phrase('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reasons as $key => $reason)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ ++$key }}</p>
                                            </th>
                                            <td>
                                                <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                    <div class="dAdmin_profile_name">
                                                        <h4>{{ $reason->disable_reason }}</h4>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="adminTable-action">
                                                    <button type="button"
                                                        class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        {{ get_phrase('Actions') }}
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:;"
                                                                onclick="rightModal('{{ route('admin.disable_reason.open_edit_modal', ['id' => $reason->id]) }}', '{{ get_phrase('Edit Reason') }}')">{{ get_phrase('Edit') }}</a>
                                                        </li>
                                                        @if ($key != 0)
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    onclick="confirmModal('{{ route('admin.disable_reason.delete', ['id' => $reason->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
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
        <div class="col-3"></div>
    </div>
@endsection
