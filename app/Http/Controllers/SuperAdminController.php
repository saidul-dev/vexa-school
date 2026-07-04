<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Session;
use App\Models\School;
use App\Models\Addon;
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
use App\Models\Package;
use App\Models\PaymentHistory;
use App\Models\GlobalSettings;
use App\Models\Currency;
use App\Models\PaymentMethods;
use App\Models\Language;
use App\Models\Faq;
use App\Models\FrontendFeature;
use Mail;
use App\Mail\SchoolEmail;
use Illuminate\Support\Facades\Redirect;
use  Omnipay\Omnipay;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Stripe, DB;
use PaytmWallet;
use File;
use App\Mail\SuperAdminAproved;

class SuperAdminController extends Controller
{
    /**
     * Show the superadmin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    private $publicly_user_id;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            $this->publicly_user_id = $this->id;
            $this->school_id = Auth::user()->school_id;

    
            return $next($request);
        });
    }


    public function superadminDashboard()
    {
        return view('superadmin.dashboard');
    }

    /**
     * Show the school list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function schoolList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $schools = School::where(function ($query) use($search) {
                    $query->where('title', 'LIKE', "%{$search}%");
                })->paginate(10);

        } else {
            $schools = School::paginate(10);
        }

        return view('superadmin.school.list', compact('schools', 'search'));
    }

    public function editSchool($id)
    {
        $school = School::find($id);
        return view('superadmin.school.edit_school', ['school' => $school]);
    }

    public function schoolUpdate(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        School::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update school.');
    }

    public function schoolAdd()
    {
        return view('superadmin.school.add_school');
    }

    public function createSchool(Request $request)
    {
        $data = $request->all();
        $school_email = $data['school_email'];
        $admin_email = $data['admin_email'];
        $duplicate_school_email_check = School::get()->where('email', $school_email);
        $duplicate_admin_email_check = User::get()->where('email', $admin_email);

        if(count($duplicate_school_email_check) == 0 && count($duplicate_admin_email_check) == 0) {
        $school = School::create([
            'title' => $data['school_name'],
            'email' => $data['school_email'],
            'phone' => $data['school_phone'],
            'address' => $data['school_address'],
            'school_info' => $data['school_info'],
            'status' => '2',
        ]);
        
        if($request->school_logo){
            $ext = $request->school_logo->getClientOriginalExtension();
            $newFileName = time().'.'.$ext;
            $request->school_logo->move(public_path().'assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school->school_logo =$newFileName;
            $school->save();
        }  
        
        if (isset($school->id) && $school->id != "") {

            $data['status'] = '1';
            $data['session_title'] = date("Y");
            $data['school_id'] = $school->id;

            $session = Session::create($data);

            School::where('id', $school->id)->update([
                'running_session' => $session->id,
            ]);
            
            if (!empty($data['photo'])) {

                $imageName = time() . '.' . $data['photo']->extension();

                $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

                $photo  = $imageName;
            } else {
                $photo = '';
            }

            $info = array(
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'birthday' => isset($data['birthday']) ? strtotime($data['birthday']) : time(),
                'phone' => $data['admin_phone'],
                'address' => $data['admin_address'],
                'photo' => $photo
            );
            $data['user_information'] = json_encode($info);
            User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role_id' => '2',
                'school_id' => $school->id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        }
        if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
            Mail::to($data['admin_email'])->send(new SchoolEmail($data));
        }
            return redirect()->back()->with('message','School created successfully');
        } else {
            return redirect()->back()->with('warning','Some of the emails have been taken.');
        }

    }

    public function schoolStatusUpdate($id='', $status='')
    {
        $school = School::find($id);
        School::where('id', $id)->update([
            'status' => $status,
        ]);
        return redirect()->back()->with('message', 'School status updated successfully.');
    }
    public function adminList($id){
        $admins = User::where('role_id', 2)->where('school_id', $id)->get();
        $data_id = $id;

        return view('superadmin.school.admin_list', compact('admins','data_id'));
    }

    // School Admin password change
    function admin_password(Request $request){

        $userId = $request->input('user_id');

        $data['password'] = Hash::make($request->password);
            User::where('id', $userId)->update($data);

            return redirect()->back()->with('message', 'You have successfully update password.');
    }

    /**
     * Show the package list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function superadminPackage(Request $request)
    {
        $page_data['search'] = $request['search'] ?? "";

        if($page_data['search'] != "") {

            $query = Package::query();

            $page_data['active_packages'] = $query->where('name', 'LIKE', "%{$page_data['search']}%")
                                                ->where('status', '1')
                                                ->paginate(10);

            $page_data['archive_packages'] = $query->where('name', 'LIKE', "%{$page_data['search']}%")
                                                ->where('status', '0')
                                                ->paginate(10);

        } else {

            $page_data['active_packages'] = Package::where('status', '1')->paginate(10);

            $page_data['archive_packages'] = Package::where('status', '0')->paginate(10);
        }

        return view('superadmin.package.package', $page_data);
    }

    public function createPackage()
    {
        return view('superadmin.package.add_package');
    }


    public function packageCreate(Request $request)
    {
        $data = $request->all();

        $data['features'] = json_encode(array_filter($request->features));

        $interval = Package::where('interval', 'life_time')->first();
        
       if ($interval && $request->interval == 'life_time') {
        return redirect()->back()->with('error', 'You cannot create life-time package second time');
       }else{
           Package::create($data);
       }


        return redirect()->back()->with('message', 'You have successfully create a package.');
    }

    public function editPackage($id)
    {
        $package = Package::find($id);
        return view('superadmin.package.edit_package', ['package' => $package]);
    }

    public function packageUpdate(Request $request, $id)
    {  
        $data = $request->all();

        unset($data['_token']);
        $package = Package::find($id);

        $interval = $package->interval;
        
       if ($interval && $request->interval == 'life_time') {
        return redirect()->back()->with('error', 'You cannot create life-time package second time');
       }else{

       Package::where('id', $id)->update($data);

       if ($interval == 'life_time') {
        $test = Package::where('id', $id)->update(['days' => '', ]);
       }

        return redirect()->back()->with('message', 'You have successfully update package.');
        }   
    }

    public function packageDelete($id)
    {
        $check_subscription = Subscription::where('package_id', $id)->get();

        $check_history = PaymentHistory::where('package_id', $id)->get();

        if(count($check_subscription) > 0){
            return redirect()->back()->with('warning', 'This Package can not be deleted because package is subscripbed by a school.');
        } else if(count($check_history) > 0){
            return redirect()->back()->with('warning', 'This Package can not be deleted because package is subscripbed by a school.');
        } else {

            $package = Package::find($id);
            $package->delete();
            return redirect()->back()->with('message', 'You have successfully delete a package.');
        }
    }

    /**
     * Show the subscription.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function subscriptionReport(Request $request)
    {
        $date_from = strtotime('first day of january this year');
        $date_to = strtotime('last day of december this year');
        $subscriptions = Subscription::where('date_added', '>=', $date_from)
            ->where('date_added', '<=', $date_to)
            ->paginate(10);

        return view('superadmin.subscription.subscription_report', compact('subscriptions', 'date_from', 'date_to'));
    }

    public function subscriptionFilterReport(Request $request)
    {
        $data = $request->all();
        $date = explode('-', $data['eDateRange']);
        $date_from = strtotime($date[0] . ' 00:00:00');
        $date_to  = strtotime($date[1] . ' 23:59:59');
        $subscriptions = Subscription::where('date_added', '>=', $date_from)
            ->where('date_added', '<=', $date_to)
            ->paginate(10);

        return view('superadmin.subscription.subscription_report', compact('subscriptions', 'date_from', 'date_to'));
    }

    public function subscriptionPendingPayment($status = "")
    {
        $date_from = strtotime('first day of january this year');
        $date_to = strtotime('last day of december this year');
        $payment_histories = PaymentHistory::where('paid_by', 'offline')    
            ->where('status', 'pending' )
            ->orwhere('status', 'suspended' )
            ->where('timestamp', '>=', $date_from)
            ->where('timestamp', '<=', $date_to)
            ->paginate(10);
        return view('superadmin.subscription.pending', compact('payment_histories', 'date_from', 'date_to'));
    }

    public function subscriptionFilterPendingPayment(Request $request)
    {
        $data = $request->all();
        $date = explode('-', $data['eDateRange']);
        $date_from = strtotime($date[0] . ' 00:00:00');
        $date_to  = strtotime($date[1] . ' 23:59:59');
        $payment_histories = PaymentHistory::where('paid_by', 'offline')
            ->where('status', 'pending')
            ->where('timestamp', '>=', $date_from)
            ->where('timestamp', '<=', $date_to)
            ->paginate(10);

        return view('superadmin.subscription.pending', compact('payment_histories', 'date_from', 'date_to'));
    }

    public function subscriptionPaymentStatus($status = "", $id = "")
    {

        if ($status == 'approve') {

            $payment_history = PaymentHistory::find($id);
            $package = Package::find($payment_history->package_id);

            $school = School::find($payment_history->school_id);
            $school_email = $school->email;

            $users = User::find($payment_history->user_id);
            $user_name = $users->name;

            
            if(strtolower($package->interval)=='days')
            {
               $expire_date = strtotime('+'.$package->days.' days', strtotime(date("Y-m-d H:i:s")) );

            }
             elseif(strtolower($package->interval)=='monthly')
            {
                $monthly=$package->days*30;

            $expire_date = strtotime('+'.$monthly.' days', strtotime(date("Y-m-d H:i:s")) );

            }
             elseif(strtolower($package->interval)=='yearly')
            {
                $yearly=$package->days*365;
             $expire_date = strtotime('+'.$yearly.' days', strtotime(date("Y-m-d H:i:s")) );

            }
             elseif($package->interval =='life_time')
            {
                $expire_date = '0'; 
            }
          
            $subcription = Subscription::where('school_id', $payment_history->school_id)->orderBy('id', 'desc')->first();


            $today = date("Y-m-d");
            $today_time = strtotime($today);

            if (!empty($subcription)) {
                $uses_date = $subcription['date_added'] - $today_time;
                $upgrade_expireDate =  $expire_date - $uses_date ;   
                $subscriptions_active = $subcription->active;
                $students_limit = $subcription->studentLimit;
                $upgradePrice = $payment_history->amount - $subcription->paid_amount;

                
            }            


            $info = array(
                'document_file' => $payment_history->document_image
            );

            $offline_tr_keys = json_encode($info);

            if(empty($subcription) || $subcription['expire_date'] < $today_time ){
                
                $subscriptionsmail = Subscription::create([
                    'package_id' => $payment_history->package_id,
                    'school_id' => $payment_history->school_id,
                    'paid_amount' => $payment_history->amount,
                    'payment_method' => ucwords($payment_history->paid_by),
                    'transaction_keys' => $offline_tr_keys,
                    'expire_date' => $expire_date,
                    'studentLimit' => $package->studentLimit,
                    'paid_amount' => $payment_history->amount,
                    'date_added' => strtotime(date('Y-m-d')),
                    'active' => '1',
                    'status' => '1',
                    
                ]);

            }elseif($subscriptions_active == '1' && $students_limit == 'Unlimited') {
               
                $subscriptionsmail = Subscription::create([
                    'package_id' => $payment_history->package_id,
                    'school_id' => $payment_history->school_id,
                    'paid_amount' => $upgradePrice,
                    'payment_method' => ucwords($payment_history->paid_by),
                    'transaction_keys' => $offline_tr_keys,
                    'expire_date' => $upgrade_expireDate,
                    'studentLimit' => "Unlimited",
                    'date_added' => strtotime(date('Y-m-d')),
                    'active' => '1',
                    'status' => '1',
                ]);
            }else{
                  
                $subscriptionsmail = Subscription::create([
                    'package_id' => $payment_history->package_id,
                    'school_id' => $payment_history->school_id,
                    'paid_amount' => $upgradePrice,
                    'payment_method' => ucwords($payment_history->paid_by),
                    'transaction_keys' => $offline_tr_keys,
                    'expire_date' => $upgrade_expireDate,
                    'studentLimit' => $package->studentLimit,
                    'date_added' => strtotime(date('Y-m-d')),
                    'active' => '1',
                    'status' => '1',
                    
                ]);
            }
            
            
  
            PaymentHistory::where('id', $id)->update([
                'status' => $status,
            ]);

            if (!empty($subcription)) {
                $subcription = $subcription->toArray();

                Subscription::where('id',  $subcription['id'])->update([
                    'active' => 0,
                ]);
            }

            $school = School::find($id);
            School::where('id', $payment_history->school_id)->update([
                'status' => 1,
            ]);
            if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
            Mail::to($school_email)->send(new SuperAdminAproved($subscriptionsmail));
            }
            return redirect()->back()->with('message', 'You have successfully update status.');
            
        }else{
            
            PaymentHistory::where('id', $id)->update([
                'status' => $status,
            ]);
            return redirect()->back()->with('message', 'Status Suspended .');
        }

        
    }

    public function subscriptionPaymentDelete($id)
    {
        $payment_history = PaymentHistory::find($id);
        $payment_history->delete();
        return redirect()->back()->with('message', 'You have successfully delete a payment history.');
    }

    public function subscriptionExpired(Request $request)
    {   
        $search = $request['search'] ?? "";

        if($search != "") {

            $schools = School::where(function ($query) use($search) {
                    $query->where('title', 'LIKE', "%{$search}%");
                })->paginate(10);

        } else {
            $schools = School::paginate(10);
        }

            $date_from = strtotime('first day of january this year');
            $date_to = strtotime('last day of december this year');
            $subscriptions = Subscription::where('expire_date', '<', strtotime(date('Y-m-d', time())))->where('active','=', 0)->paginate(10);

        return view('superadmin.subscription.expired_subcription', compact('subscriptions', 'date_from', 'date_to', 'search'));   
    }


    /**
     * Show the addon list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addonList()
    {
        $addons = Addon::all();
        return view('superadmin.addons.list', ['addons' => $addons]);
    }

    public function addonInstall()
    {
        return view('superadmin.addons.create');
    }

    public function addonCreate(Request $request)
    {
        // code...
    }

    public function addonStatus($id = '')
    {
        $addon = Addon::find($id);
        if ($addon->status == 1) {
            Addon::where('id', $id)->update([
                'status' => '0',
            ]);
        } else {
            Addon::where('id', $id)->update([
                'status' => '1',
            ]);
        }

        return to_route('superadmin.addon.list');
    }

    public function addonDelete($id)
    {
        $addon = Addon::find($id);
        $addon->delete();
        $child_addons = Addon::where('parent_id', $id)->get();
        if(count($child_addons) > 0) {
            foreach($child_addons as $child_addon) {
                $sub_addon = Addon::find($child_addon->id);
                $sub_addon->delete();
            }
        }
        return redirect()->back()->with('message', 'You have successfully delete a addon.');
    }


    /**
     * Show the system settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function systemSettings()
    {
        return view('superadmin.settings.system_settings');
    }

    public function frontendFeaturesCreate()
    {
        return view('superadmin.settings.frontendFeaturesCreate');
    }

    public function frontendFeaturesadd(Request $request)
    {

        $data = new FrontendFeature();
        $data->id = $request->id;
        $data->title = $request->title;         
        $data->description = $request->description;     
        $data->icon = $request->icon;     
        $data->save();

        return redirect()->back()->with('message', 'Features insert successfully.');
    }

    public function frontFeaDlt($id)
    {
        FrontendFeature::where('id', $id)->delete();
         return redirect()->back()->with('message', 'Deleted successfully.');
    }



    public function frontFeaUpdate( Request  $request, $id)
    {
        $data = request()->except(['_token']);

        FrontendFeature::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'Updated successfully.');
    }

    public function systemUpdate(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);
        foreach ($data as $key => $value) {
            if(DB::table('global_settings')->where('key', $key)->get()->count() > 0) {
                GlobalSettings::where('key', $key)->update([
                    'key' => $key,
                    'value' => $value,
                ]);
            } else {
               GlobalSettings::create([
                    'key' => $key,
                    'value' => $value,
                ]); 
            }
        }
        $file = $request->file('off_pay_ins_file');

        if ($file) {
            $extension = $file->getClientOriginalExtension();

            if ($extension == 'jpg' || $extension == 'png') {
                $off_pay_ins_file = time().'6.png';

            } else {
                $off_pay_ins_file = time().'6.pdf';
            }
        } else {
            
        }

        $email_logo = time().'1.png';
        $socialLogo1 = time().'2.png';
        $socialLogo2 = time().'3.png';
        $socialLogo3 = time().'4.png';
        $front_logo = time().'5.png';
        
       

        if(!empty($request->email_logo)){
            $request->email_logo->move(public_path('assets/uploads/email_logo/'), $email_logo);
            GlobalSettings::where('key', 'email_logo')->update(['value' => $email_logo]);
        }
        if(!empty($request->socialLogo1)){
            $request->socialLogo1->move(public_path('assets/uploads/email_logo/'), $socialLogo1);
            GlobalSettings::where('key', 'socialLogo1')->update(['value' => $socialLogo1]);
        }
        if(!empty($request->socialLogo2)){
            $request->socialLogo2->move(public_path('assets/uploads/email_logo/'), $socialLogo2);
            GlobalSettings::where('key', 'socialLogo2')->update(['value' => $socialLogo2]);
        }
        if(!empty($request->socialLogo3)){
            $request->socialLogo3->move(public_path('assets/uploads/email_logo/'), $socialLogo3);
            GlobalSettings::where('key', 'socialLogo3')->update(['value' => $socialLogo3]);
        }
        if(!empty($request->front_logo)){
            $request->front_logo->move(public_path('assets/uploads/logo/'), $front_logo);
            GlobalSettings::where('key', 'front_logo')->update(['value' => $front_logo]);
        }
        if (!empty($request->off_pay_ins_file)) {
            $request->off_pay_ins_file->move(public_path('assets/uploads/offline_payment/'), $off_pay_ins_file);
            GlobalSettings::where('key', 'off_pay_ins_file')->update(['value' => $off_pay_ins_file]);
        }

        return redirect()->back()->with('message', 'System settings updated successfully.');
    }


    /**
     * Show the smtp settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function smtpSettings()
    {
        return view('superadmin.settings.smtp_settings');
    }

    public function smtpUpdate(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);
        foreach($data as $key => $value){
            if($key == 'smtp_protocol'){
                set_config('MAIL_MAILER', $value);
            }elseif($key == 'smtp_crypto'){
                set_config('MAIL_ENCRYPTION', $value);
            }elseif($key == 'smtp_host'){
                set_config('MAIL_HOST', $value);
            }elseif($key == 'smtp_port'){
                set_config('MAIL_PORT', $value);
            }elseif($key == 'smtp_user'){
                set_config('MAIL_USERNAME', $value);
            }elseif($key == 'smtp_pass'){
                set_config('MAIL_PASSWORD', $value);
            } 

            set_config('MAIL_FROM_ADDRESS', get_settings('system_email'));
            
            GlobalSettings::where('key', $key)->update([
                'key' => $key,
                'value' => $value,
            ]);
        }
        

        return redirect()->back()->with('message','Smtp settings updated successfully.');
    }


    
    public function about()
    {

        $purchase_code = get_settings('purchase_code');
        $returnable_array = array(
            'purchase_code_status' => get_phrase('Not found'),
            'support_expiry_date'  => get_phrase('Not found'),
            'customer_name'        => get_phrase('Not found')
        );

        $personal_token = "gC0J1ZpY53kRpynNe4g2rWT5s4MW56Zg";
        $url = "https://api.envato.com/v3/market/author/sale?code=" . $purchase_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer   = 'bearer ' . $personal_token;
        $header   = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:' . $purchase_code . '.json';
        $ch_verify = curl_init($verify_url . '?code=' . $purchase_code);

        curl_setopt($ch_verify, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch_verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_verify, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_verify, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec($ch_verify);
        curl_close($ch_verify);

        $response = json_decode($cinit_verify_data, true);

        if (is_array($response) && isset($response['verify-purchase']) && count($response['verify-purchase']) > 0) {

            //print_r($response);
            $item_name         = $response['verify-purchase']['item_name'];
            $purchase_time       = $response['verify-purchase']['created_at'];
            $customer         = $response['verify-purchase']['buyer'];
            $licence_type       = $response['verify-purchase']['licence'];
            $support_until      = $response['verify-purchase']['supported_until'];
            $customer         = $response['verify-purchase']['buyer'];

            $purchase_date      = date("d M, Y", strtotime($purchase_time));

            $todays_timestamp     = strtotime(date("d M, Y"));
            $support_expiry_timestamp = strtotime($support_until);

            $support_expiry_date  = date("d M, Y", $support_expiry_timestamp);

            if ($todays_timestamp > $support_expiry_timestamp)
                $support_status    = 'expired';
            else
                $support_status    = 'valid';

            $returnable_array = array(
                'purchase_code_status' => $support_status,
                'support_expiry_date'  => $support_expiry_date,
                'customer_name'        => $customer,
                'product_license'      => 'valid',
                'license_type'         => $licence_type
            );
        } else {
            $returnable_array = array(
                'purchase_code_status' => 'invalid',
                'support_expiry_date'  => 'invalid',
                'customer_name'        => 'invalid',
                'product_license'      => 'invalid',
                'license_type'         => 'invalid'
            );
        }


        $data['application_details'] = $returnable_array;
        return view('superadmin.settings.about', $data);
    }


    function curl_request($code = '')
    {

        $purchase_code = $code;

        $personal_token = "FkA9UyDiQT0YiKwYLK3ghyFNRVV9SeUn";
        $url = "https://api.envato.com/v3/market/author/sale?code=" . $purchase_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer   = 'bearer ' . $personal_token;
        $header   = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:' . $purchase_code . '.json';
        $ch_verify = curl_init($verify_url . '?code=' . $purchase_code);

        curl_setopt($ch_verify, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch_verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_verify, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_verify, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec($ch_verify);
        curl_close($ch_verify);

        $response = json_decode($cinit_verify_data, true);

        if (is_array($response) && count($response['verify-purchase']) > 0) {
            return true;
        } else {
            return false;
        }
    }


    //Don't remove this code for security reasons
    function save_valid_purchase_code($action_type, Request $request){

        if($action_type == 'update'){
            $data['value'] = $request->purchase_code;

            GlobalSettings::where('key', 'purchase_code')->update($data);
            session()->flash('message', get_phrase('Purchase code has been updated'));
            echo 1;
        }else{
            return view('superadmin.settings.save_purchase_code_form');
        }
        
    }

    public function payment_settings()
    {


        $global_currency = GlobalSettings::where('key', 'system_currency')->first()->toArray();
        $global_currency = $global_currency['value'];
        $global_currency_position = GlobalSettings::where('key', 'currency_position')->first()->toArray();
        $global_currency_position = $global_currency_position['value'];

        $currencies = Currency::all()->toArray();

        $paypal = "";
        $stripe = "";
        $razorpay = "";
        $paytm = "";
        $flutterwave = "";
        $paystack = "";

        $paypal = GlobalSettings::where('key', 'paypal')->first();
        if (!empty($paypal)) {
            
            $paypal = json_decode($paypal['value'], true);
        }

        $stripe = GlobalSettings::where('key', 'stripe')->first();
        if (!empty($stripe)) {
          
            $stripe = json_decode($stripe['value'], true);
        }

        $razorpay = GlobalSettings::where('key', 'razorpay')->first();
        if (!empty($razorpay)) {
         
            $razorpay = json_decode($razorpay['value'], true);
        }

        $paytm = GlobalSettings::where('key', 'paytm')->first();
        if (!empty($paytm)) {

            $paytm = json_decode($paytm['value'], true);
        }
        
        $flutterwave = GlobalSettings::where('key', 'flutterwave')->first();
        if (!empty($flutterwave)) {

            $flutterwave = json_decode($flutterwave['value'], true);
        }

        $paystack = GlobalSettings::where('key', 'paystack')->first();
        if (!empty($paystack)) {

            $paystack = json_decode($paystack['value'], true);
        }




        return view('superadmin.payment_credentials.payment_settings', ['paystack' => $paystack, 'paytm' => $paytm, 'razorpay' => $razorpay, 'stripe' => $stripe, 'paypal' => $paypal, 'flutterwave' => $flutterwave, 'global_currency' => $global_currency, 'global_currency_position' => $global_currency_position, 'currencies' => $currencies]);
    }



    public function update_payment_settings(Request $request)
    {

        $data = $request->all();
        $update_id = $data['method'];



        if ($data['method'] == 'currency') {

            GlobalSettings::where('key', 'system_currency')->update([
                'value' =>  $data['global_currency'],
            ]);
            GlobalSettings::where('key', 'currency_position')->update([
                'value' =>  $data['currency_position'],
            ]);
        } 
        elseif ($data['method'] == 'paypal') {
            $keys = array();
            $paypal = GlobalSettings::where('key', 'paypal')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_client_id'] = $data['test_client_id'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['live_client_id'] = $data['live_client_id'];
            $keys['live_secret_key'] = $data['live_secret_key'];
            $paypal['value'] = json_encode($keys);
            $paypal->save();
        } 
        elseif ($data['method'] == 'stripe') {
            $keys = array();
            $stripe = GlobalSettings::where('key', 'stripe')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['public_live_key'] = $data['public_live_key'];
            $keys['secret_live_key'] = $data['secret_live_key'];
            $stripe['value'] = json_encode($keys);
            $stripe->save();
        } 
        elseif ($data['method'] == 'razorpay') {
            $keys = array();
            $razorpay = GlobalSettings::where('key', 'razorpay')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['live_key'] = $data['live_key'];
            $keys['live_secret_key'] = $data['live_secret_key'];
            $keys['theme_color'] = $data['theme_color'];
            $razorpay['value'] = json_encode($keys);
            $razorpay->save();
        } 
        elseif ($data['method'] == 'paytm') {
            $keys = array();
            $paytm = GlobalSettings::where('key', 'paytm')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_merchant_id'] = $data['test_merchant_id'];
            $keys['test_merchant_key'] = $data['test_merchant_key'];
            $keys['live_merchant_id'] = $data['live_merchant_id'];
            $keys['live_merchant_key'] = $data['live_merchant_key'];
            $keys['environment'] = $data['environment'];
            $keys['merchant_website'] = $data['merchant_website'];
            $keys['channel'] = $data['channel'];
            $keys['industry_type'] = $data['industry_type'];
            $paytm['value'] = json_encode($keys);
            $paytm->save();
        } 
        elseif($data['method'] =='flutterwave') {
            $keys = array();
            $flutterwave = GlobalSettings::where('key', 'flutterwave')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['test_encryption_key']=$data['test_encryption_key'];
            $keys['public_live_key'] = $data['public_live_key'];
            $keys['secret_live_key'] = $data['secret_live_key'];
            $keys['encryption_live_key'] = $data['encryption_live_key'];
            $flutterwave['value'] = json_encode($keys);
            $flutterwave->save();

        } elseif ($data['method'] == 'paystack') {
            $keys = array();
            $stripe = GlobalSettings::where('key', 'paystack')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['public_live_key'] = $data['public_live_key'];
            $keys['secret_live_key'] = $data['secret_live_key'];
            $stripe['value'] = json_encode($keys);
            $stripe->save();
        }


        return redirect()->route('superadmin.payment_settings')->with('message', 'key has been updated');
    }


    function profile(){
        return view('superadmin.profile.view');
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
        
        return redirect(route('superadmin.profile'))->with('message', get_phrase('Profile info updated successfully'));
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

            return redirect(route('superadmin.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('superadmin.profile.password');
    }

   

    //logo update
    function update_logo(Request $request){
        $dark_logo = time().'1.png';
        $light_logo = time().'2.png';
        $favicon = time().'3.png';
        $white_logo = time().'4.png';

        if(!empty($request->dark_logo)){
            $request->dark_logo->move(public_path('assets/uploads/logo/'), $dark_logo);
            GlobalSettings::where('key', 'dark_logo')->update(['value' => $dark_logo]);
        }
        if(!empty($request->light_logo)){
            $request->light_logo->move(public_path('assets/uploads/logo/'), $light_logo);
            GlobalSettings::where('key', 'light_logo')->update(['value' => $light_logo]);
        }
        if(!empty($request->favicon)){
            $request->favicon->move(public_path('assets/uploads/logo/'), $favicon);
            GlobalSettings::where('key', 'favicon')->update(['value' => $favicon]);
        }
        if(!empty($request->white_logo)){
            $request->white_logo->move(public_path('assets/uploads/logo/'), $white_logo);
            GlobalSettings::where('key', 'white_logo')->update(['value' => $white_logo]);
        }

        return redirect('superadmin/settings/system')->with('message', "Logo updated successfully");

    }

    public function manageLanguage($language = '')
    {
        if(!empty($language)) {

            $edit_profile = $language;
            $phrases = Language::where('name', $language)->get();
            $languages = get_all_language();

            return view('superadmin.language.manage_language', ['languages' => $languages, 'edit_profile' => $edit_profile, 'phrases' => $phrases]);
        } else {

            $languages = get_all_language();
            return view('superadmin.language.manage_language', ['languages' => $languages]);

        }
    }

    public function addLanguage(Request $request){

        $language = $request->language;
        if ($language == 'n-a') {
            return redirect('superadmin/settings/language')->with('error', "Language name can not be empty or can not have special characters");
        }

        $phrases = Language::where('name', 'english')->get();

        foreach($phrases as $phrase){
            Language::create([
                'name' => $language,
                'phrase' => $phrase->phrase,
                'translated' => $phrase->translated,
            ]);
        }

        return redirect('superadmin/settings/language')->with('message', "Language added successfully");
    }

    public function updatedPhrase(Request $request)
    {
        $current_editing_language = $request->currentEditingLanguage;
        $updatedValue = $request->updatedValue;
        $phrase = $request->phrase;

        $query = Language::where('name', $current_editing_language)
            ->where('phrase', $phrase)
            ->first();

        if (!empty($query) && $query->count() > 0) {
            $query->translated = $updatedValue;
            $query->save();
        }
    }

    public function deleteLanguage($name='')
    {
        $language = Language::where('name', $name)->get();
        $language->map->delete();
        return redirect()->back()->with('message', 'You have successfully delete a language.');
    }


    /**
     * Show the website settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function websiteSettings()
    {
        return view('superadmin.settings.website_settings');
    }


    /**
     * Show the faq.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function faqViews()
    {
        $faqs = Faq::all();
        return view('superadmin.settings.faq_views', ['faqs' => $faqs]);
    }

    public function faqAdd()
    {
        return view('superadmin.settings.add_faq');
    }

    public function faqCreate(Request $request)
    {
        $data = $request->all();

        Faq::create($data);

        return redirect()->back()->with('message', 'You have successfully create a faq.');
    }

    public function faqEdit($id="")
    {
        $faq = Faq::find($id);
        return view('superadmin.settings.edit_faq', ['faq' => $faq]);
    }

    public function faqUpdate(Request $request, $id="")
    {
        $data = $request->all();

        unset($data['_token']);

        Faq::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully create a faq.');
    }

    public function faqDelete($id='')
    {
        $faq = Faq::find($id);
        $faq->delete();
        return redirect()->back()->with('message', 'You have successfully delete a faq.');
    }

}
