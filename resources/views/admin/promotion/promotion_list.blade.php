<?php 
use App\Models\User;
use App\Models\Section;
use App\Models\Classes;
?>

<div class="table-responsive">
    <table class="table eTable">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ get_phrase('Image') }}</th>
                <th>{{ get_phrase('Student name') }}</th>
                <th>{{ get_phrase('Section') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th>{{ get_phrase('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($promotion_list as $enrollment)

            <?php 
            $student_details = User::find($enrollment->user_id);
            $info = json_decode($student_details->user_information);
            $user_image = $info->photo;
            if(!empty($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            } 
            $section_details = Section::find($enrollment->section_id);
            $class_to_details = Classes::find($class_id_to);
            $class_from_details = Classes::find($class_id_from);
            ?>
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>
                    <img class="img-fluid" src="{{ asset('assets') }}/{{ $user_image }}" width="50" height="50">
                </td>
                <td>
                    <div class="dAdmin_profile_name">
                      <span>{{ $student_details->name }}</span>
                    </div>
                </td>
                <td>{{ $section_details->name }}</td>
                <td>
                    <span class="eBadge ebg-success display-none-view" id = "success_{{ $enrollment->id }} " >Prmoted</span>
                    <span class="eBadge ebg-primary"  id = "danger_{{ $enrollment->id }}">Not promoted yet</span>
                </td>
                <td>
                  <button type="button" class="btn btn-icon btn-success btn-sm" onclick="enrollStudent('{{ $enrollment->id.'-'.$class_id_to.'-'.$section_id_to.'-'.$session_id_to }}', '{{ $enrollment->id }}')">{{ get_phrase('Enroll to') }}<strong> {{ $class_to_details->name }} </strong> </button> <br><br>
                  <button type="button" class="btn btn-icon btn-secondary btn-sm" onclick="enrollStudent('{{ $enrollment->id.'-'.$class_id_from.'-'.$section_id_from.'-'.$session_id_to }}', '{{ $enrollment->id }}')">{{ get_phrase('Enroll to') }}<strong> {{ $class_from_details->name }} </strong> </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script type="text/javascript">

  "use strict";

    function enrollStudent(promotion_data, enroll_id) {
        let url = "{{ route('admin.promotion.promote', ['promotion_data' => ":promotion_data"]) }}";
        url = url.replace(":promotion_data", promotion_data);
      $.ajax({
        type : 'get',
        url: url,
        success : function(response) {
            console.log(response);
          if (response) {
            $("#success_"+enroll_id).show();
            $("#danger_"+enroll_id).hide();
            toastr.success("{{ 'Student promoted successfully' }}");
          }else{
            toastr.error("{{ 'An error occured' }}");
          }
        }
      });
    }
</script>