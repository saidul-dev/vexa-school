<?php 

use App\Http\Controllers\CommonController;
use App\Models\Book;

?>


@if(count($book_issues) > 0)
<div class="issued_book" id="issued_book_report">
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ get_phrase('Book name') }}</th>
                <th>{{ get_phrase('Issue date') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($book_issues as $book_issue)
                <?php 
                $book_details = Book::find($book_issue['book_id']);
                $student_details = (new CommonController)->get_student_details_by_id($book_issue['student_id']);
                ?>
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $book_details['name'] }}</td>
                    <td>
                        {{ date('D, d/M/Y', $book_issue['issue_date']) }}
                    </td>
                    <td>
                        {{ $student_details['name'] }}<br> <small>{{ get_phrase('Student code') }}: {{ $student_details['code'] }}</small>
                    </td>
                    <td>
                        {{ $student_details['class_name'] }}
                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-soft-success">{{ get_phrase('Returned') }}</span>
                        <?php else: ?>
                            <span class="eBadge ebg-soft-warning">{{ get_phrase('Pending') }}</span>
                        <?php endif; ?>
                    </td>
                </tr>
            @endforeach
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