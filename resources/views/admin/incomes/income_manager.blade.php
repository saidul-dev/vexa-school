@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Income') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
                        <li><a href="#">{{ get_phrase('Income') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.income.open_modal') }}', '{{ get_phrase('Create Income') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add New Income') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.income.list') }}">
                <div class="row justify-content-md-center">
                    <div class="col-xl-3 mb-3">
                        <input type="text" class="form-control eForm-control" name="eDateRange"
                            value="{{ date('m/d/Y', $date_from).' - '.date('m/d/Y', $date_to) }}" />
                    </div>
                    <div class="col-xl-2 mb-3">
                        <button type="submit" class="eBtn eBtn btn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>
            <div class="income_content">
                @include('admin.incomes.list')
            </div>
        </div>
    </div>
</div>
@endsection
