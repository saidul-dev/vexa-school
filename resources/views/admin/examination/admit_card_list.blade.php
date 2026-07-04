@extends('admin.navigation')
   
@section('content')

<style>
    .admit-card::before {
        content: "";
        background: url('{{  asset('assets/uploads/school_logo/'.DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo') ) }}') no-repeat center;
        background-size: 60%;
        opacity: 0.1;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
    }
</style>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Admit Card') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Examination') }}</a></li>
                        <li><a href="#">{{ get_phrase('Admit Card') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.examination.admit_card_create') }}', '{{ get_phrase('Create Admit Card') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Create New Admit Card') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <!-- Table -->
            <div class="table-responsive">
              <table class="table eTable eTable-2">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ get_phrase('Template Name') }}</th>
                    <th scope="col">{{ get_phrase('Admit Card') }}</th>
                    <th scope="col">{{ get_phrase('Option') }}</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($admit_cards as $key => $admit_card)
                    <tr>
                        <th scope="row">
                            <p class="row-number">{{ $loop->iteration }}</p>
                        </th>
                        <td>
                            <div class="dAdmin_profile_name">
                                <h4>{{ $admit_card->template }}</h4>
                            </div>
                        </td>
                        <td>
                            <div class="dAdmin_info_name min-w-250px">
                                <!-- Button trigger modal -->
                                <a href="" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $key }}">
                                    {{get_phrase('See Admit Card')}} </a>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal{{ $key }}" tabindex="-1" aria-labelledby="exampleModalLabel{{ $key }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="admit-card" >
                                                <div class="d-flex justify-content-between align-items-center">
                                                @if(empty($school_data->school_logo))
                                                    <img class="header-logo" src="{{ asset('assets/uploads/school_logo/'.DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo') ) }}">
                                                @else
                                                    <img class="header-logo" src="{{ asset('assets') }}/images/id_logo.png">
                                                @endif
                                                    
                                                    <h3>{{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</h3>
                                                    @if(empty($school_data->school_logo))
                                                    <img class="header-logo" src="{{ asset('assets/uploads/school_logo/'.DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo') ) }}">
                                                @else
                                                    <img class="header-logo" src="{{ asset('assets') }}/images/id_logo.png">
                                                @endif
                                                </div>
                                                <h4 class="mt-3">{{ $admit_card->heading }}</h4>
                                                <p class="mt-3 mb-3"><strong>{{ $admit_card->title }}</strong></p>

                                                <div class="d-flex justify-content-between">
                                                    <table class="table table-borderless info-table">
                                                        <tr>
                                                            <td><strong>ROLL NUMBER</strong></td>
                                                            <td>161066</td>
                                                            <td><strong>ADMISSION NO</strong></td>
                                                            <td>18S168375</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>CANDIDATE'S NAME</strong></td>
                                                            <td>EDWARD THOMAS</td>
                                                            <td><strong>CLASS</strong></td>
                                                            <td>1 (A)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>D.O.B</strong></td>
                                                            <td>8/10/2002</td>
                                                            <td><strong>GENDER</strong></td>
                                                            <td>MALE</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>FATHER'S NAME</strong></td>
                                                            <td>OLIVIER THOMAS</td>
                                                            <td><strong>MOTHER'S NAME</strong></td>
                                                            <td>CAROLINE THOMAS</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>ADDRESS</strong></td>
                                                            <td colspan="3">56 MAIN STREET, SUITE 3, BROOKLYN, NY 11210-0000</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>SCHOOL NAME</strong></td>
                                                            <td>{{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</td>
                                                            <td><strong>EXAM CENTER</strong></td>
                                                            <td>{{ $admit_card->exam_center }}</td>
                                                        </tr>
                                                    </table>
                                                    <div class="student-image ml-3"><img src="https://via.placeholder.com/120x150?text=No+Image" alt=""></div>
                                                </div>

                                                <div class="signature">
                                                @if($admit_card->sign)
                                                    <img src="{{ asset('assets/upload/user-docs/' . $admit_card->sign) }}" alt="Signature" style="width: 150px; height: auto;">
                                                @else
                                                    <p>No signature uploaded.</p>
                                                @endif
                                                    <p>_________________________</p>
                                                    <p>Signature</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="adminTable-action">
                                <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ get_phrase('Actions') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                    <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.examination.admit_card_edit', ['id' => $admit_card->id]) }}', '{{ get_phrase('Edit Admit Card') }}')">{{ get_phrase('Edit') }}</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.examination.admit_card_delete', ['id' => $admit_card->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>















@endsection