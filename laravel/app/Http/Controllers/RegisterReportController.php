<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CustomValidator\Validator as CustomValidator;
use Barryvdh\DomPDF\Facade as PDF;
use App\PaymentSlip;
use App\Repositories\RegisterReportRepository;
use App\Repositories\OfficeRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;

class RegisterReportController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new RegisterReportRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Payment slips
    |--------------------------------------------------------------------------
    */

    //get payment slips
    public function getPaymentSlips(Request $request)
    {
        //get search parameter
        $office = $request->office;

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getPaymentSlips method from RegisterReportRepository to get payment slips
        $this->repo = new RegisterReportRepository;
        $slips = $this->repo->getPaymentSlips($office);

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $slips['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registerReports.paymentSlips.list', ['offices' => $offices['data'], 'office' => $office,
            'slips' => $slips['data']]);
    }

    //add payment slip
    public function addPaymentSlip()
    {
        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getUserSettings method from UserRepository to get user settings
        $this->repo = new UserRepository;
        $user_settings = $this->repo->getUserSettings();

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $user_settings['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registerReports.paymentSlips.addSlip', ['offices' => $offices['data'],
            'location' => $user_settings['location']]);
    }

    //insert payment slip
    public function insertPaymentSlip(Request $request)
    {
        $payer = $request->payer;
        $office = $request->office;
        $location = $request->location;
        $item = $request->item;
        $description = $request->description;
        $sum = $request->sum;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), PaymentSlip::validateSlipForm($company_id, $office));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call insertPaymentSlip method from RegisterReportRepository to insert payment slip
        $response = $this->repo->insertPaymentSlip('F', $payer, $office, $location, $item, $description, $sum, 0, 0);

        return response()->json($response);
    }

    //delete payment slip
    public function deletePaymentSlip($id)
    {
        //call deletePaymentSlip method from RegisterReportRepository to delete payment slip
        $response = $this->repo->deletePaymentSlip($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetPaymentSlips')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetPaymentSlips')->with('success_message', trans('main.payment_slip_delete'));
    }

    //pdf payment slip
    public function pdfPaymentSlip($id)
    {
        //call paymentSlipPdfData method from RegisterReportRepository to get payment slip pdf data
        $data = $this->repo->paymentSlipPdfData($id);

        //if response status = 0 return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.registerReports.paymentSlips.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['payment_slip']->payment_slip_id.'.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | Payout slips
    |--------------------------------------------------------------------------
    */

    //get payout slips
    public function getPayoutSlips(Request $request)
    {
        //get search parameter
        $office = $request->office;

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getPayoutSlips method from RegisterReportRepository to get payout slips
        $this->repo = new RegisterReportRepository;
        $slips = $this->repo->getPayoutSlips($office);

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $slips['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registerReports.payoutSlips.list', ['offices' => $offices['data'], 'office' => $office,
            'slips' => $slips['data']]);
    }

    //add payout slip
    public function addPayoutSlip()
    {
        //call getEmployeesSelect method from EmployeeRepository to get employees - select
        $this->repo = new EmployeeRepository;
        $employees = $this->repo->getEmployeesSelect();

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getUserSettings method from UserRepository to get user settings
        $this->repo = new UserRepository;
        $user_settings = $this->repo->getUserSettings();

        //if response status = '0' show error page
        if ($employees['data'] == 0 || $offices['status'] == 0 || $user_settings['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registerReports.payoutSlips.addSlip', ['employees' => $employees['data'],
            'offices' => $offices['data'], 'location' => $user_settings['location']]);
    }

    //insert payout slip
    public function insertPayoutSlip(Request $request)
    {
        $employee = $request->employee;
        $office = $request->office;
        $income = $request->income;
        $location = $request->location;
        $note = $request->note;
        $items = $request->items;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::payoutSlips($company_id, $office);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call insertPayoutSlip method from RegisterReportRepository to insert payout slip
        $response = $this->repo->insertPayoutSlip($employee, $office, $income, $location, $note, $items);

        return response()->json($response);
    }

    //edit payout slip
    public function editPayoutSlip($id)
    {
        //call getEmployeesSelect method from EmployeeRepository to get employees - select
        $this->repo = new EmployeeRepository;
        $employees = $this->repo->getEmployeesSelect();

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getPayoutSlipDetails method from RegisterReportRepository to get payout slip details
        $this->repo = new RegisterReportRepository;
        $slip = $this->repo->getPayoutSlipDetails($id);

        //if response status = '0' return error message
        if ($employees['status'] == 0 || $offices['status'] == 0 || $slip['status'] == 0)
        {
            return redirect()->route('GetPayoutSlips')->with('error_message', trans('errors.error'));
        }

        return view('app.registerReports.payoutSlips.editSlip', ['employees' => $employees['data'],
            'offices' => $offices['data'], 'slip' => $slip['slip'], 'items' => $slip['items']]);
    }

    //update payout slip
    public function updatePayoutSlip(Request $request)
    {
        $id = $request->id;
        $employee = $request->employee;
        $office = $request->office;
        $income = $request->income;
        $location = $request->location;
        $note = $request->note;
        $items = $request->items;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::payoutSlips($company_id, $office, $id);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call updatePayoutSlip method from RegisterReportRepository to update payout slip
        $response = $this->repo->updatePayoutSlip($id, $employee, $income, $location, $note, $items);

        return response()->json($response);
    }

    //delete payout slip
    public function deletePayoutSlip($id)
    {
        //call deletePayoutSlip method from RegisterReportRepository to delete payout slip
        $response = $this->repo->deletePayoutSlip($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetPayoutSlips')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetPayoutSlips')->with('success_message', trans('main.payout_slip_delete'));
    }

    //delete payout slip item
    public function deletePayoutSlipItem(Request $request)
    {
        $slip_id = $request->slip_id;
        $item_id = $request->item_id;

        //call deletePayoutSlipItem method from RegisterReportRepository to delete payout slip item
        $response = $this->repo->deletePayoutSlipItem($slip_id, $item_id);

        return response()->json($response);
    }

    //pdf payout slip
    public function pdfPayoutSlip($id)
    {
        //call payoutSlipPdfData method from RegisterReportRepository to get payout slip pdf data
        $data = $this->repo->payoutSlipPdfData($id);

        //if response status = 0 return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.registerReports.payoutSlips.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['payout_slip']->payout_slip_id.'.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | Register reports
    |--------------------------------------------------------------------------
    */

    //get register reports
    public function getRegisterReports(Request $request)
    {
        //get search parameter
        $office = $request->office;

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $report_offices = $this->repo->getOfficesSelect();

        //call getCurrentRegisterSum method from RegisterReportRepository to get current register sum
        $this->repo = new RegisterReportRepository;
        $register_sum = $this->repo->getCurrentRegisterSum($office);

        //call getPRegisterReports method from RegisterReportRepository to get register reports
        $reports = $this->repo->getRegisterReports($office);

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $register_sum['status'] == 0 || $reports['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registerReports.reports.list', ['offices' => $offices['data'],
            'report_offices' => $report_offices['data'], 'office' => $office, 'register_sum' => $register_sum['data'],
            'reports' => $reports['data']]);
    }

    //insert register report
    public function insertRegisterReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office = $request->office;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::registerReports($company_id);

        //if form input is not correct return error message
        if ($validator['status'] != 1)
        {
            return response()->json($validator);
        }

        //call insertRegisterReport method from RegisterReportRepository to insert register report
        $response = $this->repo->insertRegisterReport($start_date, $end_date, $office);

        return response()->json($response);
    }

    //preview register report
    public function previewRegisterReport($id)
    {
        //call getRegisterReportDetails method from RegisterReportRepository to get register report details
        $report = $this->repo->getRegisterReportDetails($id);

        //if response status = '0' return error message
        if ($report['status'] == 0)
        {
            return redirect()->route('GetRegisterReports')->with('error_message', trans('errors.error'));
        }

        return view('app.registerReports.reports.preview', ['report' => $report['data']]);
    }

    //delete register report
    public function deleteRegisterReport($id)
    {
        //call deleteRegisterReport method from RegisterReportRepository to delete register report
        $response = $this->repo->deleteRegisterReport($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetRegisterReports')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetRegisterReports')->with('success_message', trans('main.register_report_delete'));
    }

    //pdf register report
    public function pdfRegisterReport($id, $items = false)
    {
        //call registerReportPdfData method from RegisterReportRepository to get register report pdf data
        $data = $this->repo->registerReportPdfData($id, $items);

        //if response status = 0 return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.registerReports.reports.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['report']['report']->report_id.'.pdf');
    }
}
