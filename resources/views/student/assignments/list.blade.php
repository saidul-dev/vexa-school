<style>
    
    .cuttoff-text {
        --max-lines: 2; 
        overflow: hidden;

        display: -webkit-box; 
        -webkit-box-orient: vertical;
        -webkit-line-clamp: var(--max-lines);
    }

    .min-lines {
        height: 3.5rem;
    }

    .custom-border-bottom{

        border-bottom: 1px solid black;
    }

    .set-chars-limit {
        max-width: 26ch;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

</style>

<div class="row mt-3">
@foreach ($my_assignments as $my_assignment)

    @php

    $teacher_image = get_user_image($my_assignment['teacher_id']);


    //for submission status;;
    //status 0 on assignment answer indicates submission by student; expired are treated as auto submitted
    if($type == 'expired' || App\Models\Addon\AssignmentAnswer::where('assignment_id', $my_assignment['id'])->where('student_id', auth()->user()->id)->where('status', 0)->exists()){

        $submission_status = 'yes';
    } else {
        $submission_status = 'no';
    }

    if($submission_status == 'yes'){

        if(App\Models\Addon\AssignmentAnswer::where('assignment_id', $my_assignment['id'])->where('student_id', auth()->user()->id)->whereNull('obtained_mark')->exists()){

            $obtained_mark = get_phrase('pending review');
        } else {
            $obtained_mark = App\Models\Addon\AssignmentAnswer::where('assignment_id', $my_assignment['id'])->where('student_id', auth()->user()->id)->sum('obtained_mark');
        }

    }
    


    //for notification in button
    $temp_total_questions = App\Models\Addon\AssignmentQuestion::where('assignment_id', $my_assignment['id'])->count();

    $temp_answered_questions = App\Models\Addon\AssignmentAnswer::where('assignment_id', $my_assignment['id'])->where('student_id', auth()->user()->id)->count();

    $temp_pending_questions = $temp_total_questions - $temp_answered_questions;

    @endphp

    <div class="col-md-3">
        <div class="eCard ">
                <div class="eCard-body">

                    <div class="min-lines custom-border-bottom">
                        
                        <h5 class="cuttoff-text eCard-title ">{{ $my_assignment['title'] }}</h5>
                    </div>
                    
                    <div class="custom-border-bottom mt-2">
                        
                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('Subject') }}:</span> <span class="eBadge ebg-soft-info set-chars-limit">{{ App\Models\Subject::find($my_assignment['subject_id'])->name }}</span>
                        </p>

                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('Total questions') }}:</span> <span class="eBadge ebg-soft-info">{{ App\Models\Addon\AssignmentQuestion::where('assignment_id', $my_assignment['id'])->count() }}</span>
                        </p>

                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('Total marks') }}:</span> <span class="eBadge ebg-soft-info">{{ App\Models\Addon\AssignmentQuestion::where('assignment_id', $my_assignment['id'])->sum('mark') }}</span>
                        </p>

                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('Total Answered') }}:</span> <span class="eBadge ebg-soft-info">{{ App\Models\Addon\AssignmentAnswer::where('assignment_id', $my_assignment['id'])->where('student_id', auth()->user()->id)->count() }}</span>
                        </p>    
                        
                        @if($type == 'expired')
                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('Total obtained marks') }}:</span> <span class="eBadge ebg-soft-info">{{ $obtained_mark }}</span>
                        </p>    
                        @endif

                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('Start date') }}:</span><span class="eBadge ebg-soft-info">{{ date('F j, Y g:i a', (int)App\Models\Addon\Assignment::find($my_assignment['id'])->start_timestamp) }}</span>
                        </p>

                        <p class="eCard-text text-center d-flex justify-content-between align-items-center">
                            <span>{{ get_phrase('End date') }}:</span><span class="eBadge ebg-soft-info">{{ date('F j, Y g:i a', (int)App\Models\Addon\Assignment::find($my_assignment['id'])->end_timestamp) }}</span>
                        </p>
                    </div>

                    
                    
                    <div class="eCard-AdminBtn d-flex flex-wrap justify-content-between align-items-center my-2">
                        <div class="eCard-Admin d-flex align-items-center">
                            @php
                            $user_information_data = json_decode(App\Models\User::find($my_assignment['teacher_id'])->user_information, true);
                            @endphp
                            <img src="{{ $teacher_image }}" alt="" class="eCard-userImg">
                            <span class="set-chars-limit" >{{ App\Models\User::find($my_assignment['teacher_id'])->name }}</span>
                        </div>
                        
                    </div>



                    {{-- open assignment for unsubmitted active and view result for submitted active or expired --}}
                   
                    <div class="text-center">
                        
                        @if($submission_status == 'no')

                        <a href="{{ route('student.assignment.questions.view',['assignment_id' => $my_assignment['id'] ]) }}" type="button" class="eBadge-btn ebg-outline-info position-relative my-2">
                            {{ get_phrase('Open Assignment') }}

                            @if($type=='active' && $temp_pending_questions>0)
                            <span class="position-absolute top-0 start-100 translate-middle eBadge ebg-danger ms-0">{{ $temp_pending_questions }}</span>
                            @endif
                        </a>

                        @else

                        <a onclick="showLargeScrollableAjaxModal('{{ route('student.assignment.result.view', ['assignment_id'=> 
                        $my_assignment['id']]) }}', '{{ $my_assignment['title'] }}')" type="button" class="eBadge-btn ebg-outline-info my-2">{{ get_phrase('View Result') }}</a>

                        @endif

                    </div>

                    {{-- <a href="#" class="eBtn eBtn-blue" style="margin: auto;">{{ get_phrase('Open') }}</a> --}}
                </div>
        </div>
    </div>


@endforeach
</div>




<script>
    
    //large-scrollable modal code
    function showLargeScrollableAjaxModal(url, header)
    {
        // SHOWING AJAX PRELOADER IMAGE
        jQuery('#large-scrollable-modal .modal-body').html('<div style="text-align:center;margin-top:200px;"><img style="width: 100px; opacity: 0.4; " src="{{ asset('public/assets/images/straight-loader.gif') }}" /></div>');
        jQuery('#large-scrollable-modal .modal-title').html('...');
        // LOADING THE AJAX MODAL
        jQuery('#large-scrollable-modal').modal('show', {backdrop: 'true'});

        header = 'Assignment: ' + header;

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        $.ajax({
            url: url,
            success: function(response)
            {
                jQuery('#large-scrollable-modal .modal-body').html(response);
                jQuery('#large-scrollable-modal .modal-title').html(header);
            }
        });
    }

    //modal code ended


</script>                       