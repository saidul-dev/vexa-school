<?php 

use App\Models\TeacherPermission;

?>

<!-- Table -->
<div class="table-responsive">
	<table class="table eTable eTable-2">
		<thead>
		  <tr>
		    <th scope="col">#</th>
		    <th scope="col">{{ get_phrase('Teacher') }}</th>
		    <th scope="col">{{ get_phrase('Marks') }}</th>
		    <th scope="col">{{ get_phrase('Attendance') }}</th>
		</thead>
		<tbody>
		    @foreach($teachers as $teacher)
		    <?php 
		    	$permission = TeacherPermission::where('class_id', $class_id)
					->where('section_id', $section_id)
					->where('teacher_id', $teacher->id)
					->where('school_id', auth()->user()->school_id)
					->first();

				if(empty($permission)){
					$permission['marks'] = 0;
					$permission['attendance'] = 0;
				}
		    ?>
		    <?php 
		        $info = json_decode($teacher->user_information);
		        $user_image = $info->photo;
		        if(!empty($info->photo)){
		            $user_image = 'uploads/user-images/'.$info->photo;
		        }else{
		            $user_image = 'uploads/user-images/thumbnail.png';
		        }
		    ?>
		      <tr>
		        <th scope="row">
		          <p class="row-number">{{ $loop->index + 1 }}</p>
		        </th>
		        <td>
					<div class="dAdmin_info_name">
						<p><span>{{ $teacher->name }}</span></p>
					</div>
		        </td>
		        <td>
		        	<div class="eSwitches">
		        		<div class="form-check form-switch">
                          <input class="form-check-input form-switch-large" type="checkbox" value="{{ $permission['marks'] }}" role="switch" id="{{ $teacher['id'].'1' }}" onchange="togglePermission(this.id, 'marks', '{{ $teacher['id'] }}')" {{ $permission['marks'] == 1 ? 'checked':'' }} />
                        </div>
		        	</div>
		        </td>
		        <td>
		        	<div class="eSwitches">
		        		<div class="form-check form-switch">
                          <input class="form-check-input form-switch-large" type="checkbox" value="{{ $permission['attendance'] }}" role="switch" id="{{ $teacher['id'].'3' }}" onchange="togglePermission(this.id, 'attendance', '{{ $teacher['id'] }}')" {{ $permission['attendance'] == 1 ? 'checked':'' }} />
                        </div>
		        	</div>
		        </td>
		      </tr>
		    @endforeach
		</tbody>
	</table>
</div>

<!-- permission insert and update -->
<script type="text/javascript">
  
  	"use strict";

    function togglePermission(checkbox_id, column_name, teacher_id){

        var value = $('#'+checkbox_id).val();
        if($('#'+checkbox_id).prop('checked') == true){
		    value = 1;
		}else{
			value = 0;
		}
        console.log(value);
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();

        let url = "{{ route('admin.teacher.modify_permission') }}";

        $.ajax({
            url: url,
            headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {class_id : class_id, section_id : section_id, teacher_id : teacher_id, column_name : column_name,  value : value},
            success: function(response){
                // $('.permission_content').html(response);
                toastr.success('{{ get_phrase('Permission updated successfully.') }}');
            }
        });

    }
</script>