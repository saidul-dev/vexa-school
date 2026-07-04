@extends('parent.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>{{ get_phrase('Routines') }}</h4>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="row mt-3">

                <div class="col-md-4"></div>

                <div class="col-md-3">
                    <select name="student" id="student_id" class="form-select eForm-control" required>
                        <option value="">{{ get_phrase('Select Student') }}</option>
                        @foreach($student_data as $key => $each_student)
                        <option value="{{ $each_student['user_id'] }}">{{ $each_student['name'] }}</option>
                        @endforeach


                    </select>
                </div>

                <div class="col-md-3">
                    <button class="eBtn eBtn btn-secondary" onclick="filter_class_routine()" >{{ get_phrase('Filter') }}</button>
                </div>

                <div class="card-body class_routine_content">
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                        <span class="">{{ get_phrase('No data found') }}</span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

<script type="text/javascript">

  "use strict";


    function filter_class_routine(){
        var student_id = $('#student_id').val();

        if(student_id != "" ){
            getFilteredClassRoutine();
        }else{
            toastr.error('{{ get_phrase('Please select student') }}');
        }
    }

    var getFilteredClassRoutine = function() {
        var student_id = $('#student_id').val();

        if(student_id != ""){
            let url = "{{ route('parent.routine.routine_list') }}";
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {student_id : student_id},
                success: function(response){
                    $('.class_routine_content').html(response);
                }
            });
        }
    }

</script>
