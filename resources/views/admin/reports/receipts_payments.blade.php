@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <h4>{{ get_phrase('Receipts & Payments Statement') }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block" action="{{ route('admin.reports.receipts_payments') }}">
                <div class="row justify-content-md-center">
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('From') }}</label>
                        <input type="date" class="form-control eForm-control" name="from" value="{{ $from }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('To') }}</label>
                        <input type="date" class="form-control eForm-control" name="to" value="{{ $to }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                </div>
            </form>

            <h5>{{ get_phrase('Transactions') }}</h5>
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Date') }}</th>
                            <th>{{ get_phrase('Particulars') }}</th>
                            <th>{{ get_phrase('Account Head') }}</th>
                            <th>{{ get_phrase('Voucher No') }}</th>
                            <th>{{ get_phrase('Debit') }}</th>
                            <th>{{ get_phrase('Credit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lines as $line)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($line->voucher->voucher_date)->format('d-M-Y') }}</td>
                            <td>{{ $line->voucher->particulars }}</td>
                            <td>{{ $line->accountHead->name }}</td>
                            <td>{{ $line->voucher->voucher_no }}</td>
                            <td>{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                            <td>{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5>{{ get_phrase('Summary by Account Head') }}</h5>
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Account Head') }}</th>
                            <th>{{ get_phrase('Type') }}</th>
                            <th>{{ get_phrase('Opening balance') }}</th>
                            <th>{{ get_phrase('Debit') }}</th>
                            <th>{{ get_phrase('Credit') }}</th>
                            <th>{{ get_phrase('Closing balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $row)
                        <tr>
                            <td>{{ $row['head']->name }}</td>
                            <td>{{ ucfirst($row['head']->type) }}</td>
                            <td>{{ number_format($row['opening'], 2) }}</td>
                            <td>{{ number_format($row['debit'], 2) }}</td>
                            <td>{{ number_format($row['credit'], 2) }}</td>
                            <td>{{ number_format($row['closing'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
