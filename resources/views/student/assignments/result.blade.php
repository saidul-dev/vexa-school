@php

  $questions = App\Models\Addon\AssignmentQuestion::where('assignment_id', $assignment_id)->get();

    $student = DB::table('users')->where('id', auth()->user()->id)->first();

    $user_image = get_user_image(auth()->user()->id);
    $info = json_decode($student->user_information);

    $class_id = App\Models\Enrollment::where('user_id', auth()->user()->id)->first()->class_id;
    $class_name = App\Models\Classes::find($class_id)->name;

    $section_id = App\Models\Enrollment::where('user_id', auth()->user()->id)->first()->section_id;
    $section_name = App\Models\Section::find($section_id)->name;

    if(App\Models\Addon\AssignmentRemark::where('assignment_id', $assignment_id)->where('student_id', auth()->user()->id)->exists()){

      $remark = App\Models\Addon\AssignmentRemark::where('assignment_id', $assignment_id)->where('student_id', auth()->user()->id)->first()->remark;
    }
    else {

      $remark = "";
    }

    //check if teacher review/ evaluation pending
    if(App\Models\Addon\AssignmentAnswer::where('assignment_id', $assignment_id)->where('student_id', auth()->user()->id)->whereNull('obtained_mark')->exists()){
      $result = 'pending review';
    } 

    //show result after complete evaluation
    else {
      $result = App\Models\Addon\AssignmentAnswer::where('assignment_id', $assignment_id)->where('student_id', auth()->user()->id)->sum('obtained_mark');
    }



    // $student_details = (new CommonController)->get_student_academic_info($student->id);

@endphp



<div id="container">

  <div class="display-none-view ">
    <h5 class="eCard-title ">{{ App\Models\Addon\Assignment::find($assignment_id)->title }}</h5>  
  </div>


  <div class="row mb-1" >
    <div class=" d-flex justify-content-between px-0 mx-0">
      <div class="col-md-6 d-flex align-items-stretch">
        <div class="eCard ps-3">
          <div class="row g-0 align-items-center">
            <div class="col-md-4">
              <img src="{{  $user_image }}" class="eCard-img-top img-fluid rounded-0" alt="student-image">
            </div>
            <div class="col-md-8">
              <div class="eCard-body">
                <h5 class="eCard-title ">{{ auth()->user()->name }}</h5>
                <p class="eCard-text ">{{ get_phrase('Class') }}: {{ $class_name }}<br>
                {{ get_phrase('Section') }}: {{ $section_name }}<br>
                  {{ get_phrase('Email') }}: {{ auth()->user()->email }}<br>
                {{ get_phrase('Phone') }}: {{ $info->phone }}<br>
                
                </p>
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 d-flex align-items-stretch">
        <div class="eCard flex-grow-1">
          <div class="row">
            <div class="col-md-12">
              <div class="eCard-body">
                <p class="eCard-text d-flex justify-content-between">
                  <span>{{ get_phrase('Total questions answered') }}:</span><span class="eBadge ebg-soft-info">{{ App\Models\Addon\AssignmentAnswer::where('assignment_id', $assignment_id)->where('student_id', auth()->user()->id)->count() }} out of {{ App\Models\Addon\AssignmentQuestion::where('assignment_id', $assignment_id)->count() }}</span>
                </p>
                <p class="eCard-text d-flex justify-content-between">
                  <span>{{ get_phrase('Total marks obtained') }}:</span>

                  @if($result == 'pending review')
                  <span class="eBadge ebg-soft-warning">{{ $result }}</span>
                  @else 
                  <span class="eBadge ebg-soft-info">{{ $result }} out of {{ App\Models\Addon\AssignmentQuestion::where('assignment_id', $assignment_id)->sum('mark') }}</span>
                  @endif

                </p>

                @if(!$remark == '')
                <label class="eCard-text" for="remark">Remark:</label>
                <div class="form-control eCard-text" id="remark" name="remark" rows="3" >{{ $remark }}</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    <span class="border-bottom mb-4 pb-2 ps-4">{{ get_phrase('Answers') }}</span>
  </div>     
    
  @foreach($questions as $question)

  @php 
  
  if(App\Models\Addon\AssignmentQuestion::find($question['id'])->answers()->where('student_id', auth()->user()->id)->exists()){

    $answer_status = 'yes'; 
  } else {
    $answer_status = 'no';
  }

  if($answer_status == 'yes' && $result != 'pending review'){

    $obtained_mark = App\Models\Addon\AssignmentAnswer::where('question_id',$question->id)->where('student_id', auth()->user()->id)->first()->obtained_mark;
  } else {
    $obtained_mark = 0;
  }
  @endphp

  <div class="row px-1 r">
        <div class="eCard px-0">
            <div class="eCard-body col-md-12 mb-1 ">
                <div class="d-flex justify-content-between align-items-top">
                    <p class="eCard-text pe-2">{{ get_phrase('Question') }}: {{ $question['question'] }}</p>
                    <div class="eCard-text">
                      <span class="eBadge ebg-soft-dark">{{ get_phrase('Marks obtained') }}: {{ $obtained_mark }} {{ get_phrase('out of') }} {{ $question['mark'] }}</span>
                    </div>
                </div>
                <p class="eCard-text mb-1">
                  {{-- {{ get_phrase('Answer') }}: --}}
                @if($answer_status == 'no')

                  <span  class="eBadge ebg-soft-danger">{{ strtoupper(get_phrase('Not-Answered')) }}</span>  
                  @else
                  <span  class="eBadge ebg-soft-success">{{ strtoupper(get_phrase('Answered')) }}</span>
                </p>

                <div class="form-group eForm">
                  <div class="eForm-control eCard-text p-3">
                  @if($question['question_type'] == 'text')
                    {{ App\Models\Addon\AssignmentQuestion::find($question['id'])->answers()->where('student_id', auth()->user()->id)->first()->answer }}
                    
                  @else
                    <a class="btn btn-outline-primary btn-sm me-2 pt-0 selector-class-button" href="{{ route('previous.answer.download.file', ['question_id' => $question['id']]) }}" ><span class="pt-1 d-inline-block"><i class="bi bi-download"></i> {{ get_phrase('Download answer') }}</span></a>
                  @endif                                    
                  </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @endforeach
  
</div>


  




