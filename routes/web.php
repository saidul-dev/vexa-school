<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\Updater;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Clear application cache:
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');

    //Artisan::call('route:cache');

    Artisan::call('config:cache');

    Artisan::call('view:clear');

    Artisan::call('optimize:clear');

    return 'Cache cleard';
})->name('clear.cache');


//Auth routes are here
Auth::routes();


//Landing page routes are here
Route::controller(HomeController::class)->group(function () {

    Route::get('/', 'home')->name('landingPage');
    Route::post('school/create', 'schoolCreate')->name('school.create');
    Route::get('web_redirect_to_pay_fee', 'webRedirectToPayFee')->name('webRedirectToPayFee');  

});

// Account Disable Route

Route::get('admin/account-disable', function () {
    return view('admin.account_disableview');
})->name('admin.account_disableview');

Route::get('accountant/account-disable', function () {
    return view('accountant.account_disable');
})->name('accountant.account_disable');

Route::get('teacher/account-disable', function () {
    return view('teacher.account_disable');
})->name('teacher.account_disable');

Route::get('librarian/account-disable', function () {
    return view('librarian.account_disable');
})->name('librarian.account_disable');

Route::get('parent/account-disable', function () {
    return view('parent.account_disable');
})->name('parent.account_disable');

Route::get('student/account-disable', function () {
    return view('student.account_disable');
})->name('student.account_disable');


//Superadmin routes are here
Route::controller(SuperAdminController::class)->middleware('auth','superAdmin')->group(function () {

    Route::get('superadmin/dashboard', 'superadminDashboard')->name('superadmin.dashboard')->middleware('role_id');


    //School routes
    Route::get('superadmin/school/list', 'schoolList')->name('superadmin.school.list');
    Route::get('superadmin/school/edit/{id}', 'editSchool')->name('superadmin.edit.school');
    Route::post('superadmin/school//update/{id}', 'schoolUpdate')->name('superadmin.school.update');
    Route::get('superadmin/school/add', 'schoolAdd')->name('superadmin.school.add');
    Route::post('superadmin/school/create', 'createSchool')->name('superadmin.school.create');
    Route::get('superadmin/school/status_update/{id}/{status}', 'schoolStatusUpdate')->name('superadmin.school.status_update');
    Route::get('superadmin/school/admin_list/{id}', 'adminList')->name('superadmin.school.admin_list');


    //Package routes
    Route::get('superadmin/package', 'superadminPackage')->name('superadmin.package');
    Route::get('superadmin/package/create', 'createPackage')->name('superadmin.create.package');
    Route::post('superadmin/package_add', 'packageCreate')->name('superadmin.package.create');
    Route::get('superadmin/package/{id}', 'editPackage')->name('superadmin.edit.package');
    Route::post('superadmin/package/{id}', 'packageUpdate')->name('superadmin.package.update');
    Route::get('superadmin/package/delete/{id}', 'packageDelete')->name('superadmin.package.delete');


    //Subscription routes
    Route::get('superadmin/subscription/report', 'subscriptionReport')->name('superadmin.subscription.report');
    Route::post('superadmin/subscription/report/filter', 'subscriptionFilterReport')->name('superadmin.subscription.filter_report');
    Route::get('superadmin/subscription/pending', 'subscriptionPendingPayment')->name('superadmin.subscription.pending');
    Route::post('superadmin/subscription/pending/filter', 'subscriptionFilterPendingPayment')->name('superadmin.subscription.filter_pending');
    Route::get('superadmin/subscription/{status}/{id}', 'subscriptionPaymentStatus')->name('superadmin.subscription.status');
    Route::get('superadmin/subscription/delete/{id}', 'subscriptionPaymentDelete')->name('superadmin.subscription.delete');
    Route::get('superadmin/subscription/expired_subcription', 'subscriptionExpired')->name('superadmin.subscription.expired_subcription');


    //Addon routes
    Route::get('superadmin/addon/list', 'addonList')->name('superadmin.addon.list');
    Route::get('superadmin/addon/install', 'addonInstall')->name('superadmin.addon.install');
    Route::get('superadmin/addon/status/{id}', 'addonStatus')->name('superadmin.addon.status');
    Route::get('superadmin/addon/delete/{id}', 'addonDelete')->name('superadmin.addon.delete');


    //System settings routes
    Route::get('superadmin/settings/system', 'systemSettings')->name('superadmin.system_settings');
    Route::post('superadmin/system/update', 'systemUpdate')->name('superadmin.system.update');

    //Frontend features 
    Route::get('superadmin/settings/frontendFeaturesCreate', 'frontendFeaturesCreate')->name('superadmin.settings.frontendFeaturesCreate');
    Route::post('superadmin/system/frontendFeaturesadd', 'frontendFeaturesadd')->name('superadmin.system.frontendFeaturesadd');
    Route::get('superadmin/system/delete/{id}', 'frontFeaDlt')->name('superadmin.system.frontendFeaturesDlt');
    Route::post('superadmin/system/update/{id}', 'frontFeaUpdate')->name('superadmin.system.frontFeaUpdate');


    //Website settings routes
    Route::get('superadmin/settings/website', 'websiteSettings')->name('superadmin.website_settings');


    //FAQ
    Route::get('superadmin/settings/faq', 'faqViews')->name('superadmin.faq_views');
    Route::get('superadmin/settings/faq_add', 'faqAdd')->name('superadmin.faq_add');
    Route::post('superadmin/settings/faq_create', 'faqCreate')->name('superadmin.faq_create');
    Route::get('superadmin/settings/faq_edit/{id}', 'faqEdit')->name('superadmin.faq_edit');
    Route::post('superadmin/settings/faq_update/{id}', 'faqUpdate')->name('superadmin.faq_update');
    Route::get('superadmin/settings/faq/delete/{id}', 'faqDelete')->name('superadmin.faq.delete');


    //Language settings routes
    Route::get('superadmin/settings/language/{language?}', 'manageLanguage')->name('superadmin.language.manage');
    Route::post('superadmin/settings/language/add', 'addLanguage')->name('superadmin.language.add');
    Route::post('superadmin/settings/language/{language?}', 'updatedPhrase')->name('superadmin.language.update_phrase');
    Route::get('superadmin/settings/language/delete/{name}', 'deleteLanguage')->name('superadmin.language.delete');


    //Smtp settings routes
    Route::get('superadmin/settings/smtp', 'smtpSettings')->name('superadmin.smtp_settings');
    Route::post('superadmin/smtp/update', 'smtpUpdate')->name('superadmin.smtp.update');


    //About routes
    Route::get('superadmin/settings/about', 'about')->name('superadmin.about');
    Route::any('superadmin/save_valid_purchase_code/{action_type?}', 'save_valid_purchase_code')->name('superadmin.save_valid_purchase_code');


    //Payment settings routes
    Route::get('superadmin/payment/settings', 'payment_settings')->name('superadmin.payment_settings');
    Route::post('superadmin/payment/settings/update', 'update_payment_settings')->name('superadmin.update_payment_settings');


    //Payment create routes
    Route::post('PayWithPaypal/subscription', 'payWithPaypal_ForSubscription')->name('superadmin.paypal.subscription');
    Route::post('PayWithStripe/subscription', 'PayWithStripe_ForSubscription')->name('superadmin.stripe.subscription');
    Route::post('PayWithRazorpay/subscription', 'PayWithRazorpay_ForSubscription')->name('superadmin.razorpay.subscription');
    Route::post('PayWithPaytm/subscription', 'PayWithPaytm_ForSubscription')->name('superadmin.paytm.subscription');
    Route::post('subscription/paytm-callback/{success_url}/{cancle_url}/{user_data}', 'Subcription_PaytmCallback')->name('superadmin.paytm.callback');

    //Profile
    Route::get('superadmin/profile', 'profile')->name('superadmin.profile');
    Route::post('superadmin/profile/update', 'profile_update')->name('superadmin.profile.update');
    Route::any('superadmin/password/{action_type}', 'password')->name('superadmin.password');
    Route::any('superadmin/admin_password/', 'admin_password')->name('superadmin.admin_list');
    Route::post('superadmin/language', 'user_language')->name('superadmin.language');


    //Logo update
    Route::post('superadmin/logo/update', 'update_logo')->name('superadmin.logo.update');

});
//Superadmin routes end here


//Admin routes are here
Route::controller(AdminController::class)->middleware('admin','auth')->group(function () {

    Route::get('admin/dashboard', 'adminDashboard')->name('admin.dashboard')->middleware('role_id');


    //Common routes
    Route::get('admin/section/{id}', 'classWiseSections')->name('admin.class_wise_sections');
    Route::get('admin/subjects/{id}', 'classWiseSubject')->name('admin.class_wise_subject');
    Route::get('admin/students/{id}', 'classWiseStudents')->name('admin.class_wise_student');
    Route::get('admin/students_invoice/{id}', 'classWiseStudentsInvoice')->name('admin.class_wise_student_invoice');


    //Admin users route
    Route::get('admin/admin', 'adminList')->name('admin.admin')->middleware('admin_permission');
    Route::get('admin/admin/create_modal', 'createModal')->name('admin.open_modal');
    Route::post('admin/admin', 'adminCreate')->name('admin.create');
    Route::get('admin/admin/edit_modal/{id}', 'editModal')->name('admin.open_edit_modal');
    Route::post('admin/admin/{id}', 'adminUpdate')->name('admin.update');
    Route::get('admin/admin/delete/{id}', 'adminDelete')->name('admin.admin.delete');
    Route::get('admin/admin/admin_profile/{id}', 'adminProfile')->name('admin.admin.admin_profile');
    Route::any('admin/user_password/', 'school_user_password')->name('admin.user_password');
    Route::get('admin/admin/menu_permission/{id}', 'menuSettingsView')->name('admin.admin.menu_permission');
    Route::post('admin/admin/menu_permission_update/{id}', 'menuPermissionUpdate')->name('admin.admin.menu_permission_update');


    Route::get('admin/admin-documents/{id}', 'adminDocuments')->name('admin.documents');
    Route::get('admin/accountant-documents/{id}', 'accountantDocuments')->name('admin.accountant.documents');
    Route::get('admin/librarian-documents/{id}', 'librarianDocuments')->name('admin.librarian.documents');
    Route::get('admin/parent-documents/{id}', 'parentDocuments')->name('admin.parent.documents');
    Route::get('admin/student-documents/{id}', 'studentDocuments')->name('admin.student.documents');
    Route::get('admin/teacher-documents/{id}', 'teacherDocuments')->name('admin.teacher.documents');



    Route::any('admin/documents-upload/{id}', 'documentsUpload')->name('admin.documents.upload');
    Route::get('admin/documents-remove/{id}/{file_name}', 'documentsRemove')->name('admin.documents.remove');


    //Teacher users route
    Route::get('admin/teacher', 'teacherList')->name('admin.teacher')->middleware('admin_permission');
    Route::get('admin/teacher/create_modal', 'createTeacherModal')->name('admin.teacher.open_modal');
    Route::post('admin/teacher', 'adminTeacherCreate')->name('admin.teacher.create');
    Route::get('admin/teacher/edit/{id}', 'teacherEditModal')->name('admin.teacher_edit_modal');
    Route::post('admin/teacher/{id}', 'teacherUpdate')->name('admin.teacher.update');
    Route::get('admin/teacher/delete/{id}', 'teacherDelete')->name('admin.teacher.delete');
    Route::get('admin/teacher/teacher_profile/{id}', 'teacherProfile')->name('admin.teacher.teacher_profile');


    //Accountant users route
    Route::get('admin/accountant', 'accountantList')->name('admin.accountant')->middleware('admin_permission');
    Route::get('admin/accountant/create_modal', 'createAccountantModal')->name('admin.accountant.open_modal');
    Route::post('admin/accountant', 'accountantCreate')->name('admin.accountant.create');
    Route::get('admin/accountant/edit/{id}', 'accountantEditModal')->name('admin.accountant_edit_modal');
    Route::post('admin/accountant/{id}', 'accountantUpdate')->name('admin.accountant.update');
    Route::get('admin/accountant/delete/{id}', 'accountantDelete')->name('admin.accountant.delete');
    Route::get('admin/accountant/accountant_profile/{id}', 'accountantProfile')->name('admin.accountant.accountant_profile');


    //Librarian users route
    Route::get('admin/librarian', 'librarianList')->name('admin.librarian')->middleware('admin_permission');
    Route::get('admin/librarian/create_modal', 'createLibrarianModal')->name('admin.librarian.open_modal');
    Route::post('admin/librarian', 'librarianCreate')->name('admin.librarian.create');
    Route::get('admin/librarian/edit/{id}', 'librarianEditModal')->name('admin.librarian_edit_modal');
    Route::post('admin/librarian/{id}', 'librarianUpdate')->name('admin.librarian.update');
    Route::get('admin/librarian/delete/{id}', 'librarianDelete')->name('admin.librarian.delete');
    Route::get('admin/librarian/librarian_profile/{id}', 'librarianProfile')->name('admin.librarian.librarian_profile');


    //Parent users route
    Route::get('admin/parent', 'parentList')->name('admin.parent')->middleware('admin_permission');
    Route::post('admin/parent', 'parentCreate')->name('admin.parent.create');
    Route::get('admin/parent/create', 'createParent')->name('admin.parent.create_form');
    Route::get('admin/parent/edit/{id}', 'parentEditModal')->name('admin.parent_edit_modal');
    Route::post('admin/parent/{id}', 'parentUpdate')->name('admin.parent.update');
    Route::get('admin/parent/delete/{id}', 'parentDelete')->name('admin.parent.delete');
    Route::get('admin/parent/parent_profile/{id}', 'parentProfile')->name('admin.parent.parent_profile');

    //Student users route
    Route::get('admin/student', 'studentList')->name('admin.student')->middleware('admin_permission');
    Route::get('admin/student/create_modal', 'createStudentModal')->name('admin.student.open_modal');
    Route::post('admin/student', 'studentCreate')->name('admin.student.create');
    Route::get('admin/student/id_card/{id}', 'studentIdCardGenerate')->name('admin.student.id_card');
    Route::get('admin/student/edit/{id}', 'studentEditModal')->name('admin.student_edit_modal');
    Route::post('admin/student/{id}', 'studentUpdate')->name('admin.student.update');
    Route::get('admin/student/delete/{id}', 'studentDelete')->name('admin.student.delete');
    Route::get('admin/student/student_profile/{id}', 'studentProfile')->name('admin.student.student_profile');

    //User Account Status
    Route::get('admin/user_disable/{id}', 'account_disable')->name('admin.account_disable');
    Route::get('admin/user_enable/{id}', 'account_enable')->name('admin.account_enable');

    //Teacher permission route
    Route::get('admin/permission', 'teacherPermission')->name('admin.teacher.permission');
    Route::get('admin/permission/list/{filter}', 'teacherPermissionList')->name('admin.teacher.permission_list');
    Route::get('admin/teacher/permission_update', 'teacherPermissionUpdate')->name('admin.teacher.modify_permission');
    Route::get('admin/upgrade_subscription', 'upgreadeSubscription')->name('admin.subscription.upgrade_subscription');


    //Admissions routes
    Route::get('admin/offline_admission/{type}', 'offlineAdmissionForm')->name('admin.offline_admission.single')->middleware('admin_permission')->middleware('admin_permission');
    Route::post('admin/offline_admission', 'offlineAdmissionCreate')->name('admin.offline_admission.create');
    Route::post('admin/offline_admission/bulk', 'offlineAdmissionBulkCreate')->name('admin.offline_admission.bulk_create');
    Route::post('admin/offline_admission/excel', 'offlineAdmissionExcelCreate')->name('admin.offline_admission.excel_create');
    Route::get('admin/offline_admission_preview', function () {
        return view('admin.offline_admission.csv_preview');
    })->name('admin.offline_admission.preview');


    //Exam category routes
    Route::get('admin/exam_category', 'examCategoryList')->name('admin.exam_category')->middleware('admin_permission');
    Route::get('admin/exam_category/create', 'createExamCategory')->name('admin.exam_category.open_modal');
    Route::post('admin/exam_category', 'examCategoryCreate')->name('admin.create.exam_category');
    Route::get('admin/exam_category/{id}', 'editExamCategory')->name('admin.edit.exam_category');
    Route::post('admin/exam_category/{id}', 'examCategoryUpdate')->name('admin.exam_category.update');
    Route::get('admin/exam_category/delete/{id}', 'examCategoryDelete')->name('admin.exam_category.delete');


    //Exam routes
    Route::get('admin/offline_exam', 'offlineExamList')->name('admin.offline_exam')->middleware('admin_permission');
    Route::get('admin/offline_exam/export/{id}', 'offlineExamExport')->name('admin.offline_exam.export');
    Route::get('admin/exam', 'createOfflineExam')->name('admin.offline_exam.open_modal');
    Route::post('admin/offline_exam', 'offlineExamCreate')->name('admin.create.offline_exam');
    Route::get('admin/offline_exam/{id}', 'editOfflineExam')->name('admin.edit.offline_exam');
    Route::post('admin/offline_exam/{id}', 'offlineExamUpdate')->name('admin.offline_exam.update');
    Route::get('admin/offline_exam/delete/{id}', 'offlineExamDelete')->name('admin.offline_exam.delete');
    Route::get('admin/exam_list_by_class/{id}', 'classWiseOfflineExam')->name('admin.class_wise_exam_list');


    //Admit Card
    Route::get('admin/admit-card-list', 'admitCardList')->name('admin.examination.admit_card_list');
    Route::get('admin/admit-card-create', 'admitCardCreate')->name('admin.examination.admit_card_create');
    Route::post('admin/admit-card-upload', 'admitCardUpload')->name('admin.examination.admit_card_upload');
    Route::get('admin/admit-card-edit/{id}', 'admitCardEdit')->name('admin.examination.admit_card_edit');
    Route::post('admin/admit-card-update/{id}', 'admitCardUpdate')->name('admin.examination.admit_card_update');
    Route::get('admin/admit-card-delete/{id}', 'admitCardDelete')->name('admin.examination.admit_card_delete');

    Route::get('admin/print-admit-card', 'admitCardPrint')->name('admin.examination.admit_card_print');
    Route::get('admin/admitCardFilter', 'admitCardFilter')->name('admin.examination.admitCardFilter');


    //Attendance routes
    Route::get('admin/attendance', 'dailyAttendance')->name('admin.daily_attendance')->middleware('admin_permission');
    Route::get('admin/take_attendance', 'takeAttendance')->name('admin.take_attendance.open_modal');
    Route::post('admin/attendance_take', 'attendanceTake')->name('admin.attendance_take');
    Route::get('admin/attendance/student', 'studentListAttendance')->name('admin.attendance.student');
    Route::get('admin/attendance/filter', 'dailyAttendanceFilter')->name('admin.daily_attendance.filter');
    Route::get('admin/attendance/csv', 'dailyAttendanceFilter_csv')->name('admin.dailyAttendanceFilter_csv');


    //Routine routes
    Route::get('admin/routine', 'routine')->name('admin.routine')->middleware('admin_permission');
    Route::get('admin/routine/add_routine', 'addRoutine')->name('admin.routine.open_modal');
    Route::post('admin/routine/routine_add', 'routineAdd')->name('admin.routine.routine_add');
    Route::get('admin/routine/list', 'routineList')->name('admin.routine.routine_list');
    Route::get('admin/routine/edit/{id}', 'routineEditModal')->name('admin.routine_edit_modal');
    Route::post('admin/routine/{id}', 'routineUpdate')->name('admin.routine.update');
    Route::get('admin/routine/delete/{id}', 'routineDelete')->name('admin.routine.delete');


    //Syllabus routes
    Route::get('admin/syllabus', 'syllabus')->name('admin.syllabus')->middleware('admin_permission');
    Route::get('admin/syllabus/add_routine', 'addSyllabus')->name('admin.syllabus.open_modal');
    Route::post('admin/syllabus/routine_add', 'syllabusAdd')->name('admin.syllabus.syllabus_add');
    Route::get('admin/syllabus/list', 'syllabusList')->name('admin.syllabus.syllabus_list');
    Route::get('admin/syllabus/edit/{id}', 'syllabusEditModal')->name('admin.syllabus_edit_modal');
    Route::post('admin/syllabus/{id}', 'syllabusUpdate')->name('admin.syllabus.update');
    Route::get('admin/syllabus/delete/{id}', 'syllabusDelete')->name('admin.syllabus.delete');


    //Gradebooks routes
    Route::get('admin/gradebook', 'gradebook')->name('admin.gradebook')->middleware('admin_permission');
    Route::get('admin/gradebook/list', 'gradebookList')->name('admin.gradebook.list');
    Route::get('admin/gradebook/subjec_marks/{student_id}', 'subjectWiseMarks')->name('admin.gradebook.subject_wise_marks');
    Route::get('admin/exam/mark', 'addmark')->name('admin.exam_mark.open_modal');
    Route::post('admin/exam/mark_add', 'markAdd')->name('admin.add.exam_mark');


    //Marks route
    Route::get('admin/marks', 'marks')->name('admin.marks')->middleware('admin_permission');
    Route::get('admin/marks/list', 'marksFilter')->name('admin.marks.list');
    Route::get('admin/marks/list_pdf/{section_id?}/{class_id?}/{session_id?}/{exam_category_id?}/{subject_id?}', 'marksPdf')->name('admin.marks.list_pdf');
    



    //Grade routes
    Route::get('admin/grade', 'gradeList')->name('admin.grade_list')->middleware('admin_permission');
    Route::get('admin/grade_create', 'createGrade')->name('admin.grade.open_modal');
    Route::post('admin/grade', 'gradeCreate')->name('admin.create.grade');
    Route::get('admin/grade/{id}', 'editGrade')->name('admin.edit.grade');
    Route::post('admin/grade/{id}', 'gradeUpdate')->name('admin.grade.update');
    Route::get('admin/grade/delete/{id}', 'gradeDelete')->name('admin.grade.delete');


    //promotion routes
    Route::get('admin/promotion', 'promotionFilter')->name('admin.promotion')->middleware('admin_permission');
    Route::get('admin/promotion_list', 'promotionList')->name('admin.promotion.promotion_list');
    Route::get('admin/promote/{promotion_data}', 'promote')->name('admin.promotion.promote');


    //Subject routes
    Route::get('admin/subject', 'subjectList')->name('admin.subject_list')->middleware('admin_permission');
    Route::get('admin/subject_create', 'createSubject')->name('admin.subject.open_modal');
    Route::post('admin/subject', 'subjectCreate')->name('admin.create.subject');
    Route::get('admin/subject/{id}', 'editSubject')->name('admin.edit.subject');
    Route::post('admin/subject/{id}', 'subjectUpdate')->name('admin.subject.update');
    Route::get('admin/subject/delete/{id}', 'subjectDelete')->name('admin.subject.delete');


    //Depertment routes
    Route::get('admin/department', 'departmentList')->name('admin.department_list')->middleware('admin_permission');
    Route::get('admin/department_create', 'createDepartment')->name('admin.department.open_modal');
    Route::post('admin/department', 'departmentCreate')->name('admin.create.department');
    Route::get('admin/department/{id}', 'editDepartment')->name('admin.edit.department');
    Route::post('admin/department/{id}', 'departmentUpdate')->name('admin.department.update');
    Route::get('admin/department/delete/{id}', 'departmentDelete')->name('admin.department.delete');


    //Class room routes
    Route::get('admin/class_room', 'classRoomList')->name('admin.class_room_list')->middleware('admin_permission');
    Route::get('admin/class_room_create', 'createClassRoom')->name('admin.class_room.open_modal');
    Route::post('admin/class_room', 'classRoomCreate')->name('admin.create.class_room');
    Route::get('admin/class_room/{id}', 'editClassRoom')->name('admin.edit.class_room');
    Route::post('admin/class_room/{id}', 'classRoomUpdate')->name('admin.class_room.update');
    Route::get('admin/class_room/delete/{id}', 'classRoomDelete')->name('admin.class_room.delete');


    //Class list routes
    Route::get('admin/class_list', 'classList')->name('admin.class_list')->middleware('admin_permission');
    Route::get('admin/class_create', 'createClass')->name('admin.class.open_modal');
    Route::post('admin/class', 'classCreate')->name('admin.create.class');
    Route::get('admin/class/{id}', 'editClass')->name('admin.edit.class');
    Route::post('admin/class/{id}', 'classUpdate')->name('admin.class.update');
    Route::get('admin/class/section/{id}', 'editSection')->name('admin.edit.section');
    Route::post('admin/class/sections/{id}', 'sectionUpdate')->name('admin.section.update');
    Route::get('admin/class/delete/{id}', 'classDelete')->name('admin.class.delete');


    //Accounting route
    Route::get('admin/student_fee/delete/{id}/{status}', 'update_offline_payment')->name('admin.update_offline_payment');
    Route::get('admin/subscription/payment/success/{user_data}/{response}', 'admin_subscription_fee_success_payment')->name('admin_subscription_fee_success_payment');
    Route::get('admin/subscription/payment/fail/{user_data}/{response}', 'admin_subscription_fee_fail_payment')->name('admin_subscription_fee_fail_payment');
    Route::get('admin/subscription/payment/trail', 'admin_free_subcription')->name('admin_free_subcription');
    Route::post('admin/subscription/offline/payment/{id}', 'admin_subscription_offline_payment')->name('admin.admin_subscription_offline_payment');


    //Student fee manager routes
    Route::get('admin/fee_manager', 'studentFeeManagerList')->name('admin.fee_manager.list')->middleware('admin_permission');
    Route::get('admin/student_fee_manager/export/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExport')->name('admin.fee_manager.export');
    Route::get('admin/student_fee_manager/pdf_print/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExportPdfPrint')->name('admin.fee_manager.pdf_print');
    Route::get('admin/fee_manager_create/{value}', 'createFeeManager')->name('admin.fee_manager.open_modal');
    Route::post('admin/fee_manager/{value}', 'feeManagerCreate')->name('admin.create.fee_manager');
    Route::get('admin/fee_manager/{id}', 'editFeeManager')->name('admin.edit.fee_manager');
    Route::post('admin/fee_manager_list/{id}', 'feeManagerUpdate')->name('admin.fee_manager.update');
    Route::get('admin/student_fee/delete/{id}', 'studentFeeDelete')->name('admin.fee_manager.delete');
    Route::get('admin/student_fee/invoice/{id}', 'studentFeeinvoice')->name('admin.studentFeeinvoice');
    Route::get('admin/offline_payment/pending', 'offline_payment_pending')->name('admin.offline_payment_pending')->middleware('admin_permission');


    //Expense routes
    Route::get('admin/expenses/list', 'expenseList')->name('admin.expense.list')->middleware('admin_permission');
    Route::get('admin/expenses/create', 'createExpense')->name('admin.expenses.open_modal');
    Route::post('admin/expenses/added', 'expenseCreate')->name('admin.create.expenses');
    Route::get('admin/expenses/{id}', 'editExpense')->name('admin.edit.expenses');
    Route::post('admin/expenses/{id}', 'expenseUpdate')->name('admin.expenses.update');
    Route::get('admin/expenses/delete/{id}', 'expenseDelete')->name('admin.expense.delete');


    //Expense category routes
    Route::get('admin/expense_category/list', 'expenseCategoryList')->name('admin.expense.category_list')->middleware('admin_permission');
    Route::get('admin/expense_category/create', 'createExpenseCategory')->name('admin.expense_category.open_modal');
    Route::post('admin/expense_category/added', 'expenseCategoryCreate')->name('admin.create.expense_category');
    Route::get('admin/expense_category/{id}', 'editExpenseCategory')->name('admin.edit.expense_category');
    Route::post('admin/expense_category/{id}', 'expenseCategoryUpdate')->name('admin.expense_category.update');
    Route::get('admin/expense_category/delete/{id}', 'expenseCategoryDelete')->name('admin.expense.category_delete');


    //Book routes
    Route::get('admin/book/list', 'bookList')->name('admin.book.book_list')->middleware('admin_permission');
    Route::get('admin/book/create', 'createBook')->name('admin.book.open_modal');
    Route::post('admin/book/added', 'bookCreate')->name('admin.create.book');
    Route::get('admin/book/{id}', 'editBook')->name('admin.edit.book');
    Route::post('admin/book/{id}', 'bookUpdate')->name('admin.book.update');
    Route::get('admin/book/delete/{id}', 'bookDelete')->name('admin.book.delete');


    //Issue book routes
    Route::get('admin/book_issue', 'bookIssueList')->name('admin.book_issue.list')->middleware('admin_permission');
    Route::get('admin/book_issue/create', 'createBookIssue')->name('admin.book_issue.open_modal');
    Route::post('admin/book_issue/added', 'bookIssueCreate')->name('admin.create.book_issue');
    Route::get('admin/book_issue/{id}', 'editBookIssue')->name('admin.edit.book_issue');
    Route::post('admin/book_issue/{id}', 'bookIssueUpdate')->name('admin.book_issue.update');
    Route::get('admin/book_issue/return/{id}', 'bookIssueReturn')->name('admin.book_issue.return');
    Route::get('admin/book_issue/delete/{id}', 'bookIssueDelete')->name('admin.book_issue.delete');


    //Noticeboard routes
    Route::get('admin/noticeboard', 'noticeboardList')->name('admin.noticeboard.list')->middleware('admin_permission');
    Route::get('admin/noticeboard/create', 'createNoticeboard')->name('admin.noticeboard.open_modal');
    Route::post('admin/noticeboard/added', 'noticeboardCreate')->name('admin.create.noticeboard');
    Route::get('admin/noticeboard/{id}', 'editNoticeboard')->name('admin.edit.noticeboard');
    Route::post('admin/noticeboard/{id}', 'noticeboardUpdate')->name('admin.noticeboard.update');
    Route::get('admin/noticeboard/delete/{id}', 'noticeboardDelete')->name('admin.noticeboard.delete');


    //Subscription routes
    Route::get('admin/subscription', 'subscription')->name('admin.subscription')->middleware('admin_permission');
    Route::get('admin/subscription/purchase', 'subscriptionPurchase')->name('admin.subscription.purchase');
    Route::get('admin/subscription/payment/{package_id}', 'subscriptionPayment')->name('admin.subscription.payment');
    Route::post('admin/subscription/offline_payment/{id}', 'offlinePayment')->name('admin.subscription.offline_payment');

    //Event routes
    Route::get('admin/events/list', 'eventList')->name('admin.events.list')->middleware('admin_permission');
    Route::get('admin/events/create', 'createEvent')->name('admin.events.open_modal');
    Route::post('admin/events/added', 'eventCreate')->name('admin.create.event');
    Route::get('admin/events/{id}', 'editEvent')->name('admin.edit.event');
    Route::post('admin/events/{id}', 'eventUpdate')->name('admin.event.update');
    Route::get('admin/events/delete/{id}', 'eventDelete')->name('admin.events.delete');

    //Complain List routes
    Route::get('admin/complain/complainList', 'complainList')->name('admin.complain.complainList');
    

    //Settings routes
    Route::get('admin/settings/payment', 'paymentSettings')->name('admin.settings.payment')->middleware('admin_permission');
    Route::post('admin/settings/payment/post', 'paymentSettings_post')->name('admin.settings.payment_post');
    Route::get('admin/settings/school', 'schoolSettings')->name('admin.settings.school')->middleware('admin_permission');
    Route::post('admin/settings/school', 'schoolUpdate')->name('admin.school.update');

    //Session routes
    Route::get('admin/session_manager', 'sessionManager')->name('admin.settings.session_manager')->middleware('admin_permission');
    Route::get('admin/session_manager/active_session/{id}', 'activeSession')->name('admin.session_manager.active_session');
    Route::get('admin/session_manager/create', 'createSession')->name('admin.create.session');
    Route::post('admin/session_add', 'sessionCreate')->name('admin.session_manager.create');
    Route::get('admin/session_manager/{id}', 'editSession')->name('admin.edit.session');
    Route::post('admin/session_manager/{id}', 'sessionUpdate')->name('admin.session.update');
    Route::get('admin/session_manager/delete/{id}', 'sessionDelete')->name('admin.session.delete');

    //Profile
    Route::get('admin/profile', 'profile')->name('admin.profile')->middleware('admin_permission');
    Route::post('admin/profile/update', 'profile_update')->name('admin.profile.update');
    Route::any('admin/password/{action_type}', 'password')->name('admin.password');
    Route::post('admin/language', 'user_language')->name('admin.language');

    //Account disable navigation
    //Route::get('admin/account_disableview', 'account_disableview')->name('admin.account_disableview');

    // Student Feedback
    Route::get('admin/feedback-list', 'feedback_list')->name('admin.feedback.feedback_list');
    Route::get('admin/feedback-create', 'create_feedback')->name('admin.feedback.create_feedback');
    Route::post('admin/feedback-upload', 'upload_feedback')->name('admin.feedback.upload_feedback');
    Route::get('admin/feedback-edit/{id}', 'edit_feedback')->name('admin.feedback.edit_feedback');
    Route::post('admin/feedback-update/{id}', 'update_feedback')->name('admin.feedback.update_feedback');
    Route::get('admin/feedback-delete/{id}', 'delete_feedback')->name('admin.feedback.delete_feedback');

    // Message
    Route::get('admin/message/all-message/{id}', 'allMessage')->name('admin.message.all_message');
    Route::get('admin/message/message-thrades/{id}', 'messagethrades')->name('admin.message.messagethrades');
    Route::post('admin/message/single-chat/save', 'chat_save')->name('admin.message.chat_save');
    Route::get('admin/message/chat_empty', 'chat_empty')->name('admin.message.chat_empty');



});
//Admin routes end here


//Teacher routes are here
Route::controller(TeacherController::class)->middleware('teacher','auth')->group(function () {

    Route::get('teacher/dashboard', 'teacherDashboard')->name('teacher.dashboard')->middleware('role_id');


    //Attendance routes
    Route::get('teacher/attendance', 'dailyAttendance')->name('teacher.daily_attendance');
    Route::get('teacher/take_attendance', 'takeAttendance')->name('teacher.take_attendance.open_modal');
    Route::post('teacher/attendance_take', 'attendanceTake')->name('teacher.attendance_take');
    Route::get('teacher/attendance/student', 'studentListAttendance')->name('teacher.attendance.student');
    Route::get('teacher/attendance/filter', 'dailyAttendanceFilter')->name('teacher.daily_attendance.filter');
    Route::get('teacher/attendance/csv', 'dailyAttendanceFilter_csv')->name('teacher.dailyAttendanceFilter_csv');


    //Marks routes
    Route::get('teacher/marks', 'marks')->name('teacher.marks');
    Route::get('teacher/marks/list', 'marksFilter')->name('teacher.marks.list');


    //Offline exam routes
    Route::get('teacher/offline_exam', 'offlineExamList')->name('teacher.offline_exam');
    Route::get('teacher/offline_exam/export/{id}', 'offlineExamExport')->name('teacher.offline_exam.export');
    Route::get('teacher/exam_list_by_class/{id}', 'classWiseOfflineExam')->name('teacher.class_wise_exam_list');


    //Routine routes
    Route::get('teacher/routine', 'routine')->name('teacher.routine');
    Route::get('teacher/routine/list', 'routineList')->name('teacher.routine.routine_list');


    //Subject routes
    Route::get('teacher/subject', 'subjectList')->name('teacher.subject_list');


    //Gradebook routes
    Route::get('teacher/gradebook', 'gradebook')->name('teacher.gradebook');
    Route::get('teacher/gradebook/list', 'gradebookList')->name('teacher.gradebook.list');
    Route::get('teacher/gradebook/subjec_marks/{student_id}', 'subjectWiseMarks')->name('teacher.gradebook.subject_wise_marks');


    //Syllabus routes
    Route::get('teacher/syllabus', 'list_of_syllabus')->name('teacher.list_of_syllabus');
    Route::get('teacher/class_wise_section_for_syllabus', 'class_wise_section_for_syllabus')->name('teacher.class_wise_section_for_syllabus');
    Route::get('teacher/syllabus_details', 'syllabus_details')->name('teacher.syllabus_details');
    Route::get('teacher/create/syllabus/modal', 'show_syllabus_modal')->name('teacher.show_syllabus_modal');
    Route::post('teacher/create/syllabus/modal/post', 'show_syllabus_modal_post')->name('teacher.show_syllabus_modal_post');
    Route::get('teacher/syllabus/delete/{id}', 'syllabusDelete')->name('teacher.syllabus.delete');


    //Noticeboard routes
    Route::get('teacher/noticeboard', 'noticeboardList')->name('teacher.noticeboard.list');
    Route::get('teacher/noticeboard/{id}', 'editNoticeboard')->name('teacher.edit.noticeboard');


    //Event routes
    Route::get('teacher/events/list', 'eventList')->name('teacher.events.list');


    //Profile
    Route::get('teacher/profile', 'profile')->name('teacher.profile');
    Route::post('teacher/profile/update', 'profile_update')->name('teacher.profile.update');
    Route::any('teacher/password/{action_type}', 'password')->name('teacher.password');
    Route::post('teacher/language', 'user_language')->name('teacher.language');

    // Student Feedback
    Route::get('teacher/feedback-list', 'feedback_list')->name('teacher.feedback.feedback_list');
    Route::get('teacher/feedback-create', 'create_feedback')->name('teacher.feedback.create_feedback');
    Route::post('teacher/feedback-upload', 'upload_feedback')->name('teacher.feedback.upload_feedback');
    Route::get('teacher/feedback-edit/{id}', 'edit_feedback')->name('teacher.feedback.edit_feedback');
    Route::post('teacher/feedback-update/{id}', 'update_feedback')->name('teacher.feedback.update_feedback');
    Route::get('teacher/feedback-delete/{id}', 'delete_feedback')->name('teacher.feedback.delete_feedback');

    // Message
    Route::get('teacher/message/all-message/{id}', 'allMessage')->name('teacher.message.all_message');
    Route::get('teacher/message/message-thrades/{id}', 'messagethrades')->name('teacher.message.messagethrades');
    Route::post('teacher/message/single-chat/save', 'chat_save')->name('teacher.message.chat_save');
    Route::get('teacher/message/chat_empty', 'chat_empty')->name('teacher.message.chat_empty');

});
//Teacher routes end here


//Parent routes are here
Route::controller(ParentController::class)->middleware('parent','auth')->group(function () {

    Route::get('parent/dashboard', 'parentDashboard')->name('parent.dashboard')->middleware('role_id');


    //User routes
    Route::get('parent/teacherlist', 'teacherList')->name('parent.teacherlist');
    Route::get('parent/childlist', 'childList')->name('parent.childlist');
    Route::get('parent/student/id_card/{id}', 'studentIdCardGenerate')->name('parent.student.id_card');


    //Fee manager routes
    Route::get('parent/fee_manager', 'FeeManagerList')->name('parent.fee_manager.list');
    Route::get('parent/fee_manager/payment/{id}', 'FeePayment')->name('parent.FeePayment');
    Route::get('parent/fee_manager/export/{date_from}/{date_to}/{selected_status}', 'feeManagerExport')->name('parent.fee_manager.export');
    Route::get('parent/student_fee/invoice/{id}', 'studentFeeinvoice')->name('parent.studentFeeinvoice');


    //Grade rotues
    Route::get('parent/grade', 'gradeList')->name('parent.grade_list');


    //Subject routes
    Route::get('parent/child/subjects', 'subjectList')->name('parent.subject_list');
    Route::get('parent/child/subject/list', 'subjectList_by_student_name')->name('parent.subjectList_by_student_name');


    //Syllabus routes
    Route::get('parent/child/syllabus', 'syllabusList')->name('parent.syllabus_list');
    Route::get('parent/child/syllabus/list', 'syllabusList_by_student_name')->name('parent.syllabusList_by_student_name');


    //Online payment routes
    Route::get('parent/payment/success/{user_data}/{response}', 'student_fee_success_payment')->name('parent.student_fee_success_payment');
    Route::get('parent/payment/fail/{user_data}/{response}', 'student_fee_fail_payment')->name('parent.student_fee_fail_payment');


    //Offline payment routes
    Route::post('parent/student_fee/offline_payment/{id}', 'offlinePayment')->name('parent.offline_payment');


    //Routine routes
    Route::get('parent/routine', 'routine')->name('parent.routine');
    Route::get('parent/routine/list', 'routineList')->name('parent.routine.routine_list');


    //Attendence routes
    Route::get('parent/attendence/list', 'list_of_attendence')->name('parent.list_of_attendence');
    Route::get('parent/attendance/filter', 'list_of_attendence')->name('parent.daily_attendance.filter');
    Route::get('parent/attendance/csv', 'dailyAttendanceFilter_csv')->name('parent.dailyAttendanceFilter_csv');


    //Marks routes
    Route::get('parent/marks', 'marks')->name('parent.marks');
    Route::get('parent/marks/list', 'marks_list')->name('parent.marks_list');


    //Noticeboard routes
    Route::get('parent/noticeboard', 'noticeboardList')->name('parent.noticeboard.list');
    Route::get('parent/noticeboard/{id}', 'editNoticeboard')->name('parent.edit.noticeboard');


    //Event routes
    Route::get('parent/events/list', 'eventList')->name('parent.events.list');


    //Profile
    Route::get('parent/profile', 'profile')->name('parent.profile');
    Route::post('parent/profile/update', 'profile_update')->name('parent.profile.update');
    Route::any('parent/password/{action_type}', 'password')->name('parent.password');
    Route::post('parent/language', 'user_language')->name('parent.language');

    // Student Feedback
    Route::get('parent/feedback/filter', 'filter')->name('parent.feedback.filter');
    Route::get('parent/feedback-list', 'feedback_list')->name('parent.feedback.feedback_list');

    // Message
    Route::get('parent/message/all-message/{id}', 'allMessage')->name('parent.message.all_message');
    Route::get('parent/message/message-thrades/{id}', 'messagethrades')->name('parent.message.messagethrades');
    Route::post('parent/message/single-chat/save', 'chat_save')->name('parent.message.chat_save');
    Route::get('parent/message/chat_empty', 'chat_empty')->name('parent.message.chat_empty');

});
//Parent routes end here


//Student routes are here
Route::controller(StudentController::class)->middleware('student','auth')->group(function () {

    Route::get('student/dashboard', 'studentDashboard')->name('student.dashboard')->middleware('role_id');


    //User routes
    Route::get('student/teacher', 'teacherList')->name('student.teacher');


    //Attendance routes
    Route::get('student/attendance', 'dailyAttendance')->name('student.daily_attendance');
    Route::get('student/attendance/filter', 'dailyAttendance')->name('student.daily_attendance.filter');
    Route::get('student/attendance/csv', 'dailyAttendanceFilter_csv')->name('student.dailyAttendanceFilter_csv');


    //Routine routes
    Route::get('student/routine', 'routine')->name('student.routine');


    //Subject routes
    Route::get('student/subject', 'subjectList')->name('student.subject_list');


    //Syllabus routes
    Route::get('student/syllabus', 'syllabus')->name('student.syllabus');


    //Grade routes
    Route::get('student/grade', 'gradeList')->name('student.grade_list');


    //Book routes
    Route::get('student/book/list', 'bookList')->name('student.book.book_list');
    Route::get('student/book_issue', 'bookIssueList')->name('student.book.issued_list');


    //Marks routes
    Route::get('student/marks', 'marks')->name('student.marks');


    //Noticeboard routes
    Route::get('student/noticeboard', 'noticeboardList')->name('student.noticeboard.list');
    Route::get('student/noticeboard/{id}', 'editNoticeboard')->name('student.edit.noticeboard');


    //Event routes
    Route::get('student/events/list', 'eventList')->name('student.events.list');

    //Complain routes
    Route::get('student/complain/complain', 'complain')->name('student.complain.complain');
    Route::get('student/complain/complainUser', 'complainUser')->name('student.complain.complainUser');

    //Fee manager routes
    Route::get('student/fee_manager', 'FeeManagerList')->name('student.fee_manager.list');
    Route::get('student/fee_manager/payment/{id}', 'FeePayment')->name('student.FeePayment');
    Route::get('student/fee_manager/export/{date_from}/{date_to}/{selected_status}', 'feeManagerExport')->name('student.fee_manager.export');
    Route::get('student/payment/success/{user_data}/{response}', 'student_fee_success_payment_student')->name('student.student_fee_success_payment_student');
    Route::get('student/payment/fail/{user_data}/{response}', 'student_fee_fail_payment_student')->name('student.student_fee_fail_payment_student');
    Route::post('student/student_fee/offline_payment/{id}', 'offlinePaymentStudent')->name('student.offline_payment');
    Route::get('student/student_fee/invoice/{id}', 'studentFeeinvoice')->name('student.studentFeeinvoice');

    //Profile
    Route::get('student/profile', 'profile')->name('student.profile');
    Route::post('student/profile/update', 'profile_update')->name('student.profile.update');
    Route::any('student/password/{action_type}', 'password')->name('student.password');
    Route::post('student/language', 'user_language')->name('student.language');

    // Message
    Route::get('student/message/all-message/{id}', 'allMessage')->name('student.message.all_message');
    Route::get('student/message/message-thrades/{id}', 'messagethrades')->name('student.message.messagethrades');
    Route::post('student/message/single-chat/save', 'chat_save')->name('student.message.chat_save');
    Route::get('student/message/chat_empty', 'chat_empty')->name('student.message.chat_empty');

});
//Student routes end here


//Common routes are here
Route::controller(CommonController::class)->middleware('auth')->group(function () {

    //Filter object routes
    Route::get('section/{id}', 'classWiseSections')->name('class_wise_sections');
    Route::get('subjects/{id}', 'classWiseSubject')->name('class_wise_subject');
    Route::get('students/{id}', 'classWiseStudents')->name('class_wise_student');
    Route::get('class/section/{id}', 'sectionWiseStudents')->name('section_wise_students');
    Route::get('class/section/student/{id}', 'studentWiseParent')->name('student_wise_parent');

    //Grade crud routes
    Route::get('grade/get/{exam_mark}', 'getGrade')->name('get.grade');
    Route::get('mark/update', 'markUpdate')->name('update.mark');


    Route::get('user/{id}', 'idWiseUserName')->name('id_wise_user_name');

});
//Common routes end here


//Accountant routes are here
Route::controller(AccountantController::class)->middleware('accountant','auth')->group(function () {

    Route::get('accountant/dashboard', 'accountantDashboard')->name('accountant.dashboard')->middleware('role_id');


    //Fee manager routes
    Route::get('accountant/student_fee_manager', 'studentFeeManagerList')->name('accountant.fee_manager.list');
    Route::get('accountant/student_fee_manager/export/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExport')->name('accountant.fee_manager.export');
    Route::get('accountant/student_fee_manager/pdf_print/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExportPdfPrint')->name('accountant.fee_manager.pdf_print');
    Route::get('accountant/fee_manager_create/{value}', 'createFeeManager')->name('accountant.fee_manager.open_modal');
    Route::post('accountant/fee_manager/{value}', 'feeManagerCreate')->name('accountant.create.fee_manager');
    Route::get('accountant/fee_manager/{id}', 'editFeeManager')->name('accountant.edit.fee_manager');
    Route::post('accountant/fee_manager_list/{id}', 'feeManagerUpdate')->name('accountant.fee_manager.update');
    Route::get('accountant/student_fee/delete/{id}', 'studentFeeDelete')->name('accountant.fee_manager.delete');
    Route::get('accountant/student_fee/invoice/{id}', 'studentFeeinvoice')->name('accountant.studentFeeinvoice');


    //Offline payment routes
    Route::get('accountant/offline_payment/pending', 'offline_payment_pending')->name('accountant.offline_payment_pending');
    Route::get('accountant/student_fee/delete/{id}/{status}', 'update_offline_payment')->name('accountant.update_offline_payment');

    //Expenses routes
    Route::get('accountant/expenses/list', 'expenseList')->name('accountant.expense.list');
    Route::get('accountant/expenses/create', 'createExpense')->name('accountant.expenses.open_modal');
    Route::post('accountant/expenses/added', 'expenseCreate')->name('accountant.create.expenses');
    Route::get('accountant/expenses/{id}', 'editExpense')->name('accountant.edit.expenses');
    Route::post('accountant/expenses/{id}', 'expenseUpdate')->name('accountant.expenses.update');
    Route::get('accountant/expenses/delete/{id}', 'expenseDelete')->name('accountant.expense.delete');

    //Expenses category routes
    Route::get('accountant/expense_category/list', 'expenseCategoryList')->name('accountant.expense.category_list');
    Route::get('accountant/expense_category/create', 'createExpenseCategory')->name('accountant.expense_category.open_modal');
    Route::post('accountant/expense_category/added', 'expenseCategoryCreate')->name('accountant.create.expense_category');
    Route::get('accountant/expense_category/{id}', 'editExpenseCategory')->name('accountant.edit.expense_category');
    Route::post('accountant/expense_category/{id}', 'expenseCategoryUpdate')->name('accountant.expense_category.update');
    Route::get('accountant/expense_category/delete/{id}', 'expenseCategoryDelete')->name('accountant.expense.category_delete');


    //Noticeboard routes
    Route::get('accountant/noticeboard', 'noticeboardList')->name('accountant.noticeboard.list');
    Route::get('accountant/noticeboard/{id}', 'editNoticeboard')->name('accountant.edit.noticeboard');


    //Event routes
    Route::get('accountant/events/list', 'eventList')->name('accountant.events.list');


    //Profile
    Route::get('accountant/profile', 'profile')->name('accountant.profile');
    Route::post('accountant/profile/update', 'profile_update')->name('accountant.profile.update');
    Route::any('accountant/password/{action_type}', 'password')->name('accountant.password');
    Route::post('accountant/language', 'user_language')->name('accountant.language');

    //Route::get('accountant/account_disable', 'account_disable')->name('accountant.account_disable');
    
    // Message
    Route::get('accountant/message/all-message/{id}', 'allMessage')->name('accountant.message.all_message');
    Route::get('accountant/message/message-thrades/{id}', 'messagethrades')->name('accountant.message.messagethrades');
    Route::post('accountant/message/single-chat/save', 'chat_save')->name('accountant.message.chat_save');
    Route::get('accountant/message/chat_empty', 'chat_empty')->name('accountant.message.chat_empty');
});

//Accountant routes end here


//Librarian routes are here
Route::controller(LibrarianController::class)->middleware('librarian','auth')->group(function () {

    Route::get('librarian/dashboard', 'librarianDashboard')->name('librarian.dashboard')->middleware('role_id');


    //Book routes
    Route::get('librarian/book/list', 'bookList')->name('librarian.book.book_list');
    Route::get('librarian/book/create', 'createBook')->name('librarian.book.open_modal');
    Route::post('librarian/book/added', 'bookCreate')->name('librarian.create.book');
    Route::get('librarian/book/{id}', 'editBook')->name('librarian.edit.book');
    Route::post('librarian/book/{id}', 'bookUpdate')->name('librarian.book.update');
    Route::get('librarian/book/delete/{id}', 'bookDelete')->name('librarian.book.delete');

    //Book issue routes
    Route::get('librarian/book_issue', 'bookIssueList')->name('librarian.book_issue.list');
    Route::get('librarian/book_issue/create', 'createBookIssue')->name('librarian.book_issue.open_modal');
    Route::post('librarian/book_issue/added', 'bookIssueCreate')->name('librarian.create.book_issue');
    Route::get('librarian/book_issue/{id}', 'editBookIssue')->name('librarian.edit.book_issue');
    Route::post('librarian/book_issue/{id}', 'bookIssueUpdate')->name('librarian.book_issue.update');
    Route::get('librarian/book_issue/return/{id}', 'bookIssueReturn')->name('librarian.book_issue.return');
    Route::get('librarian/book_issue/delete/{id}', 'bookIssueDelete')->name('librarian.book_issue.delete');


    //Noticeboard routes
    Route::get('librarian/noticeboard', 'noticeboardList')->name('librarian.noticeboard.list');
    Route::get('librarian/noticeboard/{id}', 'editNoticeboard')->name('librarian.edit.noticeboard');


    //Event routes
    Route::get('librarian/events/list', 'eventList')->name('librarian.events.list');
    

    //Profile
    Route::get('librarian/profile', 'profile')->name('librarian.profile');
    Route::post('librarian/profile/update', 'profile_update')->name('librarian.profile.update');
    Route::any('librarian/password/{action_type}', 'password')->name('librarian.password');
    Route::post('librarian/language', 'user_language')->name('librarian.language');

    // Message
    Route::get('librarian/message/all-message/{id}', 'allMessage')->name('librarian.message.all_message');
    Route::get('librarian/message/message-thrades/{id}', 'messagethrades')->name('librarian.message.messagethrades');
    Route::post('librarian/message/single-chat/save', 'chat_save')->name('librarian.message.chat_save');
    Route::get('librarian/message/chat_empty', 'chat_empty')->name('librarian.message.chat_empty');

});
//Librarian routes end here


//Updater routes are here
Route::controller(Updater::class)->middleware('superAdmin','auth')->group(function () {

    Route::post('superadmin/addon/create', 'update')->name('superadmin.addon.create');
    Route::post('superadmin/addon/update', 'update')->name('superadmin.addon.update');
    Route::post('superadmin/product/update', 'update')->name('superadmin.product.update');

});
//Updater routes end here


//Installation routes are here
Route::controller(InstallController::class)->group(function () {

    if (\Illuminate\Support\Facades\DB::connection()->getDatabaseName() == 'db_name') {
        Route::get('/', 'index');
    }

    Route::get('install/step0', 'step0')->name('step0');
    Route::get('install/step1', 'step1')->name('step1');
    Route::get('install/step2', 'step2')->name('step2');
    Route::any('install/step3', 'step3')->name('step3');
    Route::get('install/step4', 'step4')->name('step4');
    Route::get('install/step4/{confirm_import}', 'confirmImport')->name('step4.confirm_import');
    Route::get('install/install', 'confirmInstall')->name('confirm_install');
    Route::post('install/validate', 'validatePurchaseCode')->name('install.validate');
    Route::any('install/finalizing_setup', 'finalizingSetup')->name('finalizing_setup');
    Route::get('install/success', 'success')->name('success');

});
//Installation routes end here
