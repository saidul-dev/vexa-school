<?php

use App\Models\Subject;
use App\Models\Session;
use App\Models\Gradebook;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

$index = 0;

?>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="view_mark" id="mark_report">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Subject name') }}</td>
                            @foreach($exam_categories as $exam_category)
                               <th>{{ $exam_category->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $subject)
                        <tr>
                            <td>{{ $index = $index+1 }}</td>
                            <td>{{ $subject->name }}</td>
                            @foreach($exam_categories as $exam_category)
                               <td>
                                <?php
                                $exam_marks = Gradebook::where('exam_category_id', $exam_category['id'])
                                    ->where('class_id', $student_details['class_id'])
                                    ->where('section_id', $student_details['section_id'])
                                    ->where('student_id', $student_details['user_id'])
                                    ->where('school_id', auth()->user()->school_id)
                                    ->where('session_id', $active_session)
                                    ->first();

                                    if(isset($exam_marks->marks) && $exam_marks->marks){
                                        $subject_list = json_decode($exam_marks->marks, true);
                                    }else{
                                        $subject_list = array();
                                    }
                                ?>
                                @if(is_array($subject_list))
                                    @if(array_key_exists($subject->id, $subject_list))
                                        {{ $subject_list[$subject->id] }}
                                    @else
                                        {{ "-" }}
                                    @endif
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
