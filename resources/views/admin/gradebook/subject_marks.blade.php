<?php 
use App\Models\Subject;

$subjects = json_decode($subject_wise_mark_list->marks, true);

$index = 0;

?>

<table class="table eTable">
	<thead>
		<tr>
			<th>#</th>
            <th>{{ get_phrase('Subject') }}</th>
            <th>{{ get_phrase('Marks') }}</th>
        </tr>
	</thead>

	<tbody>
		<?php foreach ($subjects as $key => $mark):

			$subject_details = json_decode(Subject::find($key), true); ?>
			
		    <tr>
		    	<td>{{ $index = $index+1 }}</td>
	            <td>{{ $subject_details['name'] }}</td>
	            <td>{{ $mark }}</td>
	        </tr>
		<?php endforeach; ?>
	</tbody>
</table>