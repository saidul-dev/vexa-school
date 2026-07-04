@if(count($exams) > 0)
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ get_phrase('Exam') }}</th>
            <th>{{ get_phrase('Starting Time') }}</th>
            <th>{{ get_phrase('Ending Time') }}</th>
            <th>{{ get_phrase('Total Marks') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($exams as $key => $exam)
            <tr>
                <td>{{ $exams->firstItem() + $key }}</td>
                <td>{{ $exam->name }}</td>
                <td>{{ date('d M Y - h:i A', $exam->starting_time) }}</td>
                <td>{{ date('d M Y - h:i A', $exam->ending_time) }}</td>
                <td>{{ $exam->total_marks }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $exams->appends(request()->all())->links() !!}
@else
<div class="empty_box center">
    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
    <br>
    {{ get_phrase('Data not found') }}
</div>
@endif