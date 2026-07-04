<?php 

use App\Http\Controllers\CommonController;
use App\Models\Book;

?>


@if(count($book_issues) > 0)
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ get_phrase('Book name') }}</th>
                <th>{{ get_phrase('Issue date') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th class="text-center">{{ get_phrase('Option') }}</th>
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
                    <td><strong>{{ $book_details['name'] }}</strong></td>
                    <td>
                        {{ date('D, d/M/Y', $book_issue['issue_date']) }}
                    </td>
                    <td>
                        <strong>{{ $student_details['name'] }}</strong>
                        <br> 
                        <strong>{{ get_phrase('Id') }}: </strong>
                        <small>{{ $student_details['code'] }}</small>
                    </td>
                    <td>
                        {{ $student_details['class_name'] }}
                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-success">{{ get_phrase('Returned') }}</span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger">{{ get_phrase('Pending') }}</span>
                        <?php endif; ?>
                    </td>
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
                            <?php if (!$book_issue['status']): ?>
                                  <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('librarian.edit.book_issue', ['id' => $book_issue->id]) }}', '{{ get_phrase('Update book issue information') }}')">{{ get_phrase('Edit') }}</a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('librarian.book_issue.return', ['id' => $book_issue->id]) }}', 'undefined');">{{ get_phrase('Return this book') }}</a>
                                  </li>
                            <?php endif; ?>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('librarian.book_issue.delete', ['id' => $book_issue->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                              </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        <span class="">{{ get_phrase('No data found') }}</span>
    </div>
@endif


@if(count($book_issues) > 0)
<div class="table-responsive display-none-view" id="book_issue_report">
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
                    <td><strong>{{ $book_details['name'] }}</strong></td>
                    <td>
                        {{ date('D, d/M/Y', $book_issue['issue_date']) }}
                    </td>
                    <td>
                        <strong>{{ $student_details['name'] }}</strong>
                        <br> 
                        <strong>{{ get_phrase('Id') }}: </strong>
                        <small>{{ $student_details['code'] }}</small>
                    </td>
                    <td>
                        {{ $student_details['class_name'] }}
                    </td>
                    <td>
                        <?php if ($book_issue['status']): ?>
                            <span class="eBadge ebg-success">{{ get_phrase('Returned') }}</span>
                        <?php else: ?>
                            <span class="eBadge ebg-danger">{{ get_phrase('Pending') }}</span>
                        <?php endif; ?>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif