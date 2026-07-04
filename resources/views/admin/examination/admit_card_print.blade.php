@extends('admin.navigation')
   
@section('content')

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Print Admit Card') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Examination') }}</a></li>
              <li><a href="#">{{ get_phrase('Admit Card') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
             <div class="row">
                <form action="{{ route('admin.examination.admitCardFilter') }}">
                    <div class="att-filter d-flex flex-wrap">
                        <div class="att-filter-option">
                            <select class="form-select eForm-select eChoice-multiple-with-remove" id = "admit_card_id" name="admit_card_id">
                                <option value="">{{ get_phrase('Select category') }}</option>
                                @foreach ($admit_cards as $admit_card)
                                    <option value="{{ $admit_card->id }}">{{ $admit_card->template }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="att-filter-option">
                            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                                <option value="">{{ get_phrase('Select class') }}</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                            
                        <div class="att-filter-option">
                            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                                <option value="">{{ get_phrase('First select a class') }}</option>
                            </select>
                        </div>
                        
                        <div class="att-filter-option">
                            <select name="session_id" id="session_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                                <option value="">{{ get_phrase('Select a session') }}</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->session_title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="att-filter-btn">
                            <button type="submit" class="eBtn eBtn btn-secondary" onclick="filter_admit_card()">{{ get_phrase('Filter') }}</button>
                        </div>
                    </div>
                </form>
                
                <div class="card-body table-responsive admit_card_content">
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

  "use strict";

    function classWiseSection(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
                classWiseSubect(classId);
            }
        });
    }

    function classWiseSubect(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#subject_id').html(response);
            }
        });
    }

    function filter_admit_card(){
        var admit_card_id = $('#admit_card_id').val();
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var session_id = $('#session_id').val();
        if(admit_card_id != "" &&  class_id != "" && section_id != "" && session_id != ""){
            getFilteredAdmitCard();
        }else{
            toastr.error('{{ get_phrase('Please select all the fields') }}');
        }
    }

    var getFilteredAdmitCard = function() {
        var admit_card_id = $('#admit_card_id').val();
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var session_id = $('#session_id').val();
        if(admit_card_id != "" &&  class_id != "" && section_id!= "" && session_id != ""){
            let url = "{{ route('admin.examination.admitCardFilter') }}";
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {admit_card_id: admit_card_id, class_id : class_id, section_id : section_id, session_id: session_id},
                
            });
        }
    }

</script>

@endsection
