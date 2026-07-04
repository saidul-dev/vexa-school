@extends('parent.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Subjects') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Academic') }}</a></li>
                        <li><a href="#">{{ get_phrase('Subjects') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-8 offset-md-2">
        <div class="eSection-wrap pb-2">

            <div class="row mt-3">
                <div class="col-md-4"></div>
                <div class="col-md-3 mb-1">
                  <select name="user_id" id="user_id" class="form-select eForm-select eChoice-multiple-with-remove" required>

                    <option value="">{{ get_phrase('Select a student') }}</option>
                    @foreach($student_data as $key => $details)
                    <option value="{{ $details['id'] }}">{{ $details['name'] }}</option>
                    @endforeach
                  </select>
                </div>


                <div class="col-md-2">
                    <button class="eBtn eBtn btn-secondary" onclick="filter()" >{{ get_phrase('Filter') }}</button>
                </div>
            </div>
            <div class="card-body subject_content">
                <div class="empty_box center" id="hide_me">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    "use strict";

    function filter() {
        var user_id = $('#user_id').val();

        if(user_id != "" ){
          $.ajax({
            url: "{{ route('parent.subjectList_by_student_name') }}",
            data: {user_id : user_id},
            success: function(response){
              $('.subject_content').html(response);
              document.getElementById('hide_me').style.visibility = "hidden";

            }
          });
        }

    }


</script>
@endsection
