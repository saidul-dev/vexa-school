@extends('admin.navigation')
   
@section('content')

@php
	$class_wise_attandance = array();
	$total_student = 0;
	$todays_total_attandance = 0;
	$all_classes = DB::table('classes')->where('school_id', auth()->user()->school_id)->get();
	$currently_session_id = DB::table('sessions')->where('status', 1)->value('id');

	foreach($all_classes as $class){
		$total_student += DB::table('enrollments')->where('session_id', $currently_session_id)->where('class_id', $class->id)->where('school_id', auth()->user()->school_id)->get()->count();

		$start_date = strtotime(date('d M Y'));
		$end_date = $start_date + 86400;
		$today_attanded = DB::table('daily_attendances')->where('class_id', $class->id)->where('timestamp', '>=', $start_date)->where('timestamp', '<', $end_date)->get();
		array_push($class_wise_attandance, array("class_name" => $class->name, "today_attended" => $today_attanded->count()));
		$todays_total_attandance += $today_attanded->count();
	}
@endphp

	<!-- Mani section header and breadcrumb -->
	<div class="mainSection-title">
	<div class="row">
	  <div class="col-12">
	    <div
	      class="d-flex justify-content-between align-items-center flex-wrap gr-15"
	    >
	      <div class="d-flex flex-column">
	        <h4>{{ get_phrase('Dashboard') }}</h4>
	        <ul class="d-flex align-items-center eBreadcrumb-2">
	          <li><a href="#">{{ get_phrase('Home') }}</a></li>
	          <li><a href="#">{{ get_phrase('Dashboard') }}</a></li>
	        </ul>
	      </div>
	    </div>
	  </div>
	</div>
	</div>

	<!-- Start Alerts -->
	<div class="row">
	<div class="col-12">
	  <div class="eSection-dashboardItems">
	    <div class="row flex-wrap">
	      <!-- Dashboard Short Details -->
	      <div class="col-lg-6">
	        <div class="dashboard_ShortListItems">
	          <div class="row">
	            <div class="col-md-6">
	              <div class="dashboard_ShortListItem">
	                <div
	                  class="dsHeader d-flex justify-content-between align-items-center"
	                >
	                  <h5 class="title">{{ get_phrase('Students') }}</h5>
	                  <a href="{{ route('admin.student') }}" class="ds_link ds_sutdent">
	                    <svg
	                      xmlns="http://www.w3.org/2000/svg"
	                      width="10.146"
	                      height="4.764"
	                      viewBox="0 0 10.146 4.764"
	                    >
	                      <path
	                        id="Read_more_icon"
	                        data-name="Read more icon"
	                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
	                        transform="translate(-3.573 -5.978)"
	                        fill="#000000"
	                      />
	                    </svg>
	                  </a>
	                </div>
	                <div
	                  class="dsBody d-flex justify-content-between align-items-center"
	                >
	                  <div class="ds_item_details">
	                    <h4 class="total_no">{{ DB::table('users')->where('role_id', 7)->where('school_id', auth()->user()->school_id)->get()->count() }}</h4>
	                    <p class="total_info">{{ get_phrase('Total Student') }}</p>
	                  </div>
	                  <div class="ds_item_icon">
	                    <img
	                      src="{{ asset('assets/images/Student_icon.png') }}"
	                      alt=""
	                    />
	                  </div>
	                </div>
	              </div>
	            </div>
	            <div class="col-md-6">
	              <div class="dashboard_ShortListItem">
	                <div
	                  class="dsHeader d-flex justify-content-between align-items-center"
	                >
	                  <h5 class="title">{{ get_phrase('Teacher') }}</h5>
	                  <a href="{{ route('admin.teacher') }}" class="ds_link ds_teacher">
	                    <svg
	                      xmlns="http://www.w3.org/2000/svg"
	                      width="10.146"
	                      height="4.764"
	                      viewBox="0 0 10.146 4.764"
	                    >
	                      <path
	                        id="Read_more_icon"
	                        data-name="Read more icon"
	                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
	                        transform="translate(-3.573 -5.978)"
	                        fill="#000000"
	                      />
	                    </svg>
	                  </a>
	                </div>
	                <div
	                  class="dsBody d-flex justify-content-between align-items-center"
	                >
	                  <div class="ds_item_details">
	                    <h4 class="total_no">{{ DB::table('users')->where('role_id', 3)->where('school_id', auth()->user()->school_id)->get()->count() }}</h4>
	                    <p class="total_info">{{ get_phrase('Total Teacher') }}</p>
	                  </div>
	                  <div class="ds_item_icon">
	                    <img
	                      src="{{ asset('assets/images/Teacher_icon.png') }}"
	                      alt=""
	                    />
	                  </div>
	                </div>
	              </div>
	            </div>
	            <div class="col-md-6">
	              <div class="dashboard_ShortListItem">
	                <div
	                  class="dsHeader d-flex justify-content-between align-items-center"
	                >
	                  <h5 class="title">{{ get_phrase('Parents') }}</h5>
	                  <a href="{{ route('admin.parent') }}" class="ds_link ds_parent">
	                    <svg
	                      xmlns="http://www.w3.org/2000/svg"
	                      width="10.146"
	                      height="4.764"
	                      viewBox="0 0 10.146 4.764"
	                    >
	                      <path
	                        id="Read_more_icon"
	                        data-name="Read more icon"
	                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
	                        transform="translate(-3.573 -5.978)"
	                        fill="#000000"
	                      />
	                    </svg>
	                  </a>
	                </div>
	                <div
	                  class="dsBody d-flex justify-content-between align-items-center"
	                >
	                  <div class="ds_item_details">
	                    <h4 class="total_no">{{ DB::table('users')->where('role_id', 6)->where('school_id', auth()->user()->school_id)->get()->count() }}</h4>
	                    <p class="total_info">{{ get_phrase('Total Parent') }}</p>
	                  </div>
	                  <div class="ds_item_icon">
	                    <img
	                      src="{{ asset('assets/images/Parents_icon.png') }}"
	                      alt=""
	                    />
	                  </div>
	                </div>
	              </div>
	            </div>
	            <div class="col-md-6">
	              <div class="dashboard_ShortListItem">
	                <div
	                  class="dsHeader d-flex justify-content-between align-items-center"
	                >
	                  <h5 class="title">{{ get_phrase('Staff') }}</h5>
	                </div>
	                <div
	                  class="dsBody d-flex justify-content-between align-items-center"
	                >
	                  <div class="ds_item_details">
	                  	@php $admin = DB::table('users')->where('role_id', 2)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
	                  	@php $teacher = DB::table('users')->where('role_id', 3)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
						@php $accountant = DB::table('users')->where('role_id', 4)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
						@php $librarian = DB::table('users')->where('role_id', 5)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
	                    <h4 class="total_no">{{ $admin + $teacher + $accountant + $librarian }}</h4>
	                    <p class="total_info">{{ get_phrase('Total Staff') }}</p>
	                  </div>
	                  <div class="ds_item_icon">
	                    <img
	                      src="{{ asset('assets/images/Staff_icon.png') }}"
	                      alt=""
	                    />
	                  </div>
	                </div>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	      <!-- Attendance -->
	      <div class="col-lg-6">
	        <div class="dashboard_report dashboard_attendance">
	          <div class="ds_report_header d-flex justify-content-between align-items-start">
	            <div class="ds_report_left">
	              <h4 class="title">{{ get_phrase('Todays Attendance') }}</h4>
	              <div
	                class="ds_report_count d-flex align-items-center"
	              >
	                <span class="total_no">{{ $todays_total_attandance }}</span>
	                <div class="ds_attend_percent">
	                  <div class="icon">
	                    <svg
	                      xmlns="http://www.w3.org/2000/svg"
	                      width="16.507"
	                      height="10.25"
	                      viewBox="0 0 16.507 10.25"
	                    >
	                      <g
	                        id="Group_2395"
	                        data-name="Group 2395"
	                        transform="translate(-343.381 -436.505)"
	                      >
	                        <path
	                          id="Path_1631"
	                          data-name="Path 1631"
	                          d="M0,4.347l4.83-3.26L6.279,5.072,12.076,0"
	                          transform="matrix(0.998, -0.07, 0.07, 0.998, 344.122, 440.793)"
	                          fill="none"
	                          stroke="#fff"
	                          stroke-linecap="round"
	                          stroke-width="1"
	                        />
	                        <g
	                          id="Polygon_2"
	                          data-name="Polygon 2"
	                          transform="matrix(0.643, 0.766, -0.766, 0.643, 356.596, 436.505)"
	                          fill="#fff"
	                        >
	                          <path
	                            d="M 4.187728404998779 3.341484308242798 L 0.9342562556266785 3.341484308242798 L 2.560992240905762 0.9013835787773132 L 4.187728404998779 3.341484308242798 Z"
	                            stroke="none"
	                          />
	                          <path
	                            d="M 2.560992240905762 1.802777767181396 L 1.868520259857178 2.841484546661377 L 3.253464221954346 2.841484546661377 L 2.560992240905762 1.802777767181396 M 2.560992240905762 4.529953002929688e-06 L 5.121982097625732 3.841484308242798 L 2.384185791015625e-06 3.841484308242798 L 2.560992240905762 4.529953002929688e-06 Z"
	                            stroke="none"
	                            fill="#fff"
	                          />
	                        </g>
	                      </g>
	                    </svg>
	                  </div>
	                  @if($total_student > 0)
	                  	<span>{{ (100 / $total_student) * $todays_total_attandance }}%</span>
	                  @else
	                  <span>0%</span>
	                  @endif
	                </div>
	              </div>
	            </div>
	            <a href="{{ route('admin.daily_attendance') }}" class="all_report_btn">{{ get_phrase('Go to Attendance') }}</a>
	          </div>
	          <div class="ds_report_list">
				<div id="chartdiv" class="chartdiv"></div>
			  </div>
	        </div>
	      </div>
	      <!-- Imcome Report -->

		  @php
		  	$total_income = 0;
		  	$first_day_of_this_month = strtotime(date('1 M Y', time()));
		  	$last_day_of_this_month = strtotime(date('Y-m-t', time()));
			$monthly_incomes = DB::table('student_fee_managers')->where('school_id', auth()->user()->school_id)->where('status', 'paid')->where('timestamp', '>=', $first_day_of_this_month)->where('timestamp', '<=', $last_day_of_this_month)->get();
			foreach($monthly_incomes as $monthly_income):
				$total_income += $monthly_income->total_amount;
			endforeach;
		  @endphp

	      <div class="col-lg-7 col-md-6">
	        <div class="dashboard_report dashboard_income_report">
	          <div
	            class="ds_report_header d-flex justify-content-between align-items-start"
	          >
	            <div class="ds_report_left">
	              <h4 class="title">{{ get_phrase('Income Report') }}</h4>
	              <div
	                class="ds_report_count d-flex align-items-center"
	              >
	                <span class="total_no">{{ currency($total_income) }}</span>
	              </div>
	            </div>
	            <div class="verticalMenu">
	              <button
	                type="button"
	                class="eBtn dropdown-toggle"
	                data-bs-toggle="dropdown"
	                aria-expanded="false"
	              >
	                <svg
	                  xmlns="http://www.w3.org/2000/svg"
	                  width="5"
	                  height="20.999"
	                  viewBox="0 0 5 20.999"
	                >
	                  <path
	                    id="Union_6"
	                    data-name="Union 6"
	                    d="M-4856,309.5a2.5,2.5,0,0,1,2.5-2.5,2.5,2.5,0,0,1,2.5,2.5,2.5,2.5,0,0,1-2.5,2.5A2.5,2.5,0,0,1-4856,309.5Zm0-8a2.5,2.5,0,0,1,2.5-2.5,2.5,2.5,0,0,1,2.5,2.5,2.5,2.5,0,0,1-2.5,2.5A2.5,2.5,0,0,1-4856,301.5Zm0-8a2.5,2.5,0,0,1,2.5-2.5,2.5,2.5,0,0,1,2.5,2.5,2.5,2.5,0,0,1-2.5,2.5A2.5,2.5,0,0,1-4856,293.5Z"
	                    transform="translate(4856 -291)"
	                    fill="#cffbe3"
	                  />
	                </svg>
	              </button>
	              <ul
	                class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
	              >
	                <li><a class="dropdown-item" href="#">{{ get_phrase('Year') }}</a></li>
	                <li><a class="dropdown-item" href="#">{{ get_phrase('Month') }}</a></li>
	                <li><a class="dropdown-item" href="#">{{ get_phrase('Week') }}</a></li>
	              </ul>
	            </div>
	          </div>
	          <div class="ds_report_list"></div>
	        </div>
	      </div>
	      <!-- Upcoming Events -->
	      <div class="col-lg-5 col-md-6">
	        <div class="dashboard_report dashboard_upcoming_events">
	          <div
	            class="ds_report_header d-flex justify-content-between align-items-start"
	          >
	            <div class="ds_report_left">
	              <h4 class="title">{{ get_phrase('Upcoming Events') }}</h4>
	            </div>
	            
	          </div>
	          <div class="ds_report_list pt-38">
	            <ul class="upcoming_events_items d-flex flex-column">

					@php $upcoming_events = DB::table('frontend_events')->where('school_id', auth()->user()->school_id)->where('timestamp', '>', time())->where('status', 1)->take(3)->orderBy('id', 'DESC')->get(); @endphp
					@foreach($upcoming_events as $upcoming_event)
					<li>
						<div
						class="upcoming_events_item d-flex justify-content-between align-items-start"
						>
						<div class="events_info">
							<a href="#" class="title">{{ $upcoming_event->title }}</a>
							<p class="date">{{ date('D, M d Y', $upcoming_event->timestamp) }}</p>
						</div>
						
						</div>
					</li>
					@endforeach
	            </ul>
	            <div class="text-end">
	              <a href="{{route('admin.events.list')}}" class="all_report_btn_2">{{ get_phrase('See all') }}</a>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	</div>


<!-- Resources -->
<script src="{{asset('assets/amchart/index.js')}}"></script>
<script src="{{asset('assets/amchart/xy.js')}}"></script>
<script src="{{asset('assets/amchart/animated.js')}}"></script>

<!-- Chart code -->
<script>
"use strict";

am5.ready(function() {

// Create root element
var root = am5.Root.new("chartdiv");


// Set themes
root.setThemes([
  am5themes_Animated.new(root)
]);


// Create chart
var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: true,
  panY: true,
  wheelX: "panX",
  wheelY: "zoomX",
  pinchZoomX:true
}));

// Add cursor
var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
cursor.lineY.set("visible", false);


// Create axes
var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
xRenderer.labels.template.setAll({
  rotation: -90,
  centerY: am5.p50,
  centerX: am5.p100,
  paddingRight: 15
});

var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  maxDeviation: 0.3,
  categoryField: "class_name",
  renderer: xRenderer,
  tooltip: am5.Tooltip.new(root, {})
}));

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  maxDeviation: 0.3,
  renderer: am5xy.AxisRendererY.new(root, {})
}));


// Create series
var series = chart.series.push(am5xy.ColumnSeries.new(root, {
  name: "Series 1",
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "today_attended",
  sequencedInterpolation: true,
  categoryXField: "class_name",
  tooltip: am5.Tooltip.new(root, {
    labelText:"{valueY}"
  })
}));

series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 });
series.columns.template.adapters.add("fill", function(fill, target) {
  return chart.get("colors").getIndex(series.columns.indexOf(target));
});

series.columns.template.adapters.add("stroke", function(stroke, target) {
  return chart.get("colors").getIndex(series.columns.indexOf(target));
});


// Set data
var data = <?php echo json_encode($class_wise_attandance); ?>;

xAxis.data.setAll(data);
series.data.setAll(data);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
series.appear(1000);
chart.appear(1000, 100);

}); // end am5.ready()
</script>

<!-- HTML -->


@endsection