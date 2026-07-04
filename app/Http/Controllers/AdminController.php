<?php
namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Session;
use App\Models\School;
use App\Models\Subscription;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Gradebook;
use App\Models\Grade;
use App\Models\Department;
use App\Models\ClassRoom;
use App\Models\ClassList;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use App\Models\Routine;
use App\Models\Syllabus;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StudentFeeManager;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Noticeboard;
use App\Models\FrontendEvent;
use App\Models\Package;
use App\Models\PaymentMethods;
use App\Models\Currency;
use App\Models\PaymentHistory;
use App\Models\TeacherPermission;
use App\Models\Payments;
use App\Models\Feedback;
use App\Models\MessageThrade;
use App\Models\Chat;
use App\Models\AdmitCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Mail;
use App\Mail\FreeEmail;
use App\Mail\StudentsEmail;
use App\Mail\NewUserEmail;

use PDF;


use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{

    private $user;
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth()->user();
            $this->check_subscription_status(Auth()->user()->school_id);
            $this->insert_gateways();
            return $next($request);
        });


    }

    function check_subscription_status($school_id = ""){
        $current_route = Route::currentRouteName();
        $has_subscription = Subscription::where('school_id', $school_id)->where('status', 1)->get()->count();
        $active_subscription = Subscription::where('school_id', $school_id)->where('active', 1)->first();
        
        $today = date("Y-m-d");
        $today_time = strtotime($today);
        
        if($has_subscription != 0) {
            if($active_subscription['expire_date'] == '0'){
                $expiry_status = '0';
            }else{
                $expiry_status = (int)$active_subscription['expire_date'] < $today_time;
            }     
             
            if(
                ($current_route != 'admin.subscription' && $expiry_status) &&
                ($current_route != 'admin.subscription.purchase' && $expiry_status) &&
                ($current_route != 'admin.subscription.payment' && $expiry_status) &&
                ($current_route != 'admin.subscription.offline_payment' && $expiry_status)
            )
            {
                redirect()->route('admin.subscription')->send();
            }

        } else {

            if(
                ($current_route != 'admin.subscription' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.purchase' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.payment' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.offline_payment' && $has_subscription == 0)
            )
            {
                redirect()->route('admin.subscription')->send();
            }
        }
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function check_admin_subscription($school_id)
     {
         $validity_of_current_package=Subscription::where('school_id',$school_id)->where('active',1)->first();
         if(!empty($validity_of_current_package))
         {
             $validity_of_current_package= $validity_of_current_package->toArray();
 
 
             $today = date("Y-m-d");
             $today_time = strtotime($today);
 
             if((int)$validity_of_current_package['expire_date'] < $today_time  )
             {
                 $this->adminDashboard();
 
             }
             else
             {
 
                
             }
 
 
         }
         else
         {
 
 
         }
 
     }



    public function adminDashboard()
    {
        $account_status = auth()->user()->account_status;
        if(auth()->user()->role_id != "") {
            return view('admin.dashboard');
        } else {
            redirect()->route('login')
                ->with('error','You are not logged in.');
        }
    }

    /**
     * Show the admin list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function adminList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $admins = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 2);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 2);
                })->paginate(10);

        } else {
            $admins = User::where('role_id', 2)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.admin.admin_list', compact('admins', 'search'));
    }

    /**
     * Show the admin add modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createModal()
    {
        return view('admin.admin.add_admin');
    }

    public function adminCreate(Request $request)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo,
            'school_role' => 0,
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);


        if(count($duplicate_user_check) == 0) {
            
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '2',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        } else {
            return redirect()->back()->with('error','Email was already taken.');
        }
        if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message','You have successfully add user.');
    }

    public function editModal($id)
    {
        $user = User::find($id);
        return view('admin.admin.edit_admin', ['user' => $user]);
    }

    public function adminUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        return redirect()->back()->with('message','You have successfully update user.');
    }

    public function adminDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 2);
        return redirect()->route('admin.admin')->with('message','You have successfully deleted user.');
    }

    public function menuSettingsView($id)
     {
        $user = User::find($id);
        return view('admin.admin.menu_permission', ['user' => $user]);
     }


    public function menuPermissionUpdate(Request $request, $id)
    {

        User::where('id', $id)->update([
            'menu_permission' => json_encode($request->permissions),
        ]);

        return redirect()->back()->with('message', 'You have successfully updated user permissions.');
    }


    public function adminProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.admin.admin_profile', ['user_details' => $user_details]);
        // return view('admin.admin.admin_profile');
    }
    
    function school_user_password(Request $request){

        $userId = $request->input('user_id');

        $data['password'] = Hash::make($request->password);
        User::where('id', $userId)->update($data);

        return redirect()->back()->with('message', 'You have successfully update password.');
    }

    public function adminDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.admin.documents', ['user_details' => $user_details]);
    }

    public function accountantDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.accountant.documents', ['user_details' => $user_details]);
    }

    public function librarianDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.librarian.documents', ['user_details' => $user_details]);
    }

    public function parentDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.parent.documents', ['user_details' => $user_details]);
    }

    public function studentDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.student.documents', ['user_details' => $user_details]);
    }

    public function teacherDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.teacher.documents', ['user_details' => $user_details]);
    }

    public function documentsUpload(Request $request, $id="")
    {
        // Validate the request
        $request->validate([
            'file_name' => 'required',
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Get the current user
        $user = User::find($id);

        $filePath = $file->move(public_path('assets/uploads/user-docs/'.$user->id.'/'), $fileName);

        // Get existing documents or initialize as an empty array
        $documents = $user->documents ? json_decode($user->documents, true) : [];

        // Add the new document with the provided file name
        $documents[slugify($request->input('file_name'))] = $fileName;

        // Update the user's documents
        $user->update(['documents' => json_encode($documents)]);
        
        return redirect()->back()->with('message', 'File uploaded successfully.');
    }

    public function documentsRemove($id = "", $file_name="")
    {
        // Find the user by ID
        $user = User::find($id);

        if ($user) {
            // Get the documents as an array
            $documents = json_decode($user->documents, true);

            // Check if the file with the given file_name exists
            if (isset($documents[$file_name])) {
                $file_path = public_path('assets/uploads/user-docs/'.$user->id.'/'.$documents[$file_name]);
                
                // Check if the file exists
                if (file_exists($file_path)) {
                    // Delete the file
                    unlink($file_path);

                    // Remove the file entry from the documents array
                    unset($documents[$file_name]);

                    // Update the user's documents column
                    $user->update(['documents' => json_encode($documents)]);

                    return redirect()->back()->with('message', 'File removed successfully.');
                } else {
                    return redirect()->back()->with('error', 'File not found.');
                }
            } else {
                return redirect()->back()->with('error', 'File not found.');
            }
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }


    /**
     * Show the teacher list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teacherList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $teachers = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 3);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 3);
                })->paginate(10);

        } else {
            $teachers = User::where('role_id', 3)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.teacher.teacher_list', compact('teachers', 'search'));
    }

    /**
     * Show the teacher add modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createTeacherModal()
    {
        $departments = Department::get()->where('school_id', auth()->user()->school_id);
        return view('admin.teacher.add_teacher', ['departments' => $departments]);
    }

    public function adminTeacherCreate(Request $request)
    {
        $data = $request->all();
         if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '3',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
            'department_id' => $data['department_id'],
            'designation' => $data['designation'],
        ]);
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
    if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
        Mail::to($data['email'])->send(new NewUserEmail($data));
    }
        return redirect()->back()->with('message','You have successfully add teacher.');
    }

    public function teacherEditModal($id)
    {
        $user = User::find($id);
        $departments = Department::get()->where('school_id', auth()->user()->school_id);
        return view('admin.teacher.edit_teacher', ['user' => $user, 'departments' => $departments]);
    }

    public function teacherUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
            'department_id' => $data['department_id'],
            'designation' => $data['designation'],
        ]);
        return redirect()->back()->with('message','You have successfully update teacher.');
    }

    public function teacherDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 3);
        return redirect()->route('admin.teacher')->with('message','You have successfully deleted teacher.');
    }
    public function teacherProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.teacher.teacher_profile', ['user_details' => $user_details]);
    }

    /**
     * Show the accountant list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function accountantList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $accountants = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 4);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 4);
                })->paginate(10);

        } else {
            $accountants = User::where('role_id', 4)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.accountant.accountant_list', compact('accountants', 'search'));
    }

    public function createAccountantModal()
    {
        return view('admin.accountant.add_accountant');
    }

    public function accountantCreate(Request $request)
    {
        $data = $request->all();
         if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '4',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);
        
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
    if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
        Mail::to($data['email'])->send(new NewUserEmail($data));
    }
        return redirect()->back()->with('message','You have successfully add accountant.');
    }

    public function accountantEditModal($id)
    {
        $user = User::find($id);
        return view('admin.accountant.edit_accountant', ['user' => $user]);
    }

    public function accountantUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        return redirect()->back()->with('message','You have successfully update accountant.');
    }

    public function accountantDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 4);
        return redirect()->route('admin.accountant')->with('message','You have successfully deleted accountant.');
    }

    public function accountantProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.accountant.accountant_profile', ['user_details' => $user_details]);
    }

    /**
     * Show the librarian list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function librarianList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $librarians = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 5);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 5);
                })->paginate(10);

        } else {
            $librarians = User::where('role_id', 5)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.librarian.librarian_list', compact('librarians', 'search'));
    }

    public function createLibrarianModal()
    {
        return view('admin.librarian.add_librarian');
    }

    public function librarianCreate(Request $request)
    {
        $data = $request->all();
         if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '5',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
    if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
        Mail::to($data['email'])->send(new NewUserEmail($data));
    }
        return redirect()->back()->with('message','You have successfully add librarian.');
    }

    public function librarianEditModal($id)
    {
        $user = User::find($id);
        return view('admin.librarian.edit_librarian', ['user' => $user]);
    }

    public function librarianUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);
        return redirect()->back()->with('message','You have successfully update librarian.');
    }

    public function librarianDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 5);
        return redirect()->route('admin.librarian')->with('message','You have successfully deleted librarian.');
    }

    public function librarianProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.librarian.librarian_profile', ['user_details' => $user_details]);
    }


    /**
     * Show the parent list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function parentList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $parents = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 6);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 6);
                })->paginate(10);

        } else {
            $parents = User::where('role_id', 6)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.parent.parent_list', compact('parents', 'search'));
    }

    public function createParent()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.parent.add_parent', ['classes' => $classes]);
    }


    public function parentCreate(Request $request)
    {
        $data = $request->all();
        
        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        $parent = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '6',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
        $students = $data['student_id'];
        $class_id = $data['class_id'];
        $section_id = $data['student_id'];

        foreach($students as $student){
            $users = User::where('id', $student)->get();

            if(count($users) == 1) {
                User::where('id', $student)->update([
                    'parent_id' => $parent->id,
                ]);
            } else {
                if(count($users) > 1) {
                    foreach($users as $user){
                        $data = Enrollment::where('class_id', $class_id)->where('section_id', $section_id)->where('user_id', $user->id)->where('school_id', auth()->user()->school_id)->first();

                        if($data != '') {
                            User::where('id', $user->id)->update([
                                'parent_id' => $parent->id,
                            ]);
                        }
                    }
                }
            }
        }
        if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        
        return redirect()->back()->with('message','You have successfully add parent.');
    }


    public function parentEditModal($id)
    {
        $user = User::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.parent.edit_parent', ['user' => $user, 'classes' => $classes]);
    }

    public function parentUpdate(Request $request, $id)
    {
        $data = $request->all();


        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {

            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);


        //Previous parent has been empty
        User::where('parent_id', $id)->update(['parent_id' => null]);


        $students = $data['student_id'];
        foreach($students as $student){
            if($student != '') {
                $user = User::where('id', $student)->first();

                if($user != '') {
                    User::where('id', $user->id)->update([
                        'parent_id' => $id,
                    ]);
                }
            }
        }

        return redirect()->back()->with('message','You have successfully update parent.');
    }

    public function parentDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 5);
        return redirect()->route('admin.parent')->with('message','You have successfully deleted parent.');
    }

    
    public function parentProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.parent.parent_profile', ['user_details' => $user_details]);
    }
    /**
     * Show the student list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentList(Request $request)
    {
        $search = $request['search'] ?? "";
        $class_id = $request['class_id'] ?? "";
        $section_id = $request['section_id'] ?? "";

        $users = User::where(function ($query) use($search) {
            $query->where('users.name', 'LIKE', "%{$search}%")
                ->orWhere('users.email', 'LIKE', "%{$search}%");
        });

        $users->where('users.school_id', auth()->user()->school_id)
        ->where('users.role_id', 7);

        if($section_id == 'all' || $section_id != ""){
            $users->where('section_id', $section_id);
        }

        if($class_id == 'all' || $class_id != ""){
            $users->where('class_id', $class_id);
        }

        $students = $users->join('enrollments', 'users.id', '=', 'enrollments.user_id')->select('enrollments.*')->paginate(10);
        
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);

        return view('admin.student.student_list', compact('students', 'search', 'classes', 'class_id', 'section_id'));
    }

    public function createStudentModal()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.student.add_student', ['classes' => $classes]);
    }

    public function studentCreate(Request $request)
    {
        $data = $request->all();
        $code = student_code();
        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'code' => student_code(),
            'role_id' => '7',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
    if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
        Mail::to($data['email'])->send(new NewUserEmail($data));
    }
        return redirect()->back()->with('message','You have successfully add student.');
    }

    public function studentIdCardGenerate($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        return view('admin.student.id_card', ['student_details' => $student_details]);
    }
    public function studentProfile($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        return view('admin.student.student_profile', ['student_details' => $student_details]);
    }

    public function studentEditModal($id)
    {
        $user = User::find($id);
        $student_details = (new CommonController)->get_student_details_by_id($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.student.edit_student', ['user' => $user, 'student_details' => $student_details, 'classes' => $classes]);
    }

    public function studentUpdate(Request $request, $id)
    {
        $data = $request->all();
        $data['student_info'] = json_encode(array_filter($request->student_info));
         if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
            'student_info'     => $data['student_info'],
        ]);

        Enrollment::where('user_id', $id)->update([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
        ]);

        return redirect()->back()->with('message','You have successfully update student.');
    }

    public function studentDelete($id)
    {
        $enroll = Enrollment::where('user_id', $id)->first();
        $enroll->delete();

        $fee_history = StudentFeeManager::get()->where('student_id', $id);
        $fee_history->map->delete();

        $attendances = DailyAttendances::get()->where('student_id', $id);
        $attendances->map->delete();

        $book_issues = BookIssue::get()->where('student_id', $id);
        $book_issues->map->delete();

        $gradebooks = Gradebook::get()->where('student_id', $id);
        $gradebooks->map->delete();

        $payments = Payments::get()->where('user_id', $id);
        $payments->map->delete();

        $payment_history = PaymentHistory::get()->where('user_id', $id);
        $payment_history->map->delete();


        $user = User::find($id);
        $user->delete();

        $students = User::get()->where('role_id', 7);
        return redirect()->back()->with('message','Student removed successfully.');
    }


    /**
     * Show the teacher permission form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teacherPermission()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where('role_id', 3)
                ->where('school_id', auth()->user()->school_id)
                ->get();
        return view('admin.permission.index', ['classes' => $classes, 'teachers' => $teachers]);
    }

    public function teacherPermissionList($value = "")
    {
        $data = explode('-', $value);
        $class_id = $data[0];
        $section_id = $data[1];
        $teachers = User::where('role_id', 3)
                ->where('school_id', auth()->user()->school_id)
                ->get();
        return view('admin.permission.list', ['teachers' => $teachers, 'class_id' => $class_id, 'section_id' => $section_id]);
    }

    public function teacherPermissionUpdate(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $teacher_id = $data['teacher_id'];
        $column_name = $data['column_name'];
        $value = $data['value'];

         $check_row = TeacherPermission::where('class_id', $class_id)
                    ->where('section_id', $section_id)
                    ->where('teacher_id', $teacher_id)
                    ->where('school_id', auth()->user()->school_id)
                    ->get();

        if(count($check_row) > 0){

            TeacherPermission::where('class_id', $class_id)
                    ->where('section_id', $section_id)
                    ->where('teacher_id', $teacher_id)
                    ->where('school_id', auth()->user()->school_id)
                    ->update([
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'school_id' => auth()->user()->school_id,
                        'teacher_id' => $teacher_id,
                        $column_name => $data['value'],
                    ]);

            
        } else {
            TeacherPermission::create([
                'class_id' => $class_id,
                'section_id' => $section_id,
                'school_id' => auth()->user()->school_id,
                'teacher_id' => $teacher_id,
                $column_name => 1,
            ]);
            
        }
        
    }


    /**
     * Show the offline_admission form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offlineAdmissionForm($type = '')
    {   
        $data['parents'] = User::where(['role_id' => 6,'school_id' => 1])->get();
        $data['departments'] = Department::get()->where('school_id', auth()->user()->school_id);
        $data['classes'] = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.offline_admission.offline_admission', ['aria_expand' => $type, 'data' => $data]);
    }

    public function offlineAdmissionCreate(Request $request)
    {
        $package = Subscription::where('school_id', auth()->user()->school_id)->latest()->first();
        
        
        $student_limit = $package->studentLimit;
        
        $student_count = User::where(['role_id' => 7, 'school_id' => auth()->user()->school_id])->count();

        
        
        if ($student_limit == 'unlimited' || $student_limit > $student_count) {
        
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['eDefaultDateRange']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);

        $data['student_info'] = json_encode(array_filter($request->student_info));

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'code' => student_code(),
                'role_id' => '7',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'student_info'     => $data['student_info'],
                'status' => 1,
            ]);

            Enrollment::create([
                'user_id' => $user->id,
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
                Mail::to($data['email'])->send(new NewUserEmail($data));
            }
            return redirect()->back()->with('message','Admission successfully done.');

        } else {

            return redirect()->back()->with('error','Sorry this email has been taken');
        }
     } else{
        return redirect()->back()->with('error','Your students limit out.Please upgrade to add more students');
     }
    }

    public function offlineAdmissionBulkCreate(Request $request)
    {
        $data = $request->all();

        $duplication_counter = 0;
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $department_id = $data['department_id'];

        $students_name = $data['name'];
        $students_email = $data['email'];
        $students_password = $data['password'];
        $students_gender = $data['gender'];
        $students_parent = $data['parent_id'];

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        foreach($students_name as $key => $value){
            $duplicate_user_check = User::get()->where('email', $students_email[$key]);

            if(count($duplicate_user_check) == 0) {

                $info = array(
                    'gender' => $students_gender[$key],
                    'blood_group' => '',
                    'birthday' => '',
                    'phone' => '',
                    'address' => '',
                    'photo' => ''
                );
                $data['user_information'] = json_encode($info);

                $user = User::create([
                    'name' => $students_name[$key],
                    'email' => $students_email[$key],
                    'password' => Hash::make($students_password[$key]),
                    'code' => student_code(),
                    'role_id' => '7',
                    'parent_id' => $students_parent[$key],
                    'school_id' => auth()->user()->school_id,
                    'user_information' => $data['user_information'],
                    'status' => 1,
                ]);


                Enrollment::create([
                    'user_id' => $user->id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'school_id' => auth()->user()->school_id,
                    'department_id' => $department_id,
                    'session_id' => $active_session,
                ]);

            } else {
                $duplication_counter++;
            }
        }

        if ($duplication_counter > 0) {

            return redirect()->back()->with('warning','Some of the emails have been taken.');

        } else {

            return redirect()->back()->with('message','Students added successfully');
        }
    }

    public function offlineAdmissionExcelCreate(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $school_id = auth()->user()->school_id;
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $package = Subscription::where('school_id', auth()->user()->school_id)->first();
        
        $student_limit = $package->studentLimit;

        $student_count = User::where(['role_id' => 7, 'school_id' => auth()->user()->school_id])->count();


        $file = $data['csv_file'];
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
           
            // Upload file
            $file->move(public_path('assets/csv_file/'), $filename);

            // In case the uploaded file path is to be stored in the database
            $filepath = url('public/assets/csv_file/'.$filename);
        }

        if (($handle = fopen($filepath, 'r')) !== FALSE) { // Check the resource is valid
            $count = 0;
            $duplication_counter = 0;

            while (($all_data = fgetcsv($handle, 1000, ",")) !== FALSE ) { // Check opening the file is OK!
            if ($student_limit == 'unlimited' || $student_limit > $student_count) {
                if($count > 0 ){
                    
                        $duplicate_user_check = User::get()->where('email', $all_data[1]);

                    if(count($duplicate_user_check) == 0){
                        
                        $info = array(
                            'gender' => $all_data[5],
                            'blood_group' => $all_data[4],
                            'birthday' => strtotime($all_data[6]),
                            'phone' => $all_data[3],
                            'address' => $all_data[7],
                            'photo' => ''
                        );

                        $data['user_information'] = json_encode($info);

                        $user = User::create([
                            'name' => $all_data[0],
                            'email' => $all_data[1],
                            'password' => Hash::make($all_data[2]),
                            'code' => student_code(),
                            'role_id' => '7',
                            'school_id' => $school_id,
                            'user_information' => $data['user_information'],
                            'status' => 1,
                        ]);


                        Enrollment::create([
                            'user_id' => $user->id,
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'school_id' => $school_id,
                            'session_id' => $session_id,
                        ]);
                    
                    

                    }else{
                        $duplication_counter++;
                    }
                    
                    // check email duplication
               
                }
            }else{
                return redirect()->back()->with('error','Your students limit out.Please upgrade to add more students.');
            }
                $count++;
            }
        
            fclose($handle);
        }

        if ($duplication_counter > 0) {

            return redirect()->back()->with('warning','Some of the emails have been taken.');

        } else {

            return redirect()->back()->with('message','Students added successfully');
        }
    }


    /**
     * Show the exam category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function examCategoryList()
    {
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.exam_category.exam_category', ['exam_categories' => $exam_categories]);
    }

    public function createExamCategory()
    {
        return view('admin.exam_category.create');
    }

    public function examCategoryCreate(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        ExamCategory::create([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
            'timestamp' => strtotime(date('Y-m-d')),
        ]);
        return redirect()->back()->with('message','Exam category created successfully.');
    }

    public function editExamCategory($id='')
    {
        $exam_category = ExamCategory::find($id);
        return view('admin.exam_category.edit', ['exam_category' => $exam_category]);
    }

    public function examCategoryUpdate(Request $request, $id)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        ExamCategory::where('id', $id)->update([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
            'timestamp' => strtotime(date('Y-m-d')),
        ]);
        return redirect()->back()->with('message','Exam category updated successfully.');
    }

    public function examCategoryDelete($id='')
    {
        $exam_category = ExamCategory::find($id);
        $exam_category->delete();
        return redirect()->back()->with('message','You have successfully delete exam category.');
    }


    /**
     * Show the exam list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offlineExamList()
    {
        $id = "all";
        $exams = Exam::get()->where('exam_type', 'offline')->where('school_id', auth()->user()->school_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.offline_exam_list', ['exams' => $exams, 'classes' => $classes, 'id' => $id]);
    }

    public function offlineExamExport($id="")
    {
        if($id != "all") {
            $exams = Exam::where([
               'exam_type' => 'offline',
               'class_id' => $id
            ])->get();
        } else {
            $exams = Exam::get()->where('exam_type', 'offline');
        }
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.offline_exam_export', ['exams' => $exams, 'classes' => $classes]);
    }

    public function classWiseOfflineExam($id)
    {
        $exams = Exam::where([
           'exam_type' => 'offline',
           'class_id' => $id
        ])->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.exam_list', ['exams' => $exams, 'classes' => $classes, 'id' => $id]);
    }

    public function createOfflineExam()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.add_offline_exam', ['classes' => $classes,'exam_categories' => $exam_categories]);
    }

    public function classWiseSubject($id)
    {
        $subjects = Subject::get()->where('class_id', $id);
        $options = '<option value="">'.'Select a subject'.'</option>';
        foreach ($subjects as $subject):
            $options .= '<option value="'.$subject->id.'">'.$subject->name.'</option>';
        endforeach;
        echo $options;
    }

    public function offlineExamCreate(Request $request)
    {

            // Retrieve request data
        $data = $request->input('class_room_id');
        $startingTime =  strtotime($request->starting_date.''.$request->starting_time);
        $endingTime = strtotime($request->ending_date.''.$request->ending_time);


           // Check if the room is occupied for the specified time range
        $occupiedExams = Exam::where('room_number', $data)
        ->where(function ($query) use ($startingTime, $endingTime) {
            $query->whereBetween('starting_time', [$startingTime, $endingTime])
            ->orWhereBetween('ending_time', [$startingTime, $endingTime])
            ->orWhere(function ($query) use ($startingTime, $endingTime) {
              $query->where('starting_time', '<=', $startingTime)
              ->where('ending_time', '>=', $endingTime);
          });
        })
        ->get();
                // Return response based on room availability
        if (count($occupiedExams) != 0) {
            return redirect()->back()->with(['warning' => 'The room is occupied for the specified time range'], 409);
        } else {
            $data = $request->all();
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
            $exam_category = ExamCategory::find($data['exam_category_id']);
            Exam::create([
                'name' => $exam_category->name,
                'exam_category_id' => $data['exam_category_id'],
                'exam_type' => 'offline',
                'room_number' => $data['class_room_id'],
                'starting_time' => strtotime($data['starting_date'].''.$data['starting_time']),
                'ending_time' => strtotime($data['ending_date'].''.$data['ending_time']),
                'total_marks' => $data['total_marks'],
                'status' => 'pending',
                'class_id' => $data['class_id'],
                'subject_id' => $data['subject_id'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

           return redirect()->back()->with(['message' => 'You have successfully create exam'], 200);
       }

   }

   public function editOfflineExam($id){
        $exam = Exam::find($id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $subjects = Subject::get()->where('class_id', $exam->class_id);
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.edit_offline_exam', ['exam' => $exam, 'classes' => $classes, 'subjects' => $subjects, 'exam_categories' => $exam_categories]);
    }

    public function offlineExamUpdate(Request $request, $id)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $exam_category = ExamCategory::find($data['exam_category_id']);
        Exam::where('id', $id)->update([
            'name' => $exam_category->name,
            'exam_category_id' => $data['exam_category_id'],
            'exam_type' => 'offline',
            'room_number' => $data['class_room_id'],
            'starting_time' => strtotime($data['starting_date'].''.$data['starting_time']),
            'ending_time' => strtotime($data['ending_date'].''.$data['ending_time']),
            'total_marks' => $data['total_marks'],
            'status' => 'pending',
            'class_id' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully update exam.');
    }

    public function offlineExamDelete($id)
    {
        $exam = Exam::find($id);
        $exam->delete();
        $exams = Exam::get()->where('exam_type', 'offline');
        return redirect()->back()->with('message','You have successfully delete exam.');
    }

    /**
     * Show the grade daily attendance.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function dailyAttendance()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $attendance_of_students = array();
        $no_of_users = 0;
        
        return view('admin.attendance.daily_attendance', ['classes' => $classes, 'attendance_of_students' => $attendance_of_students, 'no_of_users' => $no_of_users]);
    }

    public function dailyAttendanceFilter(Request $request)
    {
        $data = $request->all();
        $date = '01 '.$data['month'].' '.$data['year'];
        $first_date = strtotime($date);
        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['attendance_date'] = strtotime($date);
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get()->toArray();

        $students_details = Enrollment::where('class_id', $page_data['class_id'])
        ->where('section_id', $page_data['section_id'])
        ->get();
        

        $no_of_users = DailyAttendances::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->distinct()->count('student_id');

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.attendance.attendance_list', ['page_data' => $page_data, 'classes' => $classes, 'attendance_of_students' => $attendance_of_students,'students_details' => $students_details, 'no_of_users' => $no_of_users]);
    }

    public function takeAttendance()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.attendance.take_attendance', ['classes' => $classes]);
    }

    public function studentListAttendance(Request $request)
    {
        $data = $request->all();

        $page_data['attendance_date'] = $data['date'];
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];

        return view('admin.attendance.student', ['page_data' => $page_data]);
    }

    public function attendanceTake(Request $request)
    {
        $att_data = $request->all();

        $students = $att_data['student_id'];
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($att_data['date']);
        $data['class_id'] = $att_data['class_id'];
        $data['section_id'] = $att_data['section_id'];
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        $check_data = DailyAttendances::where(['timestamp' => $data['timestamp'], 'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'session_id' => $active_session, 'school_id' => auth()->user()->school_id])->get();
        if(count($check_data) > 0){
            foreach($students as $key => $student):
                $data['status'] = $att_data['status-'.$student];
                $data['student_id'] = $student;
                $attendance_id = $att_data['attendance_id'];
               
               if(isset($attendance_id[$key])){
                    
                    DailyAttendances::where('id', $attendance_id[$key])->update($data);
               }else{
                    DailyAttendances::create($data);
               }
            endforeach;

        }else{
            foreach($students as $student):
                $data['status'] = $att_data['status-'.$student];
                $data['student_id'] = $student;

                DailyAttendances::create($data);

            endforeach;
        }

        return redirect()->back()->with('message','Student attendance updated successfully.');
    }

    public function dailyAttendanceFilter_csv(Request $request)
    {

        $data = $request->all();

        $store_get_data=array_keys($data);


        $data['month']= substr($store_get_data[0],0,3);
        $data['year']= substr($store_get_data[0],4,4);
        $data['role_id']=substr($store_get_data[0],9,5);

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

      
        $date = '01 ' . $data['month'] . ' ' . $data['year'];


        $first_date = strtotime($date);

        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];
        $page_data['attendance_date'] = $first_date;
        $no_of_users = 0;




       

            $no_of_users = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id,  'session_id' => $active_session])->distinct()->count('student_id');
            $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id,  'session_id' => $active_session])->get()->toArray();
       

        $csv_content ="Student"."/".get_phrase('Date');
        $number_of_days = date('m', $page_data['attendance_date']) == 2 ? (date('Y', $page_data['attendance_date']) % 4 ? 28 : (date('m', $page_data['attendance_date']) % 100 ? 29 : (date('m', $page_data['attendance_date']) % 400 ? 28 : 29))) : ((date('m', $page_data['attendance_date']) - 1) % 7 % 2 ? 30 : 31);
        for ($i = 1; $i <= $number_of_days; $i++)
        {
            $csv_content .=','.get_phrase($i);

        }


        $file = "Attendence_report.csv";


        $student_id_count = 0;


            foreach(array_slice($attendance_of_students, 0, $no_of_users) as $attendance_of_student ){
                $csv_content .= "\n";

             $user_details = (new CommonController)->get_user_by_id_from_user_table($attendance_of_student['student_id']);
           if(date('m', $page_data['attendance_date']) == date('m', $attendance_of_student['timestamp'])) {



             if($student_id_count != $attendance_of_student['student_id'])
             {


                 $csv_content .= $user_details['name'] . ',';


             for ($i = 1; $i <= $number_of_days; $i++)
             {
             $page_data['date'] = $i.' '.$page_data['month'].' '.$page_data['year'];
             $timestamp = strtotime($page_data['date']);

                 $attendance_by_id = DailyAttendances::where([ 'student_id' => $attendance_of_student['student_id'], 'school_id' => auth()->user()->school_id, 'timestamp' => $timestamp])->first();
                 if(isset($attendance_by_id->status) && $attendance_by_id->status == 1){
                    $csv_content .= "P,";
                 }elseif(isset($attendance_by_id->status) && $attendance_by_id->status == 0){
                    $csv_content .= "A,";
                 }
                 else
                 {
                    $csv_content .= ",";

                 }


                 if($i==$number_of_days)
                 {
                    $csv_content= substr_replace($csv_content,"", -1);
                 }

             }

            }

             $student_id_count = $attendance_of_student['student_id'];
        }
    }

        $txt = fopen($file, "w") or die("Unable to open file!");
        fwrite($txt, $csv_content);
        fclose($txt);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        header("Content-type: text/csv");
        readfile($file);
    }

    /**
     * Show the routine.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function routine()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.routine.routine', ['classes' => $classes]);
    }

    public function routineList(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.routine.routine_list', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }

    public function addRoutine()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where(['role_id' => 3, 'school_id' => auth()->user()->school_id])->get();
        $class_rooms = ClassRoom::get()->where('school_id', auth()->user()->school_id);
        return view('admin.routine.add_routine', ['classes' => $classes, 'teachers' => $teachers, 'class_rooms' => $class_rooms]);
    }

    public function routineAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Routine::create([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'room_id' => $data['class_room_id'],
            'day' => $data['day'],
            'starting_hour' => $data['starting_hour'],
            'starting_minute' => $data['starting_minute'],
            'ending_hour' => $data['ending_hour'],
            'ending_minute' => $data['ending_minute'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/routine/list?class_id='.$data['class_id'].'&section_id='.$data['section_id'])->with('message','You have successfully create a class routine.');
    }

    public function routineEditModal($id){
        $routine = Routine::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where(['role_id' => 3, 'school_id' => auth()->user()->school_id])->get();
        $class_rooms = ClassRoom::get()->where('school_id', auth()->user()->school_id);
        return view('admin.routine.edit_routine', ['routine' => $routine, 'classes' => $classes, 'teachers' => $teachers, 'class_rooms' => $class_rooms]);
    }

    public function routineUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Routine::where('id', $id)->update([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'room_id' => $data['class_room_id'],
            'day' => $data['day'],
            'starting_hour' => $data['starting_hour'],
            'starting_minute' => $data['starting_minute'],
            'ending_hour' => $data['ending_hour'],
            'ending_minute' => $data['ending_minute'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect()->back()->with('message','You have successfully update routine.');
    }

    public function routineDelete($id)
    {
        $routine = Routine::find($id);
        $routine->delete();
        return redirect()->back()->with('message','You have successfully delete routine.');
    }

    /**
     * Show the syllabus.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function syllabus()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.syllabus.syllabus', ['classes' => $classes]);
    }


    public function syllabusList(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.syllabus.syllabus_list', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }

    public function addSyllabus()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.syllabus.add_syllabus', ['classes' => $classes]);
    }

    public function syllabusAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $file = $data['syllabus_file'];

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            $file->move(public_path('assets/uploads/syllabus/'), $filename);

            $filepath = asset('assets/uploads/syllabus/'.$filename);
        }

        Syllabus::create([
            'title' => $data['title'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'file' => $filename,
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/syllabus/list?class_id='.$data['class_id'].'&section_id='.$data['section_id'])->with('message','You have successfully create a syllabus.');
    }

    public function syllabusEditModal($id){
        $syllabus = Syllabus::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.syllabus.edit_syllabus', ['syllabus' => $syllabus, 'classes' => $classes]);
    }

    public function syllabusUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $file = $data['syllabus_file'];

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            $file->move(public_path('assets/uploads/syllabus/'), $filename);

            $filepath = asset('assets/uploads/syllabus/'.$filename);
        }

        Syllabus::where('id', $id)->update([
            'title' => $data['title'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'file' => $filename,
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/syllabus/list?class_id='.$data['class_id'].'&section_id='.$data['section_id'])->with('message','You have successfully update a syllabus.');
    }

    public function syllabusDelete($id)
    {
        $syllabus = Syllabus::find($id);
        $syllabus->delete();
        return redirect()->back()->with('message','You have successfully delete syllabus.');
    }

    /**
     * Show the gradebook.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gradebook(Request $request)
    {

        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $exam_categories = ExamCategory::get()->where('school_id', auth()->user()->school_id);

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(count($request->all()) > 0){

            $data = $request->all();

            $filter_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

            $class_id = $data['class_id'];
            $section_id = $data['section_id'];
            $exam_category_id = $data['exam_category_id'];
            $subjects = Subject::where(['class_id' => $class_id, 'school_id' => auth()->user()->school_id])->get();

        } else {
            $filter_list = [];

            $class_id = '';
            $section_id = '';
            $exam_category_id = '';
            $subjects = '';
        }

        return view('admin.gradebook.gradebook', ['filter_list' => $filter_list, 'class_id' => $class_id, 'section_id' => $section_id, 'exam_category_id' => $exam_category_id, 'classes' => $classes, 'exam_categories' => $exam_categories, 'subjects' => $subjects]);
    }

    public function gradebookList(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $exam_wise_student_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();
        echo view('admin.gradebook.list', ['exam_wise_student_list' => $exam_wise_student_list, 'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session]);
    }

    public function subjectWiseMarks(Request $request, $student_id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $subject_wise_mark_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'student_id' => $student_id, 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->first();

        echo view('admin.gradebook.subject_marks', ['subject_wise_mark_list' => $subject_wise_mark_list]);
    }

    public function addMark()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $exam_categories = ExamCategory::get()->where('school_id', auth()->user()->school_id);
        return view('admin.gradebook.add_mark', ['classes' => $classes, 'exam_categories' => $exam_categories]);
    }

    public function markAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $subject_wise_mark_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'student_id' => $data['student_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

        $result = $subject_wise_mark_list->count();

        if($result > 0){

            return redirect()->back()->with('message','Mark added successfully.');

        } else {

            $mark = array($data['subject_id'] => $data['mark']);

            $marks = json_encode($mark);

            $data['marks'] = $marks;
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;
            $data['timestamp'] = strtotime(date('Y-m-d'));

            Gradebook::create($data);

            return redirect()->back()->with('message','Mark added successfully.');
        }
    }

    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function marks($value='')
    {
        $page_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();
        $page_data['sessions'] = Session::where('school_id', auth()->user()->school_id)->get();

        return view('admin.marks.index', $page_data);
    }

    public function marksFilter(Request $request)
    {
        $data = $request->all();

        $page_data['exam_category_id'] = $data['exam_category_id'];
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['subject_id'] = $data['subject_id'];
        $page_data['session_id'] = $data['session_id'];

        $page_data['class_name'] = Classes::find($data['class_id'])->name;
        $page_data['section_name'] = Section::find($data['section_id'])->name;
        $page_data['subject_name'] = Subject::find($data['subject_id'])->name;
        $page_data['session_title'] = Session::find($data['session_id'])->session_title;


        $enroll_students = Enrollment::where('class_id', $page_data['class_id'])
        ->where('section_id', $page_data['section_id'])
        ->get();

        $page_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();

        $exam = Exam::where('exam_type', 'offline')
        ->where('class_id', $data['class_id'])
        ->where('subject_id', $data['subject_id'])
        ->where('session_id', $data['session_id'])
        ->where('exam_category_id', $data['exam_category_id'])
        ->where('school_id', auth()->user()->school_id)
        ->first();

        if ($exam) {
            $response = view('admin.marks.marks_list', ['enroll_students' => $enroll_students, 'page_data' => $page_data])->render();
            return response()->json(['status' => 'success', 'html' => $response]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No records found for the specified filter. First create exam for the selected filter.']);
        }
    }
    
    public function marksPdf($section_id = "", $class_id ="", $session_id ="", $exam_category_id="", $subject_id ="")
    {
       
        $enroll_students = Enrollment::where('class_id', $class_id)
        ->where('section_id', $section_id)
        ->get();

        $data = [
            'enroll_students' => $enroll_students,
            'section_id' => $section_id,
            'class_id' => $class_id,
            'session_id' => $session_id,
            'exam_category_id' => $exam_category_id,
            'subject_id' => $subject_id
        ];

        $pdf = PDF::loadView('admin.marks.markPdf', $data);

        return $pdf->download('webappfix.pdf');

        // return $pdf->stream('webappfix.pdf');
    }

    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gradeList()
    {
        $grades = Grade::get()->where('school_id', auth()->user()->school_id);
        return view('admin.grade.grade_list', ['grades' => $grades]);
    }

    public function createGrade()
    {
        return view('admin.grade.add_grade');
    }

    public function gradeCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_grade_check = Grade::get()->where('name', $data['grade'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_grade_check) == 0) {
            Grade::create([
                'name' => $data['grade'],
                'grade_point' => $data['grade_point'],
                'mark_from' => $data['mark_from'],
                'mark_upto' => $data['mark_upto'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new grade.');

        } else {
            return back()
            ->with('error','Sorry this grade already exists');
        }
    }

    public function editGrade($id)
    {
        $grade = Grade::find($id);
        return view('admin.grade.edit_grade', ['grade' => $grade]);
    }

    public function gradeUpdate(Request $request, $id)
    {
        $data = $request->all();
        Grade::where('id', $id)->update([
            'name' => $data['grade'],
            'grade_point' => $data['grade_point'],
            'mark_from' => $data['mark_from'],
            'mark_upto' => $data['mark_upto'],
            'school_id' => auth()->user()->school_id,
        ]);
        
        return redirect()->back()->with('message','You have successfully update grade.');
    }

    public function gradeDelete($id)
    {
        $grade = Grade::find($id);
        $grade->delete();
        $grades = Grade::get()->where('school_id', auth()->user()->school_id);
        return redirect()->back()->with('message','You have successfully delete grade.');
    }

    /**
     * Show the promotion list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function promotionFilter()
    {
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.promotion.promotion', ['sessions' => $sessions, 'classes' => $classes]);
    }

    public function promotionList(Request $request)
    {
        $data = $request->all();
        $promotion_list = Enrollment::where(['session_id' => $data['session_id_from'], 'class_id' => $data['class_id_from'], 'section_id' => $data['section_id_from']])->get();
        echo view('admin.promotion.promotion_list', ['promotion_list' => $promotion_list, 'class_id_to' => $data['class_id_to'], 'section_id_to' => $data['section_id_to'], 'session_id_to' => $data['session_id_to'], 'class_id_from' => $data['class_id_from'], 'section_id_from' => $data['section_id_from']]);
    }

    public function promote($promotion_data = '')
    {
        $promotion_data = explode('-', $promotion_data);
        $enroll_id = $promotion_data[0];
        $class_id = $promotion_data[1];
        $section_id = $promotion_data[2];
        $session_id = $promotion_data[3];

        $enroll = Enrollment::find($enroll_id);

        Enrollment::where('id', $enroll_id)->update([
            'class_id' => $class_id,
            'section_id' => $section_id,
            'session_id' => $session_id,
        ]);

        return true;
    }

    public function classWiseSections($id)
    {
        $sections = Section::get()->where('class_id', $id);
        $options = '<option value="">'.'Select a section'.'</option>';
        foreach ($sections as $section):
            $options .= '<option value="'.$section->id.'">'.$section->name.'</option>';
        endforeach;
        echo $options;
    }

    /**
     * Show the subject list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function subjectList(Request $request)
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if(count($request->all()) > 0 && $request->class_id != ''){

            $data = $request->all();
            $class_id = $data['class_id'] ?? '';
            $subjects = Subject::where('class_id', $class_id)->paginate(10);

        } else {
            $subjects = Subject::where('school_id', auth()->user()->school_id)->paginate(10);

            $class_id = '';
        }

        return view('admin.subject.subject_list', compact('subjects', 'classes', 'class_id'));
    }

    public function createSubject()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.subject.add_subject', ['classes' => $classes]);
    }

    public function subjectCreate(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Subject::create([
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/subject?class_id='.$data['class_id'])->with('message','You have successfully create subject.');
    }

    public function editSubject($id)
    {
        $subject = Subject::find($id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.subject.edit_subject', ['subject' => $subject, 'classes' => $classes]);
    }

    public function subjectUpdate(Request $request, $id)
    {
        $data = $request->all();
        Subject::where('id', $id)->update([
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'school_id' => auth()->user()->school_id,
        ]);
        
        return redirect('/admin/subject?class_id='.$data['class_id'])->with('message','You have successfully update subject.');
    }

    public function subjectDelete($id)
    {
        $subject = Subject::find($id);
        $subject->delete();
        $subjects = Subject::get()->where('school_id', auth()->user()->school_id);
        return redirect()->back()->with('message','You have successfully delete subject.');
    }

    /**
     * Show the department list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function departmentList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $departments = Department::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->paginate(10);

        } else {
            $departments = Department::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.department.department_list', compact('departments', 'search'));
    }

    public function createDepartment()
    {
        return view('admin.department.add_department');
    }

    public function departmentCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_department_check = Department::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_department_check) == 0) {
            Department::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new department.');

        } else {
            return back()
            ->with('error','Sorry this department already exists');
        }
    }

    public function editDepartment($id)
    {
        $department = Department::find($id);
        return view('admin.department.edit_department', ['department' => $department]);
    }

    public function departmentUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_department_check = Department::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_department_check) == 0) {
            Department::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);
            
            return redirect()->back()->with('message','You have successfully update subject.');
        } else {
            return back()
            ->with('error','Sorry this department already exists');
        }
    }

    public function departmentDelete($id)
    {
        $department = Department::find($id);
        $department->delete();
        return redirect()->back()->with('message','You have successfully delete department.');
    }


    /**
     * Show the class room list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function classRoomList()
    {
        $class_rooms = ClassRoom::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.class_room.class_room_list', compact('class_rooms'));
    }

    public function createClassRoom()
    {
        return view('admin.class_room.add_class_room');
    }

    public function classRoomCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_class_room_check = ClassRoom::get()->where('name', $data['name']);

        if(count($duplicate_class_room_check) == 0) {
            ClassRoom::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new class room.');

        } else {
            return back()
            ->with('error','Sorry this class room already exists');
        }
    }

    public function editClassRoom($id)
    {
        $class_room = ClassRoom::find($id);
        return view('admin.class_room.edit_class_room', ['class_room' => $class_room]);
    }

    public function classRoomUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_class_room_check = ClassRoom::get()->where('name', $data['name']);

        if(count($duplicate_class_room_check) == 0) {
            ClassRoom::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);
            
            return redirect()->back()->with('message','You have successfully update class room.');
        } else {
            return back()
            ->with('error','Sorry this class room already exists');
        }
    }

    public function classRoomDelete($id)
    {
        $department = ClassRoom::find($id);
        $department->delete();
        return redirect()->back()->with('message','You have successfully delete class room.');
    }

    /**
     * Show the class list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function classList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $class_lists = Classes::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->paginate(10);

        } else {
            $class_lists = Classes::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.class.class_list', compact('class_lists', 'search'));
    }

    public function createClass()
    {
        return view('admin.class.add_class');
    }

    public function classCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_class_check = Classes::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_class_check) == 0) {
            $id = Classes::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ])->id;

            Section::create([
                'name' => 'A',
                'class_id' => $id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new class.');

        } else {
            return back()
            ->with('error','Sorry this class already exists');
        }
    }

    public function editClass($id)
    {
        $class = Classes::find($id);
        return view('admin.class.edit_class', ['class' => $class]);
    }

    public function classUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_class_check = Classes::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_class_check) == 0) {
            Classes::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);
            
            return redirect()->back()->with('message','You have successfully update class.');
        } else {
            return back()
            ->with('error','Sorry this class already exists');
        }
    }

    public function editSection($id)
    {
        $sections = Section::get()->where('class_id', $id);
        return view('admin.class.sections', ['class_id' => $id, 'sections' => $sections]);
    }

    public function sectionUpdate(Request $request, $id)
    {
        $data = $request->all();

        $section_id = $data['section_id'];
        $section_name = $data['name'];

        foreach($section_id as $key => $value){
            if($value == 0){
                Section::create([
                    'name' => $section_name[$key],
                    'class_id' => $id,
                ]);
            }
            if($value != 0 && is_numeric($value)){
                Section::where(['id' => $value, 'class_id' => $id])->update([
                    'name' => $section_name[$key],
                ]);
            }

            $section_value = null;
            if (strpos($value, 'delete') == true) {
                $section_value = str_replace('delete', '', $value);

                $section = Section::find(['id' => $section_value, 'class_id' => $id]);
                $section->map->delete();
            }
        }

        return redirect()->back()->with('message','You have successfully update sections.');
    }

    public function classDelete($id)
    {
        $class = Classes::find($id);
        $class->delete();
        $sections = Section::get()->where('class_id', $id);
        $sections->map->delete();
        $subjects = Subject::get()->where('class_id', $id);
        $subjects->map->delete();
        return redirect()->back()->with('message','You have successfully delete class.');
    }

    /**
     * Show the student fee manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentFeeManagerList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(count($request->all()) > 0){
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = $data['status'];

            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_status != "all"){
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('admin.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);

         } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('admin.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
         }
    }

    public function feeManagerExport($date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($selected_class != "all" && $selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_class != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_status != "all"){
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        }

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();



        $file = "student_fee-".date('d-m-Y', $date_from).'-'.date('d-m-Y', $date_to).'-'.$selected_class.'-'.$selected_status.".csv";

        $csv_content = get_phrase('Invoice No') . ', ' . get_phrase('Student') . ', ' . get_phrase('Class') . ', ' . get_phrase('Invoice Title') . ', ' . get_phrase('Total Amount') . ', ' . get_phrase('Created At') . ', ' . get_phrase('Paid Amount') . ', ' . get_phrase('Status');

        foreach ($invoices as $invoice) {
            $csv_content .= "\n";

            $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']);
            $invoice_no = sprintf('%08d', $invoice['id']);

            $csv_content .= $invoice_no . ', ' . $student_details['name'] . ', ' . $student_details['class_name'] . ', ' . $invoice['title'] . ', ' . currency($invoice['total_amount']) . ', ' . date('d-M-Y', $invoice['timestamp']) . ', ' . currency($invoice['paid_amount']) . ', ' . $invoice['status'];
        }
        $txt = fopen($file, "w") or die("Unable to open file!");
        fwrite($txt, $csv_content);
        fclose($txt);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        header("Content-type: text/csv");
        readfile($file);

    }


    public function feeManagerExportPdfPrint($date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($selected_class != "all" && $selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_class != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_status != "all"){
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        }


        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.student_fee_manager.pdf_print', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);


    }

    public function createFeeManager($value="")
    {

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if($value == 'single'){
            return view('admin.student_fee_manager.single', ['classes' => $classes]);
        } else if($value == 'mass'){
            return view('admin.student_fee_manager.mass', ['classes' => $classes]);
        }
    }

    public function feeManagerCreate(Request $request, $value="")
    {
        $data = $request->all();

        if($value == 'single'){
            $total_amount = $data['amount'] - $data['discounted_price'];

            $data['total_amount'] = $total_amount;

            if ($data['paid_amount'] > $data['total_amount']) {

                return back()->with('error','Paid amount can not get bigger than total amount');

            }
            if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {

               return back()->with('error','Paid amount is not equal to total amount');
            }


            $parent_id=User::find($data['student_id'])->toArray();
            $parent_id=$parent_id['parent_id'];
            $data['parent_id'] = $parent_id;

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['timestamp'] = strtotime(date('d-M-Y'));
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;


            StudentFeeManager::create($data);

            return redirect()->back()->with('message','You have successfully create a new invoice.');
        } else if($value == 'mass'){

            if ($data['paid_amount'] > $data['total_amount']) {

                return back()->with('error','Paid amount can not get bigger than total amount');

            }
            if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {

               return back()->with('error','Paid amount is not equal to total amount');
            }

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['timestamp'] = strtotime(date('d-M-Y'));
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;

            $enrolments = Enrollment::where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->get();



            foreach ($enrolments as $enrolment) {


                $data['student_id'] = $enrolment['user_id'];
                $parent_id=User::find($data['student_id'])->toArray();
                $parent_id=$parent_id['parent_id'];
                $data['parent_id'] = $parent_id;
                StudentFeeManager::create($data);
            }

            if (sizeof($enrolments) > 0) {

                return redirect()->back()->with('message','Invoice added successfully');

            }else{

                return back()->with('error','No student found');
            }
        }
    }

    public function classWiseStudents($id='')
    {
        $enrollments = Enrollment::get()->where('class_id', $id);
        $options = '<option value="">'.'Select a student'.'</option>';
        foreach ($enrollments as $enrollment):
            $student = User::find($enrollment->user_id);
            $options .= '<option value="'.$student->id.'">'.$student->name.'</option>';
        endforeach;
        echo $options;
    }

    public function classWiseStudentsInvoice($id='')
    {
        $enrollments = Enrollment::get()->where('section_id', $id);
        $options = '<option value="">'.'Select a student'.'</option>';
        foreach ($enrollments as $enrollment):
            $student = User::find($enrollment->user_id);
            $options .= '<option value="'.$student->id.'">'.$student->name.'</option>';
        endforeach;
        echo $options;
    }

    public function editFeeManager($id='')
    {
        $invoice_details = StudentFeeManager::find($id);
        $enrollments = Enrollment::get()->where('class_id', $invoice_details->class_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.student_fee_manager.edit', ['invoice_details' => $invoice_details, 'classes' => $classes, 'enrollments' => $enrollments]);
    }

    public function feeManagerUpdate(Request $request, $id='')
    {
        $data = $request->all();

        /*GET THE PREVIOUS INVOICE DETAILS FOR GETTING THE PAID AMOUNT*/
        $previous_invoice_data = StudentFeeManager::find($id);

        $total_amount = $data['amount'] - $data['discounted_price'];

        $data['total_amount'] = $total_amount;

        if ($data['paid_amount'] > $data['total_amount']) {

            return redirect()->back()->with('error','Paid amount can not get bigger than total amount');
        }
        if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {
            return redirect()->back()->with('error','Paid amount is not equal to total amount');
        }

        /*KEEPING TRACK OF PAYMENT DATE*/
        if ($data['paid_amount'] != $previous_invoice_data['paid_amount'] && $data['paid_amount'] == $previous_invoice_data['total_amount']) {
            $timestamp = strtotime(date('d-M-Y'));
        } else {
            $timestamp = $previous_invoice_data['timestamp'];
        }

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        StudentFeeManager::where('id', $id)->update([
            'title' => $data['title'],
            'total_amount' => $total_amount,
            'amount' => $data['amount'],
            'discounted_price' => $data['discounted_price'],
            'class_id' => $data['class_id'],
            'student_id' => $data['student_id'],
            'paid_amount' => $data['paid_amount'],
            'payment_method' => $data['payment_method'],
            'timestamp' => $timestamp,
            'status' => $data['status'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully update invoice.');
    }

    public function studentFeeDelete($id)
    {
        $invoice = StudentFeeManager::find($id);
        $invoice->delete();
        return redirect()->back()->with('message','You have successfully delete invoice.');
    }

    /**
     * Show the expense expense list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        if(count($request->all()) > 0){
            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $expense_category_id = $data['expense_category_id'];

            $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
            $selected_category = ExpenseCategory::find($expense_category_id);
            if($expense_category_id != 'all'){
                $expenses = Expense::where('expense_category_id', $expense_category_id)
                                ->where('date', '>=', $date_from)
                                ->where('date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();
            } else {
                $expenses = Expense::where('date', '>=', $date_from)
                                ->where('date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();
            }

            return view('admin.expenses.expense_manager', ['expense_categories' => $expense_categories, 'expenses' => $expenses, 'selected_category' => $selected_category, 'date_from' => $date_from, 'date_to' => $date_to]);

        } else {
            $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
            $selected_category = "";
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $expenses = Expense::where('date', '>=', $date_from)
                                ->where('date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();
            return view('admin.expenses.expense_manager', ['expense_categories' => $expense_categories, 'expenses' => $expenses, 'selected_category' => $selected_category, 'date_from' => $date_from, 'date_to' => $date_to]);
        }
    }

    public function createExpense()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.expenses.create', ['expense_categories' => $expense_categories]);
    }

    public function expenseCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::create([
            'expense_category_id' => $data['expense_category_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully create a new expense.');
    }

    public function editExpense($id)
    {
        $expense_details = Expense::find($id);
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.expenses.edit', ['expense_categories' => $expense_categories, 'expense_details' => $expense_details]);
    }

    public function expenseUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::where('id', $id)->update([
            'expense_category_id' => $data['expense_category_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully update expense.');
    }

    public function expenseDelete($id)
    {
        $expense = Expense::find($id);
        $expense->delete();
        return redirect()->back()->with('message','You have successfully delete expense.');
    }


    /**
     * Show the expense category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseCategoryList()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.expense_category.expense_category_list', compact('expense_categories'));
    }

    public function createExpenseCategory()
    {
        return view('admin.expense_category.create');
    }

    public function expenseCategoryCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_category_check = ExpenseCategory::get()->where('name', $data['name']);

        if(count($duplicate_category_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            ExpenseCategory::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with('message','You have successfully create a new expense category.');

        } else {
            return back()
            ->with('error','Sorry this expense category already exists');
        }
    }

    public function editExpenseCategory($id)
    {
        $expense_category = ExpenseCategory::find($id);
        return view('admin.expense_category.edit', ['expense_category' => $expense_category]);
    }

    public function expenseCategoryUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_category_check = ExpenseCategory::get()->where('name', $data['name']);

        if(count($duplicate_category_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            ExpenseCategory::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with('message','You have successfully update expense category.');

        } else {
            return back()
            ->with('error','Sorry this expense category already exists');
        }
    }

    public function expenseCategoryDelete($id)
    {
        $expense_category = ExpenseCategory::find($id);
        $expense_category->delete();
        return redirect()->back()->with('message','You have successfully delete expense category.');
    }

    /**
     * Show the book list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $books = Book::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->orWhere(function ($query) use($search) {
                    $query->where('author', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->paginate(10);

        } else {
            $books = Book::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.book.list', compact('books', 'search'));
    }

    public function createBook()
    {
        return view('admin.book.create');
    }

    public function bookCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_book_check = Book::get()->where('name', $data['name']);

        if(count($duplicate_book_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;
            $data['timestamp'] = strtotime(date('d-M-Y'));

            Book::create($data);

            return redirect()->back()->with('message','You have successfully create a book.');

        } else {
            return back()
            ->with('error','Sorry this book already exists');
        }
    }

    public function editBook($id="")
    {
        $book_details = Book::find($id);
        return view('admin.book.edit', ['book_details' => $book_details]);
    }

    public function bookUpdate(Request $request, $id='')
    {
        $data = $request->all();

        $duplicate_book_check = Book::get()->where('name', $data['name']);

        if(count($duplicate_book_check) == 0) {
            Book::where('id', $id)->update([
                'name' => $data['name'],
                'author' => $data['author'],
                'copies' => $data['copies'],
                'timestamp' => strtotime(date('d-M-Y')),
            ]);
            
            return redirect()->back()->with('message','You have successfully update book.');
        } else {
            return back()
            ->with('error','Sorry this book already exists');
        }
    }

    public function bookDelete($id)
    {
        $book = Book::find($id);
        $book->delete();
        return redirect()->back()->with('message','You have successfully delete book.');
    }


    /**
     * Show the book list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookIssueList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(count($request->all()) > 0) {

            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                                    ->where('issue_date', '<=', $date_to)
                                    ->where('school_id', auth()->user()->school_id)
                                    ->where('session_id', $active_session)
                                    ->get();

            return view('admin.book_issue.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);
        } else {
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                                ->where('issue_date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();

            return view('admin.book_issue.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);

        }
    }

    public function createBookIssue()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $books = Book::get()->where('school_id', auth()->user()->school_id);
        return view('admin.book_issue.create', ['classes' => $classes, 'books' => $books]);
    }

    public function bookIssueCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 0;
        $data['issue_date'] = strtotime($data['issue_date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['timestamp'] = strtotime(date('d-M-Y'));

        BookIssue::create($data);

        return redirect()->back()->with('message','You have successfully issued a book.');
    }

    public function editBookIssue($id="")
    {
        $book_issue_details = BookIssue::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $books = Book::get()->where('school_id', auth()->user()->school_id);
        return view('admin.book_issue.edit', ['book_issue_details' => $book_issue_details, 'classes' => $classes, 'books' => $books]);
    }

    public function bookIssueUpdate(Request $request, $id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['issue_date'] = strtotime($data['issue_date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['timestamp'] = strtotime(date('d-M-Y'));

        unset($data['_token']);

        BookIssue::where('id', $id)->update($data);

        return redirect()->back()->with('message','Updated successfully.');
    }

    public function bookIssueReturn($id)
    {
        BookIssue::where('id', $id)->update([
            'status' => 1,
            'timestamp' => strtotime(date('d-M-Y')),
        ]);

        return redirect()->back()->with('message','Return successfully.');
    }

    public function bookIssueDelete($id)
    {
        $book_issue = BookIssue::find($id);
        $book_issue->delete();
        return redirect()->back()->with('message','You have successfully delete a issued book.');
    }


    /**
     * Show the noticeboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function noticeboardList()
    {
        $notices = Noticeboard::get()->where('school_id', auth()->user()->school_id);

        $events = array();

        foreach($notices as $notice) {
            if($notice['end_date'] !=""){
                if($notice['start_date'] != $notice['end_date']){
                    $end_date = strtotime($notice['end_date']) + 24*60*60;
                    $end_date = date('Y-m-d', $end_date);
                } else {
                    $end_date = date('Y-m-d', strtotime($notice['end_date']));
                }
            }

            if($notice['end_date'] =="" && $notice['start_time'] =="" && $notice['end_time'] ==""){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            } else if($notice['start_time'] !="" && ($notice['end_date'] =="" && $notice['end_time'] =="")){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])).'T'.$notice['start_time']
                );
            } else if($notice['end_date'] !="" && ($notice['start_time'] =="" && $notice['end_time'] =="")){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])),
                    'end' => $end_date
                );
            } else if($notice['end_date'] !="" && $notice['start_time'] !="" && $notice['end_time'] !=""){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])).'T'.$notice['start_time'],
                    'end' => date('Y-m-d', strtotime($notice['end_date'])).'T'.$notice['end_time']
                );
            } else {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            }
            array_push($events, $info);
        }

        $events = json_encode($events);

        return view('admin.noticeboard.noticeboard', ['events' => $events]);
    }

    public function createNoticeboard()
    {
        return view('admin.noticeboard.create');
    }

    public function noticeboardCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 1;
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        if(!empty($data['image'])){

            $imageName = time().'.'.$data['image']->extension();

            $data['image']->move(public_path('assets/uploads/noticeboard/'), $imageName);

            $data['image']  = $imageName;
        }

        Noticeboard::create($data);

        return redirect()->back()->with('message','You have successfully create a notice.');
    }

    public function editNoticeboard($id="")
    {
        $notice = Noticeboard::find($id);
        return view('admin.noticeboard.edit', ['notice' => $notice]);
    }

    public function noticeboardUpdate(Request $request, $id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 1;
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        if(!empty($data['image'])){

            $imageName = time().'.'.$data['image']->extension();

            $data['image']->move(public_path('assets/uploads/noticeboard/'), $imageName);

            $data['image']  = $imageName;
        }

        unset($data['_token']);

        Noticeboard::where('id', $id)->update($data);

        return redirect()->back()->with('message','Updated successfully.');
    }

    public function noticeboardDelete($id='')
    {
        $notice = Noticeboard::find($id);
        $notice->delete();
        return redirect()->back()->with('message','You have successfully delete a notice.');
    }


    /**
     * Show the subscription.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function subscription(Request $request)
    {

        $if_pending_payment=PaymentHistory::where('user_id',auth()->user()->id)->where('status','pending')->get()->count();

        if(count($request->all()) > 0){
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $subscriptions = Subscription::where('school_id', auth()->user()->school_id)
                ->where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        } else{
            $date_from = strtotime('first day of january this year');
            $date_to = strtotime('last day of december this year');
            $subscriptions = Subscription::where('school_id', auth()->user()->school_id)
                ->where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        }

        $subscription_details = Subscription::where(['school_id' => auth()->user()->school_id, 'active' => '1']);
        if($subscription_details->get()->count() > 0){
            $package_details = Package::find($subscription_details->first()->package_id);
        } else {
            $subscription_details = Subscription::where(['school_id' => auth()->user()->school_id, 'status' => '0']);
            if($subscription_details->get()->count() > 0){
                $package_details = Package::find($subscription_details->first()->package_id);
            } else {
                $package_details ='';
            }
        }
        return view('admin.subscription.subscription', ['if_pending_payment'=>$if_pending_payment,'subscriptions' => $subscriptions, 'subscription_details' => $subscription_details, 'package_details' => $package_details, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function subscriptionPurchase()
    {   
        $packages = Package::where('status', 1)->get();
        return view('admin.subscription.purchase', ['packages' => $packages]);
        
    }

    public function upgreadeSubscription()
    {   
        $packages = Package::where('status', 1)->get();
        return view('admin.subscription.upgrade_subscription', ['packages' => $packages]);
        
    }

    /**
     * Show the event list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function eventList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $events = FrontendEvent::where(function ($query) use($search) {
                    $query->where('title', 'LIKE', "%{$search}%");
                })->paginate(10);

        } else {
            $events = FrontendEvent::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.events.events', compact('events', 'search'));
    }

    public function createEvent()
    {
        return view('admin.events.create_event');
    }

    public function eventCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($data['timestamp']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['created_by'] = auth()->user()->id;

        FrontendEvent::create($data);

        return redirect()->back()->with('message','You have successfully create a event.');
    }

    public function editEvent($id="")
    {
        $event = FrontendEvent::find($id);
        return view('admin.events.edit_event', ['event' => $event]);
    }

    public function eventUpdate(Request $request, $id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($data['timestamp']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['created_by'] = auth()->user()->id;

        unset($data['_token']);

        FrontendEvent::where('id', $id)->update($data);

        return redirect()->back()->with('message','Updated successfully.');
    }

    public function eventDelete($id)
    {
        $event = FrontendEvent::find($id);
        $event->delete();
        return redirect()->back()->with('message','You have successfully delete a event.');
    }

    // Complain List
    function complainList(){
        return view('admin.complain.complainList');
    }
    



    public function schoolSettings()
    {
        $school_details = School::find(auth()->user()->school_id);
        return view('admin.settings.school_settings', ['school_details' => $school_details]);
    }

    public function schoolUpdate(Request $request)
    {

        $data = $request->all();
        
        unset($data['_token']);

        $school_data = School::where('id', auth()->user()->school_id)->first();
        if($request->school_logoo){
                    
            $old_image = $school_data->school_logo;
            
            $ext = $request->school_logoo->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->school_logoo->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->school_logo =$newFileName;
            $school_data->save();
         }   

        if($request->email_logo){
                    
            $old_image = $school_data->email_logo;
            
            $ext = $request->email_logo->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->email_logo->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->email_logo =$newFileName;
            $school_data->save();
         }   
         if($request->socialLogo1){
                    
            $old_image = $school_data->socialLogo1;
            
            $ext = $request->socialLogo1->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->socialLogo1->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->socialLogo1 =$newFileName;
            $school_data->save();
         }   
         if($request->socialLogo2){
                    
            $old_image = $school_data->socialLogo2;
            
            $ext = $request->socialLogo2->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->socialLogo2->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->socialLogo2 =$newFileName;
            $school_data->save();
         }   
         if($request->socialLogo3){
                    
            $old_image = $school_data->socialLogo3;
            
            $ext = $request->socialLogo3->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->socialLogo3->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->socialLogo3 =$newFileName;
            $school_data->save();
         }   

        School::where('id', auth()->user()->school_id)->update([
            'title' => $data['school_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'email_title' => $data['email_title'],
            'email_details' => $data['email_details'],
            'warning_text' => $data['warning_text'],
            'socialLink1' => $data['socialLink1'],
            'socialLink2' => $data['socialLink2'],
            'socialLink3' => $data['socialLink3'],
            
        ]);


        return redirect()->back()->with('message','School details updated successfully.');
    }







    public function studentFeeinvoice($id)
    {
        $invoice_details=StudentFeeManager::find($id)->toArray();
        $student_details = (new CommonController)->get_student_details_by_id($invoice_details['student_id'])->toArray();


      return view('admin.student_fee_manager.invoice',['invoice_details' => $invoice_details,'student_details' => $student_details]);
    }

    public function offline_payment_pending(Request $request )
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
         if(count($request->all()) > 0){
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = 'pending';



            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_status != "all"){
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('admin.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);

         } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status','pending')->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('admin.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
         }


    }

    public function update_offline_payment($id,$status)
    {
       
        $amount= StudentFeeManager::find($id)->toArray();
        $amount=$amount['total_amount'];
        
        $studentFeeManager = StudentFeeManager::find($id);
        $students_id = User::find($studentFeeManager->student_id);
        $student_email = $students_id->email;
        $parents_id = User::find($studentFeeManager->parent_id);
        
        if (!empty($parents_id)) {
          $parents_email = $parents_id->email;
        }

        if($status=='approve')
        {
            $studentsemail = StudentFeeManager::where('id', $id)->update([
                'status' => 'paid',
                'updated_at'=>date("Y-m-d H:i:s"),
                'paid_amount' =>$amount,
                'payment_method' => 'offline']);

            if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
                if (!empty($parents_id)) {
                    Mail::to($student_email)->send(new StudentsEmail($studentFeeManager));   
                    Mail::to($parents_email)->send(new StudentsEmail($studentFeeManager)); 
                }else{
                    Mail::to($student_email)->send(new StudentsEmail($studentFeeManager));
                }
            }
                  
                return redirect()->back()->with('message','Payment Approved');
        }
        elseif($status=='decline')
        {
            StudentFeeManager::where('id',$id)->update([
                'status' => 'unpaid',
                'updated_at'=>date("Y-m-d H:i:s"),
                'paid_amount' =>$amount,
                'payment_method' => 'offline']);

                return redirect()->back()->with('message','Payment Decline');


        }


    }

    public function paymentSettings()
    {

        $payment_gateways = PaymentMethods::where('school_id', auth()->user()->school_id)->get();

        $school_currency=School::where('id', auth()->user()->school_id)->first()->toArray();
        $currencies=Currency::all()->toArray();
        $paypal="";
        $paypal_keys="";
        $stripe="";
        $stripe_keys="";
        $razorpay="";
        $razorpay_keys="";
        $paytm="";
        $paytm_keys="";
        $flutterwave="";
        $flutterwave_keys="";
        $paystack = "";
        $paystack_keys = "";
        
        foreach ($payment_gateways as  $single_gateway) {
           
            if($single_gateway->name=="paypal")
            {
                
                $paypal=$single_gateway->toArray();
                $paypal_keys=json_decode($paypal['payment_keys']);
            }
            elseif($single_gateway->name=="stripe")
            {
                $stripe=$single_gateway->toArray();
                $stripe_keys=json_decode($stripe['payment_keys']);
            }
            elseif($single_gateway->name=="razorpay")
            {
                $razorpay=$single_gateway->toArray();
                $razorpay_keys=json_decode($razorpay['payment_keys']);
            }
            elseif($single_gateway->name=="paytm")
            {
                $paytm=$single_gateway->toArray();
                $paytm_keys=json_decode($paytm['payment_keys']);
            }
            elseif($single_gateway->name=="flutterwave")
            {
                $flutterwave=$single_gateway->toArray();
                $flutterwave_keys=json_decode($flutterwave['payment_keys']);
            } elseif ($single_gateway->name == "paystack") {
                $paystack = $single_gateway->toArray();
                $paystack_keys = json_decode($paystack['payment_keys']);
            }


        }

        return view('admin.payment_settings.key_settings', ['paytm' => $paytm, 'paytm_keys' => $paytm_keys, 'razorpay' => $razorpay, 'razorpay_keys' => $razorpay_keys, 'stripe' => $stripe, 'stripe_keys' => $stripe_keys, 'paypal' => $paypal, 'paypal_keys' => $paypal_keys, 'flutterwave' => $flutterwave, 'flutterwave_keys' => $flutterwave_keys, 'paystack' => $paystack, 'paystack_keys' => $paystack_keys, 'school_currency' => $school_currency, 'currencies' => $currencies]);
    }

    public function install_paystack()
    {
        $keys = array();
        $paystack = new PaymentMethods;
        $paystack['name'] = "paystack";
        $paystack['image'] = "paystack.png";
        $paystack['status'] = 1;
        $paystack['mode'] = "test";
        $keys['test_key'] = "pk_test_xxxxxxxxxxxxx";
        $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxxx";
        $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
        $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
        $paystack['payment_keys'] = json_encode($keys);
        $paystack['school_id'] = auth()->user()->school_id;
        $paystack->save();
    }


    public function paymentSettings_post(Request $request)
    {
        $data=$request->all();

        unset($data['_token']);

        $school_data = School::where('id', auth()->user()->school_id)->first();

        if($request->off_pay_ins_file || $request->off_pay_ins_text){

            if($request->off_pay_ins_file){
                        
                $old_image = $school_data->off_pay_ins_file;
                
                $ext = $request->off_pay_ins_file->getClientOriginalExtension();
                $newFileName = random(8).'.'.$ext;
                $request->off_pay_ins_file->move(public_path().'/assets/uploads/offline_payment/',$newFileName); // This will save file in a folder.  
                $school_data->off_pay_ins_file =$newFileName;
                $school_data->save();
            }   

         School::where('id', auth()->user()->school_id)->update([
            'off_pay_ins_text' => $data['off_pay_ins_text'],
            
        ]);

        return redirect()->back()->with('message','Offline payment instruction update.');

        }
        $method=$data['method'];
        $update_id=$data['update_id'];


        if($method=='currency')
        {
            $Currency = School::find($update_id);
            $Currency['school_currency']= $data['school_currency'];
            $Currency['currency_position']=$data['currency_position'];
            $Currency->save();

        }
        elseif($method=='paypal')
        {

            $keys=array();
            $paypal=PaymentMethods::find($update_id);
            $paypal['status']=$data['status'];
            $paypal['mode']=$data['mode'];
            $keys['test_client_id']=$data['test_client_id'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['live_client_id']=$data['live_client_id'];
            $keys['live_secret_key']=$data['live_secret_key'];
            $paypal['payment_keys']=json_encode($keys);
            $paypal['school_id']=auth()->user()->school_id;
            $paypal->save();


        }
        elseif($method=='stripe')
        {
            $keys=array();
            $stripe=PaymentMethods::find($update_id);
            $stripe['status']=$data['status'];
            $stripe['mode']=$data['mode'];
            $keys['test_key']=$data['test_key'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['public_live_key']=$data['public_live_key'];
            $keys['secret_live_key']=$data['secret_live_key'];
            $stripe['payment_keys']=json_encode($keys);
            $stripe['school_id']=auth()->user()->school_id;
            $stripe->save();
        }
        elseif($method=='razorpay')
        {
            $keys=array();
            $razorpay=PaymentMethods::find($update_id);
            $razorpay['status']=$data['status'];
            $razorpay['mode']=$data['mode'];
            $keys['test_key']=$data['test_key'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['live_key']=$data['live_key'];
            $keys['live_secret_key']=$data['live_secret_key'];
            $keys['theme_color']=$data['theme_color'];
            $razorpay['payment_keys']=json_encode($keys);
            $razorpay['school_id']=auth()->user()->school_id;
            $razorpay->save();


        }
        elseif($method=='paytm')
        {
            $keys=array();
            $paytm=PaymentMethods::find($update_id);
            $paytm['status']=$data['status'];
            $paytm['mode']=$data['mode'];
            $keys['test_merchant_id']=$data['test_merchant_id'];
            $keys['test_merchant_key']=$data['test_merchant_key'];
            $keys['live_merchant_id']=$data['live_merchant_id'];
            $keys['live_merchant_key']=$data['live_merchant_key'];
            $keys['environment']=$data['environment'];
            $keys['merchant_website']=$data['merchant_website'];
            $keys['channel']=$data['channel'];
            $keys['industry_type']=$data['industry_type'];
            $paytm['payment_keys']=json_encode($keys);
            $paytm['school_id']=auth()->user()->school_id;
            $paytm->save();

        }
        elseif($method=='flutterwave')
        {
            $keys=array();
            $flutterwave=PaymentMethods::find($update_id);
            $flutterwave['status']=$data['status'];
            $flutterwave['mode']=$data['mode'];
            $keys['test_key']=$data['test_key'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['test_encryption_key']=$data['test_encryption_key'];
            $keys['public_live_key']=$data['public_live_key'];
            $keys['secret_live_key']=$data['secret_live_key'];
            $keys['encryption_live_key']=$data['encryption_live_key'];
            $flutterwave['payment_keys']=json_encode($keys);
            $flutterwave['school_id']=auth()->user()->school_id;
            $flutterwave->save();

        } elseif ($method == 'paystack') {
            $keys = array();
            $paystack = new PaymentMethods;
            $paystack['name'] = "paystack";
            $paystack['image'] = "paystack.png";
            $paystack['status'] = 1;
            $paystack['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $paystack['payment_keys'] = json_encode($keys);
            $paystack['school_id'] = auth()->user()->school_id;
            $paystack->save();

        }

       return redirect()->route('admin.settings.payment')->with('message', 'key has been updated');



    }

    public function insert_gateways()
    {   
        $paypal=PaymentMethods::where(array('name' => 'paypal', 'school_id' => auth()->user()->school_id ))->first();

        if (empty($paypal)) {
            $keys=array();
            $paypal= new PaymentMethods;
            $paypal['name']="paypal";
            $paypal['image']="paypal.png";
            $paypal['status']=1;
            $paypal['mode']="test";
            $keys['test_client_id']="snd_cl_id_xxxxxxxxxxxxx";
            $keys['test_secret_key']="snd_cl_sid_xxxxxxxxxxxx";
            $keys['live_client_id']="lv_cl_id_xxxxxxxxxxxxxxx";
            $keys['live_secret_key']="lv_cl_sid_xxxxxxxxxxxxxx";
            $paypal['payment_keys']=json_encode($keys);
            $paypal['school_id']=auth()->user()->school_id;
            $paypal->save();
        }
        
        $stripe=PaymentMethods::where(array('name' => 'stripe', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($stripe)){
            $keys=array();
            $stripe= new PaymentMethods ;
            $stripe['name']="stripe";
            $stripe['image']="stripe.png";
            $stripe['status']=1;
            $stripe['mode']="test";
            $keys['test_key']="pk_test_xxxxxxxxxxxxx";
            $keys['test_secret_key']="sk_test_xxxxxxxxxxxxxx";
            $keys['public_live_key']="pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key']="sk_live_xxxxxxxxxxxxxx";
            $stripe['payment_keys']=json_encode($keys);
            $stripe['school_id']=auth()->user()->school_id;
            $stripe->save();
        }
        

        $razorpay=PaymentMethods::where(array('name' => 'razorpay', 'school_id' => auth()->user()->school_id ))->first();

        if((empty($razorpay))){
            $keys=array();
            $razorpay= new PaymentMethods ;
            $razorpay['name']="razorpay";
            $razorpay['image']="razorpay.png";
            $razorpay['status']=1;
            $razorpay['mode']="test";
            $keys['test_key']="rzp_test_xxxxxxxxxxxxx";
            $keys['test_secret_key']="rzs_test_xxxxxxxxxxxxx";
            $keys['live_key']="rzp_live_xxxxxxxxxxxxx";
            $keys['live_secret_key']="rzs_live_xxxxxxxxxxxxx";
            $keys['theme_color']="#c7a600";
            $razorpay['payment_keys']=json_encode($keys);
            $razorpay['school_id']=auth()->user()->school_id;
            $razorpay->save();
        }   
            
        $paytm=PaymentMethods::where(array('name' => 'paytm', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($paytm)){
            $keys=array();
            $paytm= new PaymentMethods ;
            $paytm['name']="paytm";
            $paytm['image']="paytm.png";
            $paytm['status']=1;
            $paytm['mode']="test";
            $keys['test_merchant_id']="tm_id_xxxxxxxxxxxx";
            $keys['test_merchant_key']="tm_key_xxxxxxxxxx";
            $keys['live_merchant_id']="lv_mid_xxxxxxxxxxx";
            $keys['live_merchant_key']="lv_key_xxxxxxxxxxx";
            $keys['environment']="provide-a-environment";
            $keys['merchant_website']="merchant-website";
            $keys['channel']="provide-channel-type";
            $keys['industry_type']="provide-industry-type";
            $paytm['payment_keys']=json_encode($keys);
            $paytm['school_id']=auth()->user()->school_id;
            $paytm->save();
        }
        
        $flutterwave=PaymentMethods::where(array('name' => 'flutterwave', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($flutterwave)){
            $keys=array();
            $flutterwave= new PaymentMethods ;
            $flutterwave['name']="flutterwave";
            $flutterwave['image']="flutterwave.png";
            $flutterwave['status']=1;
            $flutterwave['mode']="test";
            $keys['test_key']="flwp_test_xxxxxxxxxxxxx";
            $keys['test_secret_key']="flws_test_xxxxxxxxxxxxx";
            $keys['test_encryption_key']="flwe_test_xxxxxxxxxxxxx";
            $keys['public_live_key']="flwp_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key']="flws_live_xxxxxxxxxxxxxx";
            $keys['encryption_live_key']="flwe_live_xxxxxxxxxxxxxx";
            $flutterwave['payment_keys']=json_encode($keys);
            $flutterwave['school_id']=auth()->user()->school_id;
            $flutterwave->save();
        }

        $paystack=PaymentMethods::where(array('name' => 'paystack', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($paystack)){
            $keys = array();
            $paystack = new PaymentMethods;
            $paystack['name'] = "paystack";
            $paystack['image'] = "paystack.png";
            $paystack['status'] = 1;
            $paystack['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $paystack['payment_keys'] = json_encode($keys);
            $paystack['school_id'] = auth()->user()->school_id;
            $paystack->save();
        }
        


    }

    public function subscriptionPayment($package_id)
    {
        
        $selected_package=Package::find($package_id)->toArray();
        $user_info=User::where('id',auth()->user()->id)->first()->toArray();
        
        if($selected_package['price']==0) 
        {
             $check_duplication=Subscription::where('package_id',$selected_package['id'])->where('school_id',auth()->user()->school_id)->get()->count();
             if($check_duplication==0)
               
             {
                return redirect()->route('admin_free_subcription',['user_id'=>auth()->user()->id,'package_id'=>$selected_package['id']]);
             }
             else
             {
                 return redirect()->back()->with('error', 'you can not subscribe the free trail twice');
             }


        }
         
        return view('admin.subscription.payment_gateway',['selected_package'=>$selected_package,'user_info'=>$user_info]);
    }

    public function admin_free_subcription(Request $request)
    {   
        $data=$request->all();

        $selected_package=Package::find($data['package_id'])->toArray();
        $user_info=User::where('id',$data['user_id'])->first()->toArray();
        $school_email=School::where('id', auth()->user()->school_id)->value('email');
        

        $data['document_file'] = "sample-payment.pdf";

        $transaction_keys = json_encode($data);
                if($selected_package['package_type'] =='life_time'){
                    $status = Subscription::create([
                        'package_id' => $selected_package['id'],
                        'school_id' => auth()->user()->school_id,
                        'paid_amount' => $selected_package['price'],
                        'payment_method' => 'free',
                        'transaction_keys' => $transaction_keys,
                        'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                        'expire_date' => 'life_time',
                        'studentLimit' => $selected_package['studentLimit'],
                        'status' => '1',
                        'active' => '1',
                    ]);
                }else{
                    $status = Subscription::create([
                        'package_id' => $selected_package['id'],
                        'school_id' => auth()->user()->school_id,
                        'paid_amount' => $selected_package['price'],
                        'payment_method' => 'free',
                        'transaction_keys' => $transaction_keys,
                        'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                        'expire_date' => strtotime('+'.$selected_package['days'].' days', strtotime(date("Y-m-d H:i:s")) ),
                        'studentLimit' => $selected_package['studentLimit'],
                        'status' => '1',
                        'active' => '1',
                    ]);
                }
       
        
        Mail::to($school_email)->send(new FreeEmail($status));


            return redirect()->route('admin.subscription')->with('message', 'Free Subscription Completed Successfully');


    }




    public function admin_subscription_offline_payment(Request $request, $id = "")
    {
    	$data = $request->all();

    	if ($data['amount'] > 0) {

			$file = $data['document_image'];

	    	if ($file) {
	            $filename = $file->getClientOriginalName();
	            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

	            
	            $file->move(public_path('assets/uploads/offline_payment'), $filename);
	            $data['document_image'] = $filename;

	        } else {
	        	$data['document_image'] = '';
	        }

           $pending_payment = new PaymentHistory;

           $pending_payment['payment_type']='subscription';
           $pending_payment['user_id']=auth()->user()->id;
           $pending_payment['package_id']=$id;
           $pending_payment['amount']=$data['amount'];
           $pending_payment['school_id']=auth()->user()->school_id;
           $pending_payment['transaction_keys']='[]';
           $pending_payment['document_image']=$data['document_image'];
           $pending_payment['paid_by']='offline';
           $pending_payment['status']='pending';
           $pending_payment['timestamp']=strtotime(date("Y-m-d H:i:s"));

           $pending_payment->save();


            return redirect()->route('admin.subscription')->with('message', 'offline payment requested successfully');
        }else{
            return redirect()->route('admin.subscription')->with('message', 'offline payment requested fail');
        }


    }

    public function offlinePayment(Request $request, $id = "")
    {
        $data = $request->all();

        if ($data['amount'] > 0) :

            $file = $data['document_image'];

            if ($file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

                
                $file->move(public_path('assets/uploads/offline_payment'), $filename);
                $data['document_image'] = $filename;
            } else {
                $data['document_image'] = '';
            }


            PaymentHistory::create([
                'payment_type' => 'subscription',
                'user_id' => auth()->user()->id,
                'amount' => $data['amount'],
                'school_id' => $id,
                'transaction_keys' => json_encode(array()),
                'document_image' => $data['document_image'],
                'paid_by' => 'offline',
                'status' => 'pending',
                'timestamp' => strtotime(date('Y-m-d')),
            ]);

            return redirect('admin/subscription')->with('message', 'Your document will be reviewd.');

        else :
            return redirect('admin/subscription')->with('warning', 'Session timed out. Please try again');
        endif;
    }


    function profile(){
        return view('admin.profile.view');
    }

    function profile_update(Request $request){
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['designation'] = $request->designation;
        
        $user_info['birthday'] = strtotime($request->eDefaultDateRange);
        $user_info['gender'] = $request->gender;
        $user_info['phone'] = $request->phone;
        $user_info['address'] = $request->address;


        if(empty($request->photo)){
            $user_info['photo'] = $request->old_photo;
        }else{
            $file_name = random(10).'.png';
            $user_info['photo'] = $file_name;

            $request->photo->move(public_path('assets/uploads/user-images/'), $file_name);
        }

        $data['user_information'] = json_encode($user_info);

        User::where('id', auth()->user()->id)->update($data);
        
        return redirect(route('admin.profile'))->with('message', get_phrase('Profile info updated successfully'));
    }

    function user_language(Request $request){
        $data['language'] = $request->language;
        User::where('id', auth()->user()->id)->update($data);
        
        return redirect()->back()->with('message', 'You have successfully transleted language.');
    }

    function password($action_type = null, Request $request){



        if($action_type == 'update'){

            

            if($request->new_password != $request->confirm_password){
                return back()->with("error", "Confirm Password Doesn't match!");
            }


            if(!Hash::check($request->old_password, auth()->user()->password)){
                return back()->with("error", "Current Password Doesn't match!");
            }

            $data['password'] = Hash::make($request->new_password);
            User::where('id', auth()->user()->id)->update($data);

            return redirect(route('admin.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('admin.profile.password');
    }



    /**
     * Show the session manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function sessionManager()
    {
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();
        return view('admin.session.session_manager', ['sessions' => $sessions]);
    }

    public function activeSession($id)
    {
        $previous_session_id = get_school_settings(auth()->user()->school_id)->value('running_session');

        Session::where('id', $previous_session_id)->update([
            'status' => '0',
        ]);

        $session = Session::where('id', $id)->update([
            'status' => '1',
        ]);

        School::where('id', auth()->user()->school_id)->update([
            'running_session' => $id,
        ]);

        $response = array(
            'status' => true,
            'notification' => get_phrase('Session has been activated')
        );
        $response = json_encode($response);

        echo $response;
    }

    public function createSession()
    {
        return view('admin.session.create');
    }

    public function sessionCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_session_check = Session::get()->where('session_title', $data['session_title'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_session_check) == 0) {

            $data['status'] = '0';
            $data['school_id'] = auth()->user()->school_id;

            Session::create($data);

            return redirect()->back()->with('message', 'You have successfully create a session.');
        } else {
            return redirect()->back()->with('error', 'Sorry this session already exists');
        }
    }

    public function editSession($id = '')
    {
        $session = Session::find($id);
        return view('admin.session.edit', ['session' => $session]);
    }

    public function sessionUpdate(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Session::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update session.');
    }

    public function sessionDelete($id = '')
    {
        $previous_session_id = get_school_settings(auth()->user()->school_id)->value('running_session');

        if($previous_session_id != $id){
            $session = Session::find($id);
            $session->delete();
            return redirect()->back()->with('message', 'You have successfully delete a session.');
        } else {
            return redirect()->back()->with('error', 'Can not delete active session.');
        }
    }

    // Account Disable
    public function account_disable($id)
    {
        User::where('id', $id)->update([
            'account_status' => 'disable',
        ]);
        return redirect()->back()->with('message','Account Disable Successfully');
    }

    // Account Enable
    public function account_enable($id)
    {
        User::where('id', $id)->update([
            'account_status' => 'enable',
        ]);
        return redirect()->back()->with('message','Account Enable Successfully');
    }

    public function feedback_list()
    {
        $feedbacks = Feedback::where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->paginate(20);
        return view('admin.feedback.feedback_list', ['feedbacks' => $feedbacks]);
    }

    public function create_feedback()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.feedback.create_feedback', ['classes' => $classes]);
    }

    public function upload_feedback(Request $request){
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        //$admin_id = auth()->user()->id;

        $feedbackData = [
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'student_id' => isset($data['student_id'][0]) ? $data['student_id'][0] : null, // Assuming single student for feedback
            'parent_id' => isset($data['parent_id'][0]) ? $data['parent_id'][0] : null, // Assuming single parent for feedback
            'feedback_text' => $data['feedback_text'],
            'school_id' => auth()->user()->school_id,
            'admin_id' => auth()->user()->id,
            'session_id' => $active_session,
            'title' => $data['title']

        ];
    
        // Create feedback entry
        Feedback::create($feedbackData);
    
        return redirect()->back()->with('message', 'Feedback Sent Successfully');
    }

    public function edit_feedback($id){

        $feedback = Feedback::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.feedback.edit_feedback', ['classes' => $classes],  ['feedback' => $feedback]);

    }

    public function update_feedback(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Feedback::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update feedback.');
    }

    public function delete_feedback($id)
     {
        Feedback::where('id', $id)->delete();
         return redirect()->back()->with('message', 'Delete successfully.');
     }

    //  Message

    public function allMessage(Request $request, $id)
    {

            $msg_user_details = DB::table('users')
            ->join('message_thrades', function ($join) {
                // Join where the user is the sender
                $join->on('users.id', '=', 'message_thrades.sender_id')
                    ->orWhere(function ($query) {
                        // Join where the user is the receiver
                        $query->on('users.id', '=', 'message_thrades.reciver_id');
                    });
            })
            ->select('users.id as user_id', 'message_thrades.id as thread_id', 'users.*', 'message_thrades.*')
            ->where('message_thrades.id', $id)
            ->where('message_thrades.school_id', auth()->user()->school_id)
            ->where('users.id', '<>', auth()->user()->id) // Exclude the authenticated user
            ->first();

            
            
        if ($request->ajax()) {
            $query = $request->input('query');
            
            // Search users by name or any other criteria
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->where('school_id', auth()->user()->school_id)
                ->get();

            // Prepare HTML response
            $html = '';

            // Check if any users were found
            if ($users->isEmpty()) {
                return response()->json('No User found');
            }

            foreach ($users as $user) {
                
                if (!empty($user)) {
                    $userInfo = json_decode($user->user_information);
                    
                    $user_image = !empty($userInfo->photo) 
                        ? asset('assets/uploads/user-images/' . $userInfo->photo) 
                        : asset('assets/uploads/user-images/thumbnail.png');

                    $html .= '
                        <div class="user-item d-flex align-items-center msg_us_src_list">
                            <a href="' . route('admin.message.messagethrades', ['id' => $user->id]).'">
                                <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                                <span class="ms-3">' . $user->name . '</span>
                            </a>
                        </div>
                    ';
                }
            }

            return response()->json($html);
        }


        $chat_datas = Chat::where('school_id', auth()->user()->school_id)->get();

        $counter_condition = Chat::where('message_thrade', $id)->orderBy('id', 'desc')->first();

       
        if(!empty($counter_condition->sender_id)){
            if($counter_condition->sender_id != auth()->user()->id){
                Chat::where('message_thrade', $id)->update(['read_status' => 1]);
            }
       }
       
        
        return view('admin.message.all_message', ['msg_user_details' => $msg_user_details], ['chat_datas' => $chat_datas]);
    }

    public function messagethrades($id){

        $exists = MessageThrade::where('reciver_id', $id)
                            ->where('sender_id', auth()->user()->id)
                            ->exists();
        if( $id != auth()->user()->id){
            if (!$exists) {
                $message_thrades_data = [
                    'reciver_id' => $id,
                    'sender_id' => auth()->user()->id,
                    'school_id' => auth()->user()->school_id,
                ];
        
                MessageThrade::create($message_thrades_data);
        
                //return redirect()->back()->with('message', 'User added successfully');
            }
    
            
            $message_thrades = MessageThrade::where('reciver_id', $id)
                                         ->where('sender_id', auth()->user()->id)
                                         ->first();
            $msg_trd_id = $message_thrades->id;
            
            $msg_user_details = DB::table('users')
                ->join('message_thrades', 'users.id', '=', 'message_thrades.reciver_id')
                ->select('users.id as user_id', 'message_thrades.id as thread_id', 'users.*', 'message_thrades.*')
                ->where('message_thrades.id', $msg_trd_id)
                ->first();
    
                $chat_datas = Chat::where('school_id', auth()->user()->school_id)->get();
    
                // Combine all data into a single array
                return view('admin.message.all_message', ['id' => $msg_trd_id, 'msg_user_details' => $msg_user_details, 'chat_datas' => $chat_datas,]);
        }
        return redirect()->back()->with('error', 'You can not add you');
        
                        
    }


    public function chat_save(Request $request)
    {
        $data = $request->all();
        $chat_data = [
            'message_thrade' => $data['message_thrade'],
            'reciver_id' => $data['reciver_id'],
            'message' => $data['message'],
            'school_id' => auth()->user()->school_id,
            'sender_id' => auth()->user()->id,
            'read_status' => 0,

        ];
    
        // Create feedback entry
        Chat::create($chat_data);

        return redirect()->back();
    }

    public function chat_empty(Request $request)
    {

        if ($request->ajax()) {
            $query = $request->input('query');

            $users = User::where('name', 'LIKE', "%{$query}%")
                ->where('school_id', auth()->user()->school_id)
                ->get();

            $html = '';

            if ($users->isEmpty()) {
                return response()->json('No User found');
            }

            foreach ($users as $user) {
                $userInfo = json_decode($user->user_information);
                $user_image = !empty($userInfo->photo) 
                    ? asset('assets/uploads/user-images/' . $userInfo->photo) 
                    : asset('assets/uploads/user-images/thumbnail.png');

                $html .= '
                    <div class="user-item d-flex align-items-center msg_us_src_list">
                        <a href="' . route('admin.message.messagethrades', ['id' => $user->id]).'">
                            <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                            <span class="ms-3">' . $user->name . '</span>
                        </a>
                    </div>
                ';
            }

            return response()->json($html);
        }

        // Pass the data to the view only if msg_user_details is not null
        return view('admin.message.chat_empty');
    }

    public function admitCardList()
    {
        $admit_cards = AdmitCard::where('school_id', auth()->user()->school_id)->get();

        return view('admin.examination.admit_card_list', ['admit_cards' => $admit_cards]);
    }

    public function admitCardCreate()
    {
        
        return view('admin.examination.admit_card_create');
    }

    public function admitCardUpload(Request $request)
    {
        $data = $request->all();
    
        $admitCardData = [
            'template'    => $data['template'],
            'heading'     => $data['heading'],
            'title'       => $data['title'],
            'school_id'   => auth()->user()->school_id,
            'exam_center' => $data['exam_center'],
            'footer_text' => $data['footer_text'], 
        ];
    
        if ($request->hasFile('sign')) {
            $ext = $request->file('sign')->getClientOriginalExtension();
            $newFileName = time() . '.' . $ext;
            $request->file('sign')->move(public_path('assets/upload/user-docs/'), $newFileName);
            $admitCardData['sign'] = $newFileName; // Add the sign filename to $admitCardData
        }  

        AdmitCard::create($admitCardData);
    
        return redirect()->back()->with('message', 'You have successfully created an Admit Card');
    }
    
    public function admitCardEdit($id)
    {
        $admitCardEdit = AdmitCard::find($id);
        return view('admin.examination.admit_card_edit', ['admitCardEdit' => $admitCardEdit]);
    }

    public function admitCardUpdate(Request $request, $id)
    {
        $admitCard = AdmitCard::findOrFail($id);

        $admitCard->template = $request->template;
        $admitCard->heading = $request->heading;
        $admitCard->title = $request->title;
        $admitCard->exam_center = $request->exam_center;
        $admitCard->footer_text = $request->footer_text;

    
        // Check if a new image is uploaded
        if ($request->hasFile('sign')) {
            // Store the new image
            $newImage = $request->file('sign');
            $newFileName = time().'.'.$newImage->getClientOriginalExtension();
            $newImage->move(public_path('assets/upload/user-docs/'), $newFileName);
    
            // Delete the old image if it exists
            if ($admitCard->sign && file_exists(public_path().'assets/upload/user-docs/'.$admitCard->sign)) {
                unlink(public_path().'assets/upload/user-docs/'.$admitCard->sign);
            }
    
            // Update testimonial with the new image path
            $admitCard->sign = $newFileName;
        }
    
        // Save changes
        $admitCard->save();
         
        // Redirect back or wherever needed
        return redirect()->back()->with('message', 'Admit Card Updated Successfully');
    }

    public function admitCardDelete($id)
     {
        AdmitCard::where('id', $id)->delete();
         return redirect()->back()->with('message', 'Delete successfully.');
     }

     public function admitCardPrint()
     {
        $page_data['admit_cards'] = AdmitCard::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();
        $page_data['sessions'] = Session::where('school_id', auth()->user()->school_id)->get();

        return view('admin.examination.admit_card_print', $page_data);
     }

     public function admitCardFilter(Request $request)
     {
        $data = $request->all();

        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['session_id'] = $data['session_id'];

        $page_data['class_name'] = Classes::find($data['class_id'])->name;
        $page_data['section_name'] = Section::find($data['section_id'])->name;
        $page_data['session_title'] = Session::find($data['session_id'])->session_title;
        $admit_cards= AdmitCard::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();

        $enroll_students = Enrollment::where('class_id', $page_data['class_id'])
        ->where('section_id', $page_data['section_id'])
        ->paginate(10);

        $selected_admit_card = AdmitCard::where('id', $data['admit_card_id'])->first();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();

        
        return view('admin.examination.admitCardFilter', [
            'enroll_students' => $enroll_students,
            'page_data' => $page_data,
            'selected_admit_card' => $selected_admit_card,
            'admit_cards' => $admit_cards,
            'classes' => $classes,
            'sessions' => $sessions
        ]);
     }


   
}
