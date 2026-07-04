<!--title-->
@extends('student.navigation')


@section('content')


<style>
	.list-bullets{}
	.list-bullets li{
		position:relative;
		padding-left:20px;
		padding-bottom:5px;
	}
	.list-bullets li:after {
    position: absolute;
    content: "";
    top: 8px;
    left: 0;
    background: #444;
    height: 8px;
    width: 8px;
    border-radius: 50%;
	}

	.title{
    position: sticky;
    top: 0px;
    left: 0;
    z-index: 999;
    background-color: #fff;
    padding-top: 2px;
	}

	.test{
    position: sticky;
    top: 0;
    left: 0;
    padding-top: 2px;
	}

	.qustions-container .x {
		max-height: calc(100% - 40px); /* Subtract the height of the table header */
		overflow-y: auto; /* Enable vertical scrolling if necessary */
		position: relative; /* Add this */
		z-index: 1; /* Add this */
	}

</style>


<!--title-->
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4 class="page-title">
              <i class="bi bi-book title_icon"></i></i> {{ App\Models\Addon\Assignment::find($assignment_id)->title }}
                        
            </h4>

            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="{{ route('student.dashboard') }}">{{ get_phrase('Home') }}</a></li>
              <li><a href="{{ route('student.assignment_home', ['type' => 'published']) }}">{{ get_phrase('My assignments') }}</a></li>
              <li><a href="#">{{ get_phrase('Questions') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
          		<a class="btn btn-outline-primary float m-1" href="{{ url()->previous() }}" ><i class="bi bi-arrow-left"></i>{{ get_phrase('Back') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
  <div class="col-12">
		<div class="eSection-wrap-2">
	    <div class="row">
	        <div class="col-md-8">
	        	<div class="title">
	        		
	            <h4 class="border-bottom mb-4 pb-2">Questions</h4>
	        	</div>
	            
            @foreach($questions as $question)

            <div class="row qustions-container" id="my_assignment_question_container">
              <div class="x">
              	
                <div class="eCard">
                    <div class="eCard-body col-md-12 mb-3 ">
                        <div class="d-flex justify-content-between align-items-center mb-3 py-2">
                            <h6 class="mb-0"><span >#</span> {{ $question['question'] }}</h6>
                            <div class="d-flex align-items-center">
                            	<span class="eBadge ebg-soft-dark">{{ get_phrase('Mark') }}: {{ $question['mark'] }}</span>
                            	@if(App\Models\Addon\AssignmentQuestion::find($question['id'])->answers()->where('student_id', auth()->user()->id)->exists())
                            	<span id="answerStatus-{{ $question['id'] }}" class="eBadge ebg-soft-success answer-status-answered">{{ get_phrase('answered') }}</span>
                            	@else
                            	<span id="answerStatus-{{ $question['id'] }}" class="eBadge ebg-soft-warning answer-status-pending">{{ get_phrase('pending') }}</span>
                            	@endif
                            </div>
                        </div>
                        <form id="answerForm-{{ $question['id'] }}" action="{{ route('student.assignment.questions.answer.save') }}" class="ajaxFormSubmission" method="POST" enctype="multipart/form-data">
                            @csrf 
                          <div class="form-group eForm">
                          	<input type="hidden" id="question_id-{{ $question['id'] }}" name="question_id" value="{{ $question['id'] }}">

                        @if($question['question_type'] == 'text')

                        		<textarea id="text-{{ $question['id'] }}" name="text_input" class="question-answer eForm-control" rows="2" placeholder="{{ get_phrase('Write your answer here') }}">@if(App\Models\Addon\AssignmentAnswer::where('question_id', $question['id'])->where('student_id', auth()->user()->id)->exists()){{ App\Models\Addon\AssignmentQuestion::find($question['id'])->answers()->where('student_id', auth()->user()->id)->first()->answer }}@endif</textarea>	
                        	
	                          <button type="submit" class="eBtn eBtn-blue float-end btn-sm mt-2"><span class="px-3">{{ get_phrase('Save') }}</span></button>
                        @elseif($question['question_type'] == 'file')
                        		<input class="form-control eForm-control-file" type="file" id="file-{{ $question['id'] }}" name="file_input" accept="image/*,.pdf,.txt,.doc,.docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                          	
                        		<div class="d-flex justify-content-between float-end">

                          		{{-- download previous if available --}}
                          		@if(App\Models\Addon\AssignmentQuestion::find($question['id'])->answers()->where('student_id', auth()->user()->id)->exists())
                          		<a class="btn btn-outline-primary float btn-sm mt-2 me-2" href="{{ route('previous.answer.download.file', ['question_id' => $question['id']]) }}" ><span class="pt-1 d-inline-block"><i class="bi bi-download"></i> {{ get_phrase('Download previous file') }}</span></a>
                          		@endif

                            	<button type="submit" class="eBtn eBtn-blue  btn-sm mt-2 "><span class="px-3">{{ get_phrase('Save') }}</span></button>
                        		</div>
                            @endif
                          </div>
                        </form>

                    </div>
                </div>
              </div>

            </div>

          @endforeach
            <div class="col-md-12 p-0 pe-3">
            	<form id="submit-my-assignment-form" class="default_class" action="{{ route('student.assignment.submit-assignment') }}"  method="POST">
            		@csrf
            		<input type="hidden" id="assignment_id" name="assignment_id" value="{{ $assignment_id }}">
                <button type="submit" class="btn btn-success float-end ">{{ get_phrase('Submit Assignment') }}</button>
            	</form>
            </div>

	        </div>

	        <div class="col-md-4">

	        	<div class="test">
	        		
	            <h4 class="border-bottom mb-4 pb-2">Deadline</h4>
	            <div class="eAlert eBtn-blueish" role="alert">
            		<h5 class="mb-3">{{ get_phrase('Notice !!') }}</h5>
            		<ul class="list-bullets">
            		  <li>{{ get_phrase('Be aware of the end date. You will not be able to answer afterwards.') }}</li>
            		  <li>{{ get_phrase('Make sure to save answers before submitting.') }}</li>
            		</ul>

                <hr>
                <strong>{{ get_phrase('End date:') }}</strong> {{ date('F j, Y, g:i A', (int)App\Models\Addon\Assignment::find($assignment_id)->end_timestamp) }}
	            </div>
	        	</div>
	        </div>
	    </div>
	  </div>   
  </div>
</div>








<script type="text/javascript">
	
	$(function () {
    $('.ajaxFormSubmission').ajaxForm({
      beforeSend: function() {
        var percentVal = '0%';
          
      },
      uploadProgress: function(event, position, total, percentComplete) {
        var percentVal = percentComplete + '%';
      },
      complete: function(xhr) {
        var response = xhr.responseText;
          
					
				//response contains question id
				var spanId = 'answerStatus-' + response;
				var spanElement = document.getElementById(spanId);

				if (spanElement) {
          // Modify the class and text content of the span element
          spanElement.className = 'eBadge ebg-soft-info';
          spanElement.textContent = 'saved';
      	}
        

      },
      error: function()
      {
        //You can write here your js error message
      }
    });
	});



</script>


@endsection

