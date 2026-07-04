@extends('superadmin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Manage Faq') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Settings') }}</a></li>
                        <li><a href="#">{{ get_phrase('Manage Faq') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="largeModal('{{ route('superadmin.faq_add') }}', 'Create question and answer')">{{ get_phrase('Add question and answer') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="eSection-wrap">
        <div class="title">
          <h4>{{ get_phrase('Faq List') }}</h4>
          <p>{{ get_settings('faq_subtitle') }}</p>
        </div>
        <div class="eMain">
            <div class="row">
                @foreach($faqs as $faq)
                <div class="col-md-4">
                  <div class="eCard">
                    <div class="eCard-body">
                        <h5 class="eCard-title">{{ $faq->title }}</h5>
                        <p class="eCard-text">{{ $faq->description }}</p>

                        <button type="button" class="eBtn eBtn-blue dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{{ get_phrase('Action') }}</button>
                            <ul class="dropdown-menu">
                                <li>
                                  <a class="dropdown-item" href="javascript:;" onclick="largeModal('{{ route('superadmin.faq_edit', ['id' => $faq->id]) }}', '{{ get_phrase('Update question and answer') }}')">{{ get_phrase('Edit') }}</a>
                                </li>
                                <li>
                                  <hr class="dropdown-divider" />
                                </li>
                                <li>
                                  <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('superadmin.faq.delete', ['id' => $faq->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                </li>
                            </ul>
                    </div>
                  </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection