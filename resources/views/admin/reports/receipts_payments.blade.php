@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Receipts & Payments Statement') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
                        <li><a href="#">{{ get_phrase('Receipts & Payments Statement') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block" action="{{ route('admin.reports.receipts_payments') }}">
                <div class="row justify-content-md-center align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('From') }}</label>
                        <input type="date" class="form-control eForm-control" name="from" value="{{ $from }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="eForm-label">{{ get_phrase('To') }}</label>
                        <input type="date" class="form-control eForm-control" name="to" value="{{ $to }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="eBtn eBtn-blue form-control">{{ get_phrase('Filter') }}</button>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="position-relative">
                          <button class="eBtn-3 dropdown-toggle form-control" type="button" id="rpExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ get_phrase('Export') }}
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                            <li><a class="dropdown-item" href="javascript:;" onclick="generateReportPDF()">{{ get_phrase('PDF') }}</a></li>
                            <li><a class="dropdown-item" href="javascript:;" onclick="printReport()">{{ get_phrase('Print') }}</a></li>
                          </ul>
                        </div>
                    </div>
                </div>
            </form>

            <div class="report-letterhead" id="rp_report">
                <div class="report-letterhead-top">
                    <div>
                        <h3>{{ $school->title ?? get_settings('system_title') }}</h3>
                        <p>{{ $school->address ?? '' }}</p>
                    </div>
                    <div class="report-letterhead-badge">
                        <span>{{ get_phrase('Receipts & Payments Statement') }}</span>
                        <small>{{ \Carbon\Carbon::parse($from)->format('d F Y') }} &mdash; {{ \Carbon\Carbon::parse($to)->format('d F Y') }}</small>
                    </div>
                </div>

                <div class="report-stats">
                    <div class="report-stat">
                        <span class="report-stat-label">{{ get_phrase('Transactions') }}</span>
                        <span class="report-stat-value">{{ $totals['transactions'] }}</span>
                    </div>
                    <div class="report-stat">
                        <span class="report-stat-label">{{ get_phrase('Total Debit') }}</span>
                        <span class="report-stat-value">{{ number_format($totals['debit'], 2) }}</span>
                    </div>
                    <div class="report-stat">
                        <span class="report-stat-label">{{ get_phrase('Total Credit') }}</span>
                        <span class="report-stat-value">{{ number_format($totals['credit'], 2) }}</span>
                    </div>
                    <div class="report-stat">
                        <span class="report-stat-label">{{ get_phrase('Status') }}</span>
                        @if(abs($totals['debit'] - $totals['credit']) < 0.01)
                            <span class="report-badge report-badge-ok">{{ get_phrase('Balanced') }}</span>
                        @else
                            <span class="report-badge report-badge-warn">{{ get_phrase('Out of Balance') }}</span>
                        @endif
                    </div>
                </div>

                <h5 class="report-section-title">{{ get_phrase('Transaction Detail') }}</h5>
                <div class="table-responsive tScrollFix pb-2">
                    <table class="table eTable report-table">
                        <thead>
                            <tr>
                                <th>{{ get_phrase('Date') }}</th>
                                <th>{{ get_phrase('Particulars') }}</th>
                                <th>{{ get_phrase('Account Head') }}</th>
                                <th>{{ get_phrase('Type') }}</th>
                                <th>{{ get_phrase('Voucher No') }}</th>
                                <th>{{ get_phrase('Recorded By') }}</th>
                                <th class="text-end">{{ get_phrase('Debit') }}</th>
                                <th class="text-end">{{ get_phrase('Credit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lines as $line)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($line->voucher->voucher_date)->format('d-M-Y') }}</td>
                                <td>{{ $line->voucher->particulars }}</td>
                                <td>{{ $line->accountHead->name ?? '' }}</td>
                                <td><span class="report-type-tag report-type-{{ $line->accountHead->type ?? '' }}">{{ ucfirst($line->accountHead->type ?? '') }}</span></td>
                                <td>{{ $line->voucher->voucher_no }}</td>
                                <td>{{ optional($line->voucher->recordedBy)->name ?? '-' }}</td>
                                <td class="text-end">{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                                <td class="text-end">{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ get_phrase('No transactions found for this period') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">{{ get_phrase('Total') }}</th>
                                <th class="text-end">{{ number_format($totals['debit'], 2) }}</th>
                                <th class="text-end">{{ number_format($totals['credit'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <h5 class="report-section-title">{{ get_phrase('Summary by Account Head') }}</h5>
                <div class="table-responsive tScrollFix pb-2">
                    <table class="table eTable report-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">{{ get_phrase('Account Head') }}</th>
                                <th rowspan="2" class="align-middle">{{ get_phrase('Type') }}</th>
                                <th colspan="2" class="text-center">{{ get_phrase('Opening Balance') }}</th>
                                <th colspan="2" class="text-center">{{ get_phrase('Period Movement') }}</th>
                                <th colspan="2" class="text-center">{{ get_phrase('Closing Balance') }}</th>
                            </tr>
                            <tr>
                                <th class="text-end">{{ get_phrase('Debit') }}</th>
                                <th class="text-end">{{ get_phrase('Credit') }}</th>
                                <th class="text-end">{{ get_phrase('Debit') }}</th>
                                <th class="text-end">{{ get_phrase('Credit') }}</th>
                                <th class="text-end">{{ get_phrase('Debit') }}</th>
                                <th class="text-end">{{ get_phrase('Credit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summary as $row)
                            <tr>
                                <td>{{ $row['head']->name }}</td>
                                <td><span class="report-type-tag report-type-{{ $row['head']->type }}">{{ ucfirst($row['head']->type) }}</span></td>
                                <td class="text-end">{{ $row['opening_debit'] > 0 ? number_format($row['opening_debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['opening_credit'] > 0 ? number_format($row['opening_credit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['closing_debit'] > 0 ? number_format($row['closing_debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['closing_credit'] > 0 ? number_format($row['closing_credit'], 2) : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">{{ get_phrase('Total') }}</th>
                                <th class="text-end">{{ number_format($totals['opening_debit'], 2) }}</th>
                                <th class="text-end">{{ number_format($totals['opening_credit'], 2) }}</th>
                                <th class="text-end">{{ number_format($totals['debit'], 2) }}</th>
                                <th class="text-end">{{ number_format($totals['credit'], 2) }}</th>
                                <th class="text-end">{{ number_format($totals['closing_debit'], 2) }}</th>
                                <th class="text-end">{{ number_format($totals['closing_credit'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    function generateReportPDF() {
        const element = document.getElementById("rp_report");
        var clonedElement = element.cloneNode(true);
        $(clonedElement).css("display", "block");

        var opt = {
            margin: 0.4,
            filename: 'receipts_payments_statement.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(clonedElement).save();
        clonedElement.remove();
    }

    function printReport() {
        var printContents = document.getElementById('rp_report').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection
