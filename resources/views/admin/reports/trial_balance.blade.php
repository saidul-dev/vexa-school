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
                <div class="row justify-content-md-center align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('As of') }}</label>
                        <input type="date" class="form-control eForm-control" name="as_of" value="{{ $as_of }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="position-relative">
                          <button class="eBtn-3 dropdown-toggle form-control" type="button" id="tbExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ get_phrase('Export') }}
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.trial_balance.export', ['as_of' => $as_of]) }}">{{ get_phrase('Excel') }}</a></li>
                            <li><a class="dropdown-item" href="javascript:;" onclick="generateReportPDF()">{{ get_phrase('PDF') }}</a></li>
                            <li><a class="dropdown-item" href="javascript:;" onclick="printReport()">{{ get_phrase('Print') }}</a></li>
                          </ul>
                        </div>
                    </div>
                </div>
            </form>

            <div id="tb_report">
                <div class="report-letterhead-top">
                    <div>
                        <h3>{{ $school->title ?? get_settings('system_title') }}</h3>
                        <p>{{ $school->address ?? '' }}</p>
                    </div>
                    <div class="report-letterhead-badge">
                        <span>{{ get_phrase('Trial Balance') }}</span>
                        <small>{{ get_phrase('As of') }} {{ \Carbon\Carbon::parse($as_of)->format('d F Y') }}</small>
                    </div>
                </div>

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
</div>

<script type="text/javascript">
    "use strict";

    function generateReportPDF() {
        const element = document.getElementById("tb_report");
        var clonedElement = element.cloneNode(true);
        $(clonedElement).css("display", "block");

        var opt = {
            margin: 0.4,
            filename: 'trial_balance.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(clonedElement).save();
        clonedElement.remove();
    }

    function printReport() {
        var printContents = document.getElementById('tb_report').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection
