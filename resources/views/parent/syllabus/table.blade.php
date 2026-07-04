<?php
    use App\Models\Subject;
    use App\Models\Classes;
?>

@if(count($class_name) > 0)
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>

            <th>{{ get_phrase('Title') }}</th>
            <th>{{ get_phrase('Syllabus') }}</th>
            <th>{{ get_phrase('Subject') }}</th>
            <th>{{ get_phrase('Class') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($syllabus as $subject_details)
        <tr>
            <td>  {{ $subject_details['title'] }}  </td>
            <td><a href="{{ asset('assets/uploads/syllabus/'.$subject_details['file']) }}" class="btn btn-primary btn-sm bi bi-download" download>{{ get_phrase('Download') }}</a></td>
                 <?php $suject_name=Subject::where('id',$subject_details['subject_id'])->first()->toArray(); ?>
            <td>{{  $suject_name['name'];  }} </td>
            <?php $class_name=Classes::where('id',$subject_details['class_id'])->first()->toArray(); ?>
            <td>{{  $class_name['name'];   }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty_box center">
    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
    <br>
    {{ get_phrase('No data found') }}
</div>
@endif


