@extends('admin.navigation')
   
@section('content')

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('All Message') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Contact Us') }}</a></li>
                        <li><a href="#">{{ get_phrase('All message') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="content">
    <div class="container p-0">
        <div class="card">
            <div class="row g-0">
                <div class="col-12 col-lg-5 col-xl-3 border-right">
                    <div class="px-4 d-none d-md-block">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <input type="text" id="search" class="form-control my-3" placeholder="Search...">
                            </div>
                        </div>
                            
                        <div id="user-results" class="d-none">
                        </div>
                    </div>
                    
                </div>
                <div class="col-12 col-lg-7 col-xl-9">
                    <div class="chat_body">
                        
                        <div class="py-2 px-4 border-bottom d-none d-lg-block">
                            <div class="d-flex align-items-center py-1">
                                <div class="position-relative">
                                    
                                </div>
                                <div class="flex-grow-1 pl-3">
                                    <strong class="chat_user_name">Add user</strong>
                                </div>
                                <div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="position-relative">
                            <div class="chat-messages p-4" id="chat_messages_body">
                                
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
        </div>
    </div>
</main>

<script>


    $(document).ready(function() {
        $('#search').on('keyup', function() {
            let query = $(this).val();

            if (query.trim() === '') {
                $('#user-results').addClass('d-none');
            } else {
                $('#user-results').removeClass('d-none');
                
                $.ajax({
                   
                    type: "GET",
                    data: { query: query },
                    success: function(data) {
                        $('#user-results').html(data);
                    }
                });
            }
        });

        $('#user-results').on('click', function() {
            $(this).removeClass('d-none'); 
        });
    });
</script>

@endsection