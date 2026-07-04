@extends('admin.navigation')
   
@section('content')

<?php 

use App\Http\Controllers\CommonController;
use App\Models\School;
use App\Models\User;

?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Feedback') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Back Office') }}</a></li>
                        <li><a href="#">{{ get_phrase('Feedback') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.feedback.create_feedback') }}', '{{ get_phrase('Create Feedback') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add New Feedback') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
  @if(count($feedbacks) > 0)
    @foreach ($feedbacks as $feedback)
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
          @if(auth()->user()->id == $feedback->admin_id)
          <div class="adminTable-action">
            <button
              type="button"
              class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
              data-bs-toggle="dropdown"
              aria-expanded="false"
              style="color: #797c8b"
            >
              {{ get_phrase('Actions') }}
            </button>
            <ul
              class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
            >
              <li>
                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.feedback.edit_feedback', ['id' => $feedback->id]) }}', '{{ get_phrase('Edit feedback') }}')">{{ get_phrase('Edit') }}</a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.feedback.delete_feedback', ['id' => $feedback->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
              </li>
            </ul>
          </div>
          @endif
        </div>
      </div>
    </div>
    </div>
    
    @endforeach
    <div class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
      <p class="admin-tInfo">{{ get_phrase('Showing').' 1 - '.count($feedbacks).' '.get_phrase('from').' '.$feedbacks->total().' '.get_phrase('data') }}</p>
      <div class="admin-pagi">
        {!! $feedbacks->appends(request()->all())->links() !!}
      </div>
  </div>
  @else
    <div class="empty_box center">
      <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
      <br>
      <span class="">{{ get_phrase('No data found') }}</span>
    </div> 
  @endif
</div>


@endsection