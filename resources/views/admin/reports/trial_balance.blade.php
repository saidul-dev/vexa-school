@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <h4>{{ get_phrase('Trial Balance') }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block" action="{{ route('admin.reports.trial_balance') }}">
                <div class="row justify-content-md-center">
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('As of') }}</label>
                        <input type="date" class="form-control eForm-control" name="as_of" value="{{ $as_of }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Account Head') }}</th>
                            <th>{{ get_phrase('Type') }}</th>
                            <th>{{ get_phrase('Debit') }}</th>
                            <th>{{ get_phrase('Credit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row['head']->name }}</td>
                            <td>{{ ucfirst($row['head']->type) }}</td>
                            <td>{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                            <td>{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">{{ get_phrase('Total') }}</th>
                            <th>{{ number_format($totals['debit'], 2) }}</th>
                            <th>{{ number_format($totals['credit'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if(abs($totals['debit'] - $totals['credit']) > 0.01)
                <div class="alert alert-danger">{{ get_phrase('Warning: the ledger does not balance.') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
