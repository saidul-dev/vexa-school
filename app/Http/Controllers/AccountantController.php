<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\StudentFeeManager;
use App\Models\Session;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\AccountHead;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherLine;
use App\Models\Income;
use App\Services\AccountingService;
use App\Models\Enrollment;
use App\Models\user;
use App\Models\Noticeboard;
use App\Models\FrontendEvent;
use App\Models\MessageThrade;
use App\Models\Chat;

use Illuminate\Support\Facades\DB;

class AccountantController extends Controller
{
    /**
     * Show the accountant dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function accountantDashboard()
    {
        return view('accountant.dashboard');
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

            return view('accountant.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);

         } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('accountant.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
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

        return view('accountant.student_fee_manager.pdf_print', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);


    }

    public function createFeeManager($value="")
    {

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if($value == 'single'){
            return view('accountant.student_fee_manager.single', ['classes' => $classes]);
        } else if($value == 'mass'){
            return view('accountant.student_fee_manager.mass', ['classes' => $classes]);
        }
    }

    private function syncStudentFeeVoucher($invoice, $previousPaidAmount)
    {
        $accountingService = new AccountingService();

        if ($invoice->status != 'paid') {
            $accountingService->voidVoucherFor('student_fee', $invoice->id);
            return;
        }

        $delta = $invoice->paid_amount - $previousPaidAmount;

        if ($delta == 0) {
            return;
        }

        if ($delta < 0) {
            $accountingService->voidVoucherFor('student_fee', $invoice->id);
            if ($invoice->paid_amount <= 0) {
                return;
            }
            $delta = $invoice->paid_amount;
        }

        $assetHead = $accountingService->resolveAssetHead(auth()->user()->school_id, $invoice->payment_method);
        $incomeHead = $invoice->account_head_id ? $invoice->account_head_id : optional($accountingService->defaultStudentFeeIncomeHead(auth()->user()->school_id))->id;

        if (!$assetHead || !$incomeHead) {
            return;
        }

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $accountingService->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'receipt',
            date('Y-m-d'),
            $invoice->title,
            $assetHead->id,
            $incomeHead,
            $delta,
            'student_fee',
            $invoice->id,
            auth()->user()->id
        );
    }

    public function feeManagerCreate(Request $request, $value="")
    {
        $data = $request->all();

        if($value == 'single'){

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


            $invoice = StudentFeeManager::create($data);
            $this->syncStudentFeeVoucher($invoice, 0);

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
                $invoice = StudentFeeManager::create($data);
                $this->syncStudentFeeVoucher($invoice, 0);
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

    public function editFeeManager($id='')
    {
        $invoice_details = StudentFeeManager::find($id);
        $enrollments = Enrollment::get()->where('class_id', $invoice_details->class_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('accountant.student_fee_manager.edit', ['invoice_details' => $invoice_details, 'classes' => $classes, 'enrollments' => $enrollments]);
    }

    public function feeManagerUpdate(Request $request, $id='')
    {
        $data = $request->all();

        /*GET THE PREVIOUS INVOICE DETAILS FOR GETTING THE PAID AMOUNT*/
        $previous_invoice_data = StudentFeeManager::find($id);

        if ($data['paid_amount'] > $data['total_amount']) {

            return redirect()->back()->with('error','Paid amount can not get bigger than total amount');
        }
        if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {
            return redirect()->back()->with('error','Paid amount is not equal to total amount');
        }

        /*KEEPING TRACK OF PAYMENT DATE*/
        if ($data['paid_amount'] != $previous_invoice_data && $data['paid_amount'] > 0) {
            $timestamp = strtotime(date('d-M-Y'));
        }elseif ($data['paid_amount'] == 0 || $data['paid_amount'] == "") {
            $timestamp = 0;
        }

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        StudentFeeManager::where('id', $id)->update([
            'title' => $data['title'],
            'total_amount' => $data['total_amount'],
            'class_id' => $data['class_id'],
            'student_id' => $data['student_id'],
            'paid_amount' => $data['paid_amount'],
            'payment_method' => $data['payment_method'],
            'timestamp' => $timestamp,
            'status' => $data['status'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        $this->syncStudentFeeVoucher(StudentFeeManager::find($id), $previous_invoice_data['paid_amount']);

        return redirect()->back()->with('message','You have successfully update invoice.');
    }

    public function studentFeeDelete($id)
    {
        $invoice = StudentFeeManager::find($id);
        $invoice->delete();
        return redirect()->back()->with('message','You have successfully delete invoice.');
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
            $payment_method = 'offline';



            if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('payment_method', $payment_method)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('payment_method', $payment_method)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('accountant.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);

         } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $selected_class = "";
            $selected_status = 'pending';
            $payment_method = 'offline';
            $invoices = StudentFeeManager::where('status','pending')->where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('payment_method', $payment_method)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('accountant.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
         }


    }

    public function update_offline_payment($id,$status)
    {
        $amount= StudentFeeManager::find($id)->first()->toArray();
        $amount=$amount['total_amount'];
        $previous_paid_amount = StudentFeeManager::find($id)->paid_amount;

        if($status=='approve')
        {
            StudentFeeManager::where('id', $id)->update([
                'status' => 'paid',
                'updated_at'=>date("Y-m-d H:i:s"),
                'paid_amount' =>$amount,
                'payment_method' => 'offline']);

            $this->syncStudentFeeVoucher(StudentFeeManager::find($id), $previous_paid_amount);

                return redirect()->back()->with('message','Payment Approved');
        }
        elseif($status=='decline')
        {
            StudentFeeManager::where('id',$id)->update([
                'status' => 'unpaid',
                'updated_at'=>date("Y-m-d H:i:s"),
                'paid_amount' =>$amount,
                'payment_method' => 'offline']);

            $this->syncStudentFeeVoucher(StudentFeeManager::find($id), $previous_paid_amount);

                return redirect()->back()->with('message','Payment Decline');


        }


    }

    public function studentFeeinvoice(Request $request, $id)
    {
        $invoice_details=StudentFeeManager::find($id)->toArray();
        $student_details = (new CommonController)->get_student_details_by_id($invoice_details['student_id'])->toArray();


      return view('accountant.student_fee_manager.invoice',['invoice_details' => $invoice_details,'student_details' => $student_details]);
    }


    /**
     * Show the expense expense list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseList(Request $request)
    {
        if(count($request->all()) > 0){
            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $account_head_id = $data['account_head_id'];

            $account_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'expense')->get();
            $selected_head = AccountHead::find($account_head_id);
            if($account_head_id != 'all'){
                $expenses = Expense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where(['account_head_id' => $account_head_id, 'school_id' => auth()->user()->school_id])->get();
            } else {
                $expenses = Expense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('school_id', auth()->user()->school_id)->get();
            }

            return view('accountant.expenses.expense_manager', ['account_heads' => $account_heads, 'expenses' => $expenses, 'selected_head' => $selected_head, 'date_from' => $date_from, 'date_to' => $date_to]);

        } else {
            $account_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'expense')->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $expenses = Expense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('school_id', auth()->user()->school_id)->get();
            $selected_head = "";
            return view('accountant.expenses.expense_manager', ['account_heads' => $account_heads, 'expenses' => $expenses, 'selected_head' => $selected_head, 'date_from' => $date_from, 'date_to' => $date_to]);
        }
    }

    public function createExpense()
    {
        $expense_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'expense')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('accountant.expenses.create', ['expense_heads' => $expense_heads, 'asset_heads' => $asset_heads]);
    }

    public function expenseCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $expense = Expense::create([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        (new AccountingService())->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'payment',
            date('Y-m-d', $expense->date),
            $expense->title,
            $data['account_head_id'],
            $data['payment_account_head_id'],
            $data['amount'],
            'expense',
            $expense->id,
            auth()->user()->id
        );

        return redirect()->back()->with('message','You have successfully create a new expense.');
    }

    public function editExpense($id)
    {
        $expense_details = Expense::find($id);
        $expense_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'expense')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('accountant.expenses.edit', ['expense_heads' => $expense_heads, 'asset_heads' => $asset_heads, 'expense_details' => $expense_details]);
    }

    public function expenseUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::where('id', $id)->update([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        $accountingService = new AccountingService();
        $accountingService->voidVoucherFor('expense', $id);
        $accountingService->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'payment',
            date('Y-m-d', strtotime($data['date'])),
            $data['title'],
            $data['account_head_id'],
            $data['payment_account_head_id'],
            $data['amount'],
            'expense',
            $id,
            auth()->user()->id
        );

        return redirect()->back()->with('message','You have successfully update expense.');
    }

    public function expenseDelete($id)
    {
        $expense = Expense::find($id);
        (new AccountingService())->voidVoucherFor('expense', $id);
        $expense->delete();
        return redirect()->back()->with('message','You have successfully delete expense.');
    }

    public function incomeList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if (count($request->all()) > 0) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to = strtotime($date[1].' 23:59:59');
        } else {
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')).' 23:59:59');
        }

        $incomes = Income::where('date', '>=', $date_from)
            ->where('date', '<=', $date_to)
            ->where('school_id', auth()->user()->school_id)
            ->where('session_id', $active_session)
            ->get();

        return view('accountant.incomes.income_manager', ['incomes' => $incomes, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function createIncome()
    {
        $income_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'income')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('accountant.incomes.create', ['income_heads' => $income_heads, 'asset_heads' => $asset_heads]);
    }

    public function incomeCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $income = Income::create([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        (new AccountingService())->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'receipt',
            date('Y-m-d', $income->date),
            $income->title,
            $data['payment_account_head_id'],
            $data['account_head_id'],
            $data['amount'],
            'income',
            $income->id,
            auth()->user()->id
        );

        return redirect()->back()->with('message', 'You have successfully create a new income.');
    }

    public function editIncome($id)
    {
        $income_details = Income::find($id);
        $income_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'income')->get();
        $asset_heads = AccountHead::where('school_id', auth()->user()->school_id)->where('type', 'asset')->get();
        return view('accountant.incomes.edit', ['income_heads' => $income_heads, 'asset_heads' => $asset_heads, 'income_details' => $income_details]);
    }

    public function incomeUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Income::where('id', $id)->update([
            'title' => $data['title'],
            'account_head_id' => $data['account_head_id'],
            'payment_account_head_id' => $data['payment_account_head_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        $accountingService = new AccountingService();
        $accountingService->voidVoucherFor('income', $id);
        $accountingService->recordVoucher(
            auth()->user()->school_id,
            $active_session,
            'receipt',
            date('Y-m-d', strtotime($data['date'])),
            $data['title'],
            $data['payment_account_head_id'],
            $data['account_head_id'],
            $data['amount'],
            'income',
            $id,
            auth()->user()->id
        );

        return redirect()->back()->with('message', 'You have successfully update income.');
    }

    public function incomeDelete($id)
    {
        $income = Income::find($id);
        (new AccountingService())->voidVoucherFor('income', $id);
        $income->delete();
        return redirect()->back()->with('message', 'You have successfully delete income.');
    }

    public function receiptsPaymentsStatement(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $from = $request->input('from', date('Y-m-01'));
        $to = $request->input('to', date('Y-m-t'));

        $lines = AccountVoucherLine::whereHas('voucher', function ($query) use ($schoolId, $from, $to) {
                $query->where('school_id', $schoolId)->whereBetween('voucher_date', [$from, $to]);
            })
            ->with(['voucher', 'accountHead'])
            ->get()
            ->sortBy(function ($line) { return $line->voucher->voucher_date; });

        $heads = AccountHead::where('school_id', $schoolId)->get();

        $summary = $heads->map(function ($head) use ($from, $to) {
            $priorMovement = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($from) {
                    $query->where('voucher_date', '<', $from);
                })
                ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as net')
                ->value('net');

            $openingSigned = $head->opening_balance_type == 'debit' ? $head->opening_balance : -$head->opening_balance;
            $opening = $openingSigned + (float) $priorMovement;

            $periodDebit = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($from, $to) {
                    $query->whereBetween('voucher_date', [$from, $to]);
                })->sum('debit');

            $periodCredit = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($from, $to) {
                    $query->whereBetween('voucher_date', [$from, $to]);
                })->sum('credit');

            $closing = $opening + $periodDebit - $periodCredit;

            return [
                'head' => $head,
                'opening' => $opening,
                'debit' => $periodDebit,
                'credit' => $periodCredit,
                'closing' => $closing,
            ];
        });

        return view('accountant.reports.receipts_payments', ['lines' => $lines, 'summary' => $summary, 'from' => $from, 'to' => $to]);
    }

    public function trialBalance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $asOf = $request->input('as_of', date('Y-m-d'));

        $heads = AccountHead::where('school_id', $schoolId)->where('status', 'active')->get();

        $rows = $heads->map(function ($head) use ($asOf) {
            $movement = AccountVoucherLine::where('account_head_id', $head->id)
                ->whereHas('voucher', function ($query) use ($asOf) {
                    $query->where('voucher_date', '<=', $asOf);
                })
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();

            $openingSigned = $head->opening_balance_type == 'debit' ? $head->opening_balance : -$head->opening_balance;
            $net = $openingSigned + (float) $movement->debit - (float) $movement->credit;

            return [
                'head' => $head,
                'debit' => $net > 0 ? $net : 0,
                'credit' => $net < 0 ? abs($net) : 0,
            ];
        });

        $totals = [
            'debit' => $rows->sum('debit'),
            'credit' => $rows->sum('credit'),
        ];

        return view('accountant.reports.trial_balance', ['rows' => $rows, 'totals' => $totals, 'as_of' => $asOf]);
    }


    /**
     * Show the expense category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseCategoryList()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('accountant.expense_category.expense_category_list', compact('expense_categories'));
    }

    public function createExpenseCategory()
    {
        return view('accountant.expense_category.create');
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
        return view('accountant.expense_category.edit', ['expense_category' => $expense_category]);
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

    public function accountHeadList()
    {
        $account_heads = AccountHead::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('accountant.account_heads.list', compact('account_heads'));
    }

    public function createAccountHead()
    {
        return view('accountant.account_heads.create');
    }

    public function accountHeadCreate(Request $request)
    {
        $data = $request->all();

        AccountHead::create([
            'school_id' => auth()->user()->school_id,
            'name' => $data['name'],
            'type' => $data['type'],
            'opening_balance' => $data['opening_balance'] ?? 0,
            'opening_balance_type' => $data['opening_balance_type'] ?? 'debit',
            'is_system' => false,
            'status' => 'active',
        ]);

        return redirect()->back()->with('message', 'You have successfully created a new account head.');
    }

    public function editAccountHead($id)
    {
        $account_head = AccountHead::where('school_id', auth()->user()->school_id)->findOrFail($id);
        return view('accountant.account_heads.edit', ['account_head' => $account_head]);
    }

    public function accountHeadUpdate(Request $request, $id)
    {
        $data = $request->all();
        $account_head = AccountHead::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $account_head->update([
            'name' => $data['name'],
            'type' => $account_head->is_system ? $account_head->type : $data['type'],
            'opening_balance' => $data['opening_balance'] ?? 0,
            'opening_balance_type' => $data['opening_balance_type'] ?? 'debit',
        ]);

        return redirect()->back()->with('message', 'You have successfully updated account head.');
    }

    public function accountHeadDelete($id)
    {
        $account_head = AccountHead::where('school_id', auth()->user()->school_id)->findOrFail($id);

        if ($account_head->is_system) {
            return redirect()->back()->with('error', 'This is a default account head and cannot be deleted.');
        }

        $account_head->delete();

        return redirect()->back()->with('message', 'You have successfully deleted account head.');
    }

    function profile(){
        return view('accountant.profile.view');
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
        
        return redirect(route('accountant.profile'))->with('message', get_phrase('Profile info updated successfully'));
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

            return redirect(route('accountant.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('accountant.profile.password');
    }

    public function noticeboardList()
    {

        $notices = Noticeboard::get()->where('school_id', auth()->user()->school_id);

        $events = array();

        foreach ($notices as $notice) {
            if ($notice['end_date'] != "") {
                if ($notice['start_date'] != $notice['end_date']) {
                    $end_date = strtotime($notice['end_date']) + 24 * 60 * 60;
                    $end_date = date('Y-m-d', $end_date);
                } else {
                    $end_date = date('Y-m-d', strtotime($notice['end_date']));
                }
            }

            if ($notice['end_date'] == "" && $notice['start_time'] == "" && $notice['end_time'] == "") {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            } else if ($notice['start_time'] != "" && ($notice['end_date'] == "" && $notice['end_time'] == "")) {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])) . 'T' . $notice['start_time']
                );
            } else if ($notice['end_date'] != "" && ($notice['start_time'] == "" && $notice['end_time'] == "")) {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])),
                    'end' => $end_date
                );
            } else if ($notice['end_date'] != "" && $notice['start_time'] != "" && $notice['end_time'] != "") {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])) . 'T' . $notice['start_time'],
                    'end' => date('Y-m-d', strtotime($notice['end_date'])) . 'T' . $notice['end_time']
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

        return view('accountant.noticeboard.noticeboard', ['events' => $events]);
    }

    public function editNoticeboard($id = "")
    {
        $notice = Noticeboard::find($id);
        return view('accountant.noticeboard.edit', ['notice' => $notice]);
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

        return view('accountant.events.events', compact('events', 'search'));
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
                             <a href="' . route('accountant.message.messagethrades', ['id' => $user->id]).'">
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
 
        
        if($counter_condition->sender_id != auth()->user()->id){
             Chat::where('message_thrade', $id)->update(['read_status' => 1]);
         }
         
         return view('accountant.message.all_message', ['msg_user_details' => $msg_user_details], ['chat_datas' => $chat_datas]);
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
                 return view('accountant.message.all_message', ['id' => $msg_trd_id, 'msg_user_details' => $msg_user_details, 'chat_datas' => $chat_datas,]);
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
                         <a href="' . route('accountant.message.messagethrades', ['id' => $user->id]).'">
                             <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                             <span class="ms-3">' . $user->name . '</span>
                         </a>
                     </div>
                 ';
             }
 
             return response()->json($html);
         }
 
         // Pass the data to the view only if msg_user_details is not null
         return view('accountant.message.chat_empty');
     }
 

}
