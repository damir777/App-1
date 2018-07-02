<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 600);

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;
use App\Notifications\sendxxInvoice;
use App\Notifications\sendContactEmail;
use App\Company;
use App\User;
use App\Client;
use App\Invoice;
use App\InvoiceProduct;
use App\Offer;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use App\Repositories\InvoiceRepository;

class SuperAdminController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new CompanyRepository;
    }

    //get statistics
    public function getStatistics()
    {
        //call getSuperAdminStatistics method from CompanyRepository to get super admin statistics
        $statistics = $this->repo->getSuperAdminStatistics();

        //if response status = '0' show error page
        if ($statistics['status'] == 0)
        {
            return view('errors.500');
        }

        return view('superAdmin.statistics', ['statistics' => $statistics['data']]);
    }

    //get companies
    public function getCompanies()
    {
        //call getCompanies method from CompanyRepository to get companies
        $companies = $this->repo->getCompanies();

        //if response status = '0' show error page
        if ($companies['status'] == 0)
        {
            return view('errors.500');
        }

        return view('superAdmin.companies', ['companies' => $companies['data']]);
    }

    //update licence
    public function updateLicence(Request $request)
    {
        $company_id = $request->company_id;
        $licence_end = $request->licence_end;

        //validate form inputs
        $validator = Validator::make($request->all(), Company::$licence);

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => trans('errors.validation_error')]);
        }

        //call updateLicence method from CompanyRepository to update licence
        $response = $this->repo->updateLicence($company_id, $licence_end);

        return response()->json($response);
    }

    //get users
    public function getUsers()
    {
        //call getAllUsers method from CompanyRepository to get users
        $users = $this->repo->getUsers();

        //if response status = '0' show error page
        if ($users['status'] == 0)
        {
            return view('errors.500');
        }

        return view('superAdmin.users', ['users' => $users['data']]);
    }

    //login as user
    public function loginAsUser($id)
    {
        //call loginAsUser method from UserRepository to login as user
        $this->repo = new UserRepository;
        $response = $this->repo->loginAsUser($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('LoginPage')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('SuperAdminGetUsers');
    }

    //return to super admin
    public function returnToSuperAdmin()
    {
        //logout user
        Auth::logout();

        if (!Session::has('super_admin'))
        {
            return redirect()->route('LoginPage');
        }

        //clear all session variable
        Session::flush();

        $user = User::find(32);

        //login user
        Auth::login($user);

        return redirect()->route('SuperAdminGetUsers');
    }

    //deactivate user
    public function deactivateUser($id)
    {
        //call deactivateUser method from UserRepository to deactivate user
        $this->repo = new UserRepository;
        $response = $this->repo->deactivateUser($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('SuperAdminGetUsers')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('SuperAdminGetUsers')->with('success_message', trans('main.deactivate_user'));
    }

    //activate user
    public function activateUser($id)
    {
        //call activateUser method from ClientRepository to activate user
        $this->repo = new UserRepository;
        $response = $this->repo->activateUser($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('SuperAdminGetUsers')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('SuperAdminGetUsers')->with('success_message', trans('main.activate_user'));
    }

    //get monthly subscribers
    public function getMonthlySubscribers()
    {
        //call getMonthlySubscribers method from CompanyRepository to get monthly subscribers
        $subscribers = $this->repo->getMonthlySubscribers();

        //if response status = '0' show error page
        if ($subscribers['status'] == 0)
        {
            return view('errors.500');
        }

        return view('superAdmin.monthlySubscribers', ['subscribers' => $subscribers['data']]);
    }

    //get annual subscribers
    public function getAnnualSubscribers()
    {
        //call getAnnualSubscribers method from CompanyRepository to get annual subscribers
        $subscribers = $this->repo->getAnnualSubscribers();

        //if response status = '0' show error page
        if ($subscribers['status'] == 0)
        {
            return view('errors.500');
        }

        return view('superAdmin.annualSubscribers', ['subscribers' => $subscribers['data']]);
    }

    //invoices cron job
    public function invoicesCronJob()
    {
        //start transaction
        DB::beginTransaction();

        $discount_companies = [54, 75, 81, 111];

        $emails_array = [];

        $companies = Company::select('id', 'name', 'oib', 'address', 'city', 'zip_code', 'licence_end', 'invoice_email')
            ->whereNotIn('id', [5, 6, 7, 8, 57, 62, 65, 66])->whereRaw('YEAR(licence_end) = 2050')->orderBy('id', 'asc')->get();

        foreach ($companies as $company)
        {
            $client = Client::select('id')->where('company_id', '=', 7)->where('oib', '=', $company->oib)->first();

            if (!$client)
            {
                $client = new Client;
                $client->company_id = 7;
                $client->client_type = 2;
                $client->name = $company->name;
                $client->oib = $company->oib;
                $client->address = $company->address;
                $client->city = $company->city;
                $client->zip_code = $company->zip_code;
                $client->zip_code_id = 1;
                $client->country = 'HR';
                $client->phone = $company->phone;
                $client->email = $company->invoice_email;
                $client->int_client = 'F';
                $client->save();
            }

            //call getNextInvoiceId method to get next invoice id
            $repo = new InvoiceRepository;
            $response = $repo->getNextInvoiceId(7, 'F', 11, 70);

            $due_date = date('Y-m-d', strtotime('+10 day', strtotime(date('Y-m-d'))));

            $note = 'Račun je kreiran automatski na računalu te je važeći bez potpisa i pečata.';

            $invoice = new Invoice;
            $invoice->company_id = 7;
            $invoice->user_id = 91;
            $invoice->doc_number = $response['doc_number'];
            $invoice->invoice_id = $response['invoice_id'];
            $invoice->retail = 'F';
            $invoice->office_id = 11;
            $invoice->register_id = 70;
            $invoice->invoice_date = DB::raw('NOW()');
            $invoice->client_id = $client->id;
            $invoice->language_id = 1;
            $invoice->payment_type_id = 2;
            $invoice->currency_id = 1;
            $invoice->input_currency_id = 1;
            $invoice->currency_ratio = 1.000000;
            $invoice->due_date = $due_date;
            $invoice->note = $note;
            $invoice->paid = 'F';
            $invoice->reversed_id = 0;
            $invoice->tax = 'T';
            $invoice->save();

            $quantity = User::where('company_id', '=', $company->id)->where('active', '=', 'T')->count();

            $price = 40;

            if (in_array($company->id, $discount_companies))
            {
                $price = 31.20;
            }

            //insert invoice product
            $invoice_product = new InvoiceProduct;
            $invoice_product->invoice_id = $invoice->id;
            $invoice_product->product_id = 392;
            $invoice_product->quantity = $quantity;
            $invoice_product->price = $price;
            $invoice_product->custom_price = 'T';
            $invoice_product->brutto = 'F';
            $invoice_product->tax_group_id = 4;
            $invoice_product->rebate = 0;
            $invoice_product->save();

            $data = $repo->pdfData(1, $invoice->id, 7);

            //if response status = 0 return error message
            if ($data['status'] == 0)
            {
                return 'Nije OK';
            }

            $emails_array[] = ['email' => $company->invoice_email, 'invoice_id' => $invoice->id, 'pdf_data' => $data];
        }

        //commit transaction
        DB::commit();

        foreach ($emails_array as $email)
        {
            $invoice_id = str_replace('/', '-', $email['invoice_id']);

            //create temp user and send mail to client
            (new User)->forceFill([
                'name' => 'Klijent',
                'email' => $email['email']
            ])->notify(new sendxxInvoice($email['pdf_data'], $invoice_id));

            Log::info('Poslani račun: '.$invoice_id.'<br>');
        }

        return 'OK';
    }

    public function createUsagePdf()
    {
        //set companies array
        $companies_array = [];

        $total_users = 0;
        $active_users = 0;

        //get companies
        $companies = Company::select('id', 'name', DB::raw('DATE_FORMAT(created_at, "%d.%m.%Y.") AS register_date'),
            'licence_end')->get();

        foreach ($companies as $company)
        {
            $admin = User::with('role')
                ->where('company_id', '=', $company->id)
                ->whereHas('role', function($query) {
                    $query->where('role_id', '=', 2);
                })->first();

            //count invoices
            $count_invoices = Invoice::where('company_id', '=', $company->id)->count();

            $last_invoice = '';
            $last_offer = '';

            if ($count_invoices > 0)
            {
                $invoice_date = Invoice::select(DB::raw('DATE_FORMAT(invoice_date, "%d.%m.%Y.") AS invoice_date'),
                    'invoice_date AS sql_invoice_date')
                    ->where('company_id', '=', $company->id)->orderBy('sql_invoice_date', 'desc')->first();

                $last_invoice = $invoice_date->invoice_date;
            }

            //count offers
            $count_offers = Offer::where('company_id', '=', $company->id)->count();

            if ($count_offers > 0)
            {
                $offer_date = Offer::select(DB::raw('DATE_FORMAT(offer_date, "%d.%m.%Y.") AS offer_date'),
                    'offer_date AS sql_offer_date')
                    ->where('company_id', '=', $company->id)->orderBy('sql_offer_date', 'desc')->first();

                $last_offer = $offer_date->offer_date;
            }

            $current_date = date('Y-m-d');

            $total_users++;

            if ($current_date > $company->licence_end)
            {
                $active = 'NE';
            }
            else
            {
                $active = 'DA';

                $active_users++;
            }

            //add company to companies array
            $companies_array[] = array('name' => $company->name, 'admin' => $admin->first_name.' '.$admin->last_name,
                'email' => $admin->email, 'register_date' => $company->register_date, 'invoices' => $count_invoices,
                'last_invoice' => $last_invoice, 'offers' => $count_offers, 'last_offer' => $last_offer, 'active' => $active);
        }

        //sort array
        foreach ($companies_array as $key => $row)
        {
            $name[$key] = $row['name'];
            $admin[$key] = $row['admin'];
            $email[$key] = $row['email'];
            $register_date[$key] = $row['register_date'];
            $invoices[$key] = $row['invoices'];
            $last_invoice[$key] = $row['last_invoice'];
            $offers[$key] = $row['offers'];
            $last_offer[$key] = $row['last_offer'];
            $active[$key] = $row['active'];
        }

        array_multisort($invoices, SORT_DESC, $companies_array);

        $view = '<html>
			<head>
		   	<meta http-equiv="content-type" content="text/html; charset=utf-8">
            <title>xx</title>
            <style>
                * {font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;}
                p {font-size: 10px;}
                th {font-size: 10px;}
                td {font-size: 10px;}
            </style>
			</head>
			<body>
			<div style="margin-bottom: 20px;">
			    <p style="font-size: 20px">Ukupno korisnika: '.$total_users.'&nbsp;&nbsp;&nbsp;&nbsp;Aktivnih korisnika: '.
                    $active_users.'</p>
			</div>
			<div>
				<table border="1" cellpadding="2">
			        <tr>
			            <td><b>Kompanija</b></td><td><b>Vlasnik</b><td><b>E-mail</b></td><td><b>Broj računa</b></td>
			            <td><b>Zadnji račun</b></td><td><b>Broj ponuda</b></td><td><b>Zadnja ponuda</b></td>
			            <td><b>Registracija</b></td><td><b>Aktivan</b></td>
			        </tr>';

                    foreach ($companies_array as $row)
                    {
                        $view .= '<tr><td>'.$row['name'].'</td><td>'.$row['admin'].'</td><td>'.$row['email'].'</td>'.
                            '<td align="center">'.$row['invoices'].'</td><td align="center">'.$row['last_invoice'].'</td>'.
                            '<td align="center">'.$row['offers'].'</td><td align="center">'.$row['last_offer'].'</td>'.
                            '<td align="center">'.$row['register_date'].'</td><td align="center">'.$row['active'].'</td></tr>';
                    }

        $view .= '</table></div></body></html>';

        $pdf = PDF::loadHTML($view)->setPaper('a4', 'landscape');

        return $pdf->stream('xx');
    }

    //send contact email
    public function sendContactEmail(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $subject = $request->subject;
        $message = $request->message;

        //validate form inputs
        $validator = Validator::make($request->all(), ['name' => 'required', 'email' => 'required|email', 'subject' => 'required',
            'message' => 'required']);

        //if form input is not correct return error status
        if (!$validator->passes())
        {
            return response()->json(['status' => 0]);
        }

        try
        {
            //create temp user and send contact mail to info@xx.com
            (new User)->forceFill([
                'name' => 'Korisnik',
                'email' => 'info@xx.com'
            ])->notify(new sendContactEmail($name, $email, $subject, $message));

            return response()->json(['status' => 1]);
        }
        catch (Exception $e)
        {
            return response()->json(['status' => 0]);
        }
    }
}
