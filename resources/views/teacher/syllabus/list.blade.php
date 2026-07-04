<?php
use App\Models\Subject;
?>

@if(count($syllabuses) > 0)
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>
            <th>{{ get_phrase('Title') }}</th>
            <th>{{ get_phrase('Syllabus') }}</th>
            <th>{{ get_phrase('Subject') }}</th>
            <th class="text-end">{{ get_phrase('Option') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($syllabuses as $syllabus):?>
            <tr>
                <td>{{ $syllabus['title'] }}</td>
                <td><a href="{{ asset('assets/uploads/syllabus') }}/{{ $syllabus['file'] }}" class="btn btn-primary btn-sm bi bi-download" download>{{ get_phrase(' Download') }}</a></td>
                <td>
                    <?php $subject= Subject::where('id' ,$syllabus['subject_id'])->first()->toArray(); ?>
                    {{ $subject['name'] }}
                </td>
                <td class="text-center">
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
                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('teacher.syllabus.delete', ['id' => $syllabus['id']]) }}', 'undefined')">{{ get_phrase('Delete') }}</a>
                          </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
@else
<div class="syllabus_content">
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
        <br>
        {{ get_phrase('No data found') }}
    </div>
</div>
@endif

