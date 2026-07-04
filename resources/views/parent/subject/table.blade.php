@if(count($class_name) > 0)
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>

            <th>{{ get_phrase('Subject') }}</th>
            <th>{{ get_phrase('Class') }}</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>
                <?php $count = count($subjects); ?>
                @foreach($subjects as $key => $subject)
                    @if($key < $count-1)
                        {{ $subject['name']." ," }}
                    @else
                        {{ $subject['name'] }}
                    @endif
                @endforeach
            </td>

            @foreach($class_name as $class)
            <td>{{ $class['name'] }}</td>
             @endforeach
         </tr>
    </tbody>
</table>
@else
<div class="empty_box center">
    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
    <br>
    <span class="">{{ get_phrase('No data found') }}</span>
</div>
@endif


