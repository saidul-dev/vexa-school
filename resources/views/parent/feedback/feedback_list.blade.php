

<?php 

use App\Http\Controllers\CommonController;
use App\Models\School;
use App\Models\User;

?>

<div class="row">
    @foreach ($feedbacks as $feedback)
    @if(auth()->user()->id == $feedback->parent_id)
    @php
        $student_details = (new CommonController)->get_student_academic_info($feedback->student_id);
        $parent_details = User::find($student_details->parent_id);

        $admin = User::find($feedback->admin_id);

        if(!empty($admin)){
        $info = json_decode($admin->user_information);
            $user_image = $info->photo;
            if(!empty($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            }
        }
    @endphp
    <div class="col-md-4">
    <div class="eCard eCard-2">
      <div class="eCard-body">
        <div class="d-flex justify-content-between">
          <h5 class="eCard-subtitle"><span>{{get_phrase('Student')}}: </span>{{$student_details->name}}</h5>
          <h5 class="eCard-subtitle"><span>{{get_phrase('Parent')}}: </span>{{$parent_details->name}}</h5>
        </div>
        
        <div class="card_subtitle d-flex justify-content-between">
            @if(empty($student_details->class_name))
            <h5 class="eCard-subtitle"><span>{{get_phrase('Class')}}: </span>{{get_phrase('removed')}}</h5>
            <h5 class="eCard-subtitle"><span>{{get_phrase('Section')}}: </span>{{get_phrase('removed')}}</h5>
        @else
            <h5 class="eCard-subtitle"><span>{{get_phrase('Class')}}: </span>{{ $student_details->class_name }}</h5>
            <h5 class="eCard-subtitle"><span>{{get_phrase('Section')}}: </span>{{ $student_details->section_name }}</h5>
            @endif
        </div>
      </div>
      <div class="eCard-body">
        <h5 class="eCard-title">{{$feedback->title}}</h5>
        <p class="eCard-text">
            {{$feedback->feedback_text}}
        </p>
        <div class="eCard-AdminBtn d-flex flex-wrap justify-content-between align-items-center">
          <div class="eCard-Admin d-flex align-items-center">
            @if(!empty($admin->name))
            <img src="{{ asset('assets') }}/{{ $user_image }}" alt="" class="eCard-userImg">
            
            <span>{{$admin->name}}</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
@endforeach
</div>


