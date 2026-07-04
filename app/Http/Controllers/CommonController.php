<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Session;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Str;
use App\Models\Gradebook;
use App\Models\Subject;
use App\Models\School;
use DB;
use PDF;



class CommonController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_student_details_by_id($id = "", $api="")
    {

        //Fetch Details

        $enrol_data = Enrollment::where('user_id', $id)->first();

        $student = User::find($id);

        $info = json_decode($student->user_information);

        $parent_details = User::find($student->parent_id);

        $role = Role::where('role_id', $student->role_id)->first();

        $class_details = Classes::find($enrol_data->class_id);

        $section_details = Section::find($enrol_data->section_id);

        $active_session = get_school_settings($student->school_id)->value('running_session');

        $school_name = School::find($student->school_id)->value('title');

        //End Fetch


        $enrol_data['code'] = $student->code;
        $enrol_data['user_id'] = $id;
        $enrol_data['parent_name'] = $parent_details->name??"";
        $enrol_data['name'] = $student->name;
        $enrol_data['email'] = $student->email;

        $enrol_data['role'] = $role->name;

        $enrol_data['address'] = $info->address;
        $enrol_data['phone'] = $info->phone;
        $enrol_data['birthday'] = $info->birthday;
        $enrol_data['gender'] = $info->gender;
        $enrol_data['blood_group'] = $info->blood_group??"";
        $enrol_data['student_info']    = $student->student_info;
        $enrol_data['photo'] = isset($api) && $api == 'api' ? get_user_image_url($id): get_user_image($id);
        $enrol_data['school_id'] = $student->school_id;
        $enrol_data['school_name'] = $school_name;
        $enrol_data['running_session'] = $active_session;

        $enrol_data['class_name'] = $class_details->name ??"";
        $enrol_data['class_id'] = $class_details->id ??"";
        $enrol_data['section_name'] = $section_details->name ??"";
        $enrol_data['section_id'] = $section_details->id ??"";

        return $enrol_data;
    }

    public function getAdminDetails($id)
    {
        // $admin_details = User::where('school_id', auth()->user()->school_id);

        $user_details = User::find($id);

        $info = json_decode($user_details->user_information);
        
        $user_data['id'] = $user_details->id;
        $user_data['name'] = $user_details->name;
        $user_data['email'] = $user_details->email;

        $user_data['address'] = $info->address;
        $user_data['phone'] = $info->phone;
        $user_data['birthday'] = $info->birthday;
        $user_data['gender'] = $info->gender;
        $user_data['blood_group'] = $info->blood_group??"";
        $user_data['photo'] = get_user_image($id);
        $user_data['school_id'] = $user_details->school_id;

        return $user_data;
    }


    public function get_student_academic_info($id = "")
    {

        //Fetch Details
        $enrol_data = Enrollment::where('user_id', $id)->first();
        $student = User::find($id);
        $class_details = Classes::find($enrol_data->class_id);

        $section_details = Section::find($enrol_data->section_id);

        //End Fetch

        $enrol_data['parent_id'] = $student->parent_id;
        $enrol_data['code'] = $student->code;
        $enrol_data['user_id'] = $id;
        $enrol_data['name'] = $student->name;
        $enrol_data['email'] = $student->email;


        $enrol_data['class_name'] = $class_details->name ??"";
        $enrol_data['class_id'] = $class_details->id ??"";
        $enrol_data['section_name'] = $section_details->name ??"";
        $enrol_data['section_id'] = $section_details->id ??"";

        return $enrol_data;
    }



    public function classWiseStudents($id = '')
    {
        $enrollments = Enrollment::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($enrollments as $enrollment) :
            $student = User::find($enrollment->user_id);
            $options .= '<option value="' . $student->id . '">' . $student->name . '</option>';
        endforeach;
        echo $options;
    }

    public function classWiseSubject($id)
    {
        $subjects = Subject::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a subject' . '</option>';
        foreach ($subjects as $subject) :
            $options .= '<option value="' . $subject->id . '">' . $subject->name . '</option>';
        endforeach;
        echo $options;
        // return view('admin.examination.add_offline_exam', ['subjects' => $subjects]);
    }

    public function classWiseSections($id)
    {
        $sections = Section::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a section' . '</option>';
        foreach ($sections as $section) :
            $options .= '<option value="' . $section->id . '">' . $section->name . '</option>';
        endforeach;
        echo $options;
    }

    public function sectionWiseStudents($id)
    {
        $enrollments = Enrollment::where('section_id', $id)->where('school_id', auth()->user()->school_id)->get();
        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($enrollments as $enrollment) :
            $student = User::find($enrollment->user_id);
            $options .= '<option value="' . $student->id . '">' . $student->name . '</option>';
        endforeach;
        echo $options;
    }

    public function studentWiseParent($id)
    {
        $student = User::find($id);
        
        $parent_details = User::find($student->parent_id);

        $options = '<option value="">' . 'Select Parent' . '</option>';
        
            $options .= '<option value="' . $parent_details['id'] . '">' . $parent_details['name'] . '</option>';
        
        echo $options;
    }


    public function getGrade($acquired_mark = '')
    {
        echo get_grade($acquired_mark);
    }

    public function markUpdate(Request $request)
    {
        $data = $request->all();

        if (!empty($data['session_id'])) {
            $active_session  = $data['session_id'];
        } else {
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        }

        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        $query = Gradebook::where('exam_category_id', $data['exam_category_id'])
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('student_id', $data['student_id'])
            ->where('school_id', $data['school_id'])
            ->where('session_id', $data['session_id'])
            ->first();

        if (!empty($query) && $query->count() > 0) {

            $marks = json_decode($query->marks, true);
            $marks[$data['subject_id']] = $data['mark'];
            $query->marks = json_encode($marks);
            $query->comment = $data['comment'];
            $query->save();


        } else {
            $mark[$data['subject_id']] = $data['mark'];
            $marks = json_encode($mark);
            $data['marks'] = $marks;
            $data['timestamp'] = strtotime(date('Y-m-d'));
            Gradebook::create($data);
        }
    }

    public function get_user_by_id_from_user_table($id)
    {
        $user = User::find($id);

        return $user;
    }

    public function idWiseUserName($id='')
    {
        $result = User::where('id', $id)->value('name');
        return $result;
    }

    public function getClassDetails($id='')
    {
        $class_details = Classes::find($id);
        return $class_details;
    }

    public function getSectionDetails($id='')
    {
        $section_details = Section::find($id);
        return $section_details;
    }

    public function getSubjectDetails($id='')
    {
        $subject_details = Subject::find($id);
        return $subject_details;
    }

   

  

}
