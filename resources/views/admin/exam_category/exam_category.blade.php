@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Exam Category') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Examination') }}</a></li>
              <li><a href="#">{{ get_phrase('Exam Category') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.exam_category.open_modal') }}', '{{ get_phrase('Create Exam Category') }}')">{{ get_phrase('Add Exam Category') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- Start Exam Category area -->
<div class="row">
    <div class="col-7 offset-md-2">
        <div class="eSection-wrap">
            @if(count($exam_categories) > 0)
            <!-- Table -->
            <div class="table-responsive tScrollFix pb-2">
                <table class="table eTable">
                	<thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ get_phrase('Title') }}</th>
                            <th scope="col" class="text-end">{{ get_phrase('Options') }}</th>
                        </tr>
                	</thead>
                	<tbody>
                		@foreach($exam_categories as $key => $exam_category)
                		<tr>
                			<td>
                				{{ $key+1 }}
                			</td>
                			<td>
                                {{ $exam_category['name'] }}
    						</td>
    						<td class="text-center">
                                <div class="adminTable-action">
                                    <button
                                        type="button"
                                        class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        {{ get_phrase('Actions') }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                                    >
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.edit.exam_category', ['id' => $exam_category->id]) }}', '{{ get_phrase('Edit Exam Category') }}')">{{ get_phrase('Edit') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.exam_category.delete', ['id' => $exam_category->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                        </li>
                                    </ul>
                                </div>					
    						</td>
                		</tr>
                		@endforeach
                	</tbody>
                </table>
            </div>
            @else
            <div class="exam_catrgories_content">
                <div class="empty_box center">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<!-- End Exam category area -->
@endsection