<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 600);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade as PDF;
use App\Notifications\sendRecurringInvoice;
use App\Invoice;
use App\InvoiceProduct;
use App\Contract;
use App\ContractProduct;
use App\Client;
use App\Company;
use App\User;
use App\Repositories\InvoiceRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\TaxGroupRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OfficeRepository;
use App\Repositories\NoteRepository;
use App\Repositories\UserRepository;

class InvoiceController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new InvoiceRepository;
    }

    //get invoices
    public function getInvoices($type, Request $request)
    {
        //get search parameters
        $search_string = $request->search_string;
        $office = $request->office;
        $register = $request->register;
        $year = $request->year;

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getRegisterSelect method from OfficeRepository to get registers - select
        $registers = $this->repo->getRegistersSelect(1);

        //call getInvoices method from InvoiceRepository to get invoices
        $this->repo = new InvoiceRepository;
        $invoices = $this->repo->getInvoices($type, $search_string, $office, $register, $year);

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $registers['status'] == 0 || $invoices['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.invoices.list', ['type' => $type, 'offices' => $offices['data'], 'registers' => $registers['data'],
            'search_string' => $search_string, 'office' => $office, 'register' => $register, 'year' => $year,
            'invoices' => $invoices['data']]);
    }

    //add invoice
    public function addInvoice($type)
    {
        $retail = 'F';
        $due_date = null;
        $payment_type = 2;

        if ($type == 1)
        {
            $retail = 'T';
            $due_date = date('d.m.Y.');
            $payment_type = 1;
        }

        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getZipCodesSelect method from CompanyRepository to get zip codes - select
        $zip_codes = $this->repo->getZipCodesSelect();

        //call getUnitsSelect method from CompanyRepository to get units - select
        $units = $this->repo->getUnitsSelect();

        //call getPaymentTypeSelect method from CompanyRepository to get payment types - select
        $payment_types = $this->repo->getPaymentTypesSelect();

        //call getCurrenciesSelect method from CompanyRepository to get currencies - select
        $currencies = $this->repo->getCurrenciesSelect();

        //call getLanguagesSelect from CompanyRepository to get languages - select
        $languages = $this->repo->getLanguagesSelect();

        //call getCategoriesSelect method from ProductRepository to get categories - select
        $this->repo = new ProductRepository;
        $categories = $this->repo->getCategoriesSelect();

        //call getTaxGroupsSelect method from TaxGroupRepository to get tax groups - select
        $this->repo = new TaxGroupRepository;
        $tax_groups = $this->repo->getTaxGroupsSelect();

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect();

        //call getRegisterSelect method from OfficeRepository to get registers - select
        $registers = $this->repo->getRegistersSelect();

        //call getNotesSelect method from NoteRepository to get notes - select
        $this->repo = new NoteRepository;
        $notes = $this->repo->getNotesSelect();

        //call getReferenceNumber method from InvoiceRepository to get reference number
        $this->repo = new InvoiceRepository;
        $reference_number = $this->repo->getReferenceNumber();

        //call getUserSettings method from UserRepository to get user settings
        $this->repo = new UserRepository;
        $user_settings = $this->repo->getUserSettings();

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $payment_types['status'] == 0 ||
            $currencies['status'] == 0 || $languages['status'] == 0 || $categories['status'] == 0 || $tax_groups['status'] == 0 ||
            $offices['status'] == 0 || $registers['status'] == 0 || $notes['status'] == 0 || $reference_number['status'] == 0 ||
            $user_settings['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.invoices.addInvoice', ['retail' => $retail, 'due_date' => $due_date,
            'payment_type' => $payment_type, 'countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'payment_types' => $payment_types['data'], 'currencies' => $currencies['data'],
            'languages' => $languages['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'offices' => $offices['data'], 'registers' => $registers['data'], 'notes' => $notes['data'], 'model' => 'HR99',
            'reference_number' => $reference_number['data'], 'office' => $user_settings['office'],
            'register' => $user_settings['register']]);
    }

    //insert invoice
    public function insertInvoice(Request $request)
    {
        $retail = $request->retail;
        $office = $request->office;
        $register = $request->register;
        $client = $request->client;
        $language = $request->language;
        $payment_type = $request->payment_type;
        $currency = $request->currency;
        $input_currency = $request->input_currency;
        $due_date = $request->due_date;
        $delivery_date = $request->delivery_date;
        $note = $request->note;
        $int_note = $request->int_note;
        $tax = $request->tax;
        $advance = $request->advance;
        $show_model = $request->show_model;
        $model = $request->model;
        $reference_number = $request->reference_number;
        $products = $request->products;
        $notes = $request->notes;
        $merchandise = $request->merchandise;
        $print = $request->print;
        $email = $request->email;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Invoice::validateInvoiceForm($company_id, $retail));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call insertInvoice method from InvoiceRepository to insert invoice
        $response = $this->repo->insertInvoice($retail, $office, $register, $client, $language, $payment_type, $currency,
            $input_currency, $due_date, $delivery_date, $note, $int_note, $tax, $advance, $show_model, $model, $reference_number,
            $products, $notes, $merchandise, $print, $email);

        return response()->json($response);
    }

    //edit invoice
    public function editInvoice($id)
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getZipCodesSelect method from CompanyRepository to get zip codes - select
        $zip_codes = $this->repo->getZipCodesSelect();

        //call getUnitsSelect method from CompanyRepository to get units - select
        $units = $this->repo->getUnitsSelect();

        //call getPaymentTypeSelect method from CompanyRepository to get payment types - select
        $payment_types = $this->repo->getPaymentTypesSelect();

        //call getCurrenciesSelect method from CompanyRepository to get currencies - select
        $currencies = $this->repo->getCurrenciesSelect();

        //call getLanguagesSelect from CompanyRepository to get languages - select
        $languages = $this->repo->getLanguagesSelect();

        //call getCategoriesSelect method from ProductRepository to get categories - select
        $this->repo = new ProductRepository;
        $categories = $this->repo->getCategoriesSelect();

        //call getTaxGroupsSelect method from TaxGroupRepository to get tax groups - select
        $this->repo = new TaxGroupRepository;
        $tax_groups = $this->repo->getTaxGroupsSelect();

        //call getNotesSelect method from NoteRepository to get notes - select
        $this->repo = new NoteRepository;
        $notes = $this->repo->getNotesSelect();

        //call getInvoiceDetails method from InvoiceRepository to get invoice details
        $this->repo = new InvoiceRepository;
        $invoice = $this->repo->getInvoiceDetails($id);

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $payment_types['status'] == 0 ||
            $currencies['status'] == 0 || $languages['status'] == 0 || $categories['status'] == 0 || $tax_groups['status'] == 0 ||
            $notes['status'] == 0 || $invoice['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.invoices.editInvoice', ['countries' => $countries['data'],
            'zip_codes' => $zip_codes['data'], 'units' => $units['data'], 'payment_types' => $payment_types['data'],
            'currencies' => $currencies['data'], 'languages' => $languages['data'], 'categories' => $categories['data'],
            'tax_groups' => $tax_groups['data'], 'notes' => $notes['data'], 'invoice' => $invoice['data'],
            'document_notes' => $invoice['data']->invoice_notes]);
    }

    //update invoice
    public function updateInvoice(Request $request)
    {
        $id = $request->id;
        $date = $request->date;
        $client = $request->client;
        $language = $request->language;
        $payment_type = $request->payment_type;
        $currency = $request->currency;
        $input_currency = $request->input_currency;
        $due_date = $request->due_date;
        $delivery_date = $request->delivery_date;
        $note = $request->note;
        $int_note = $request->int_note;
        $tax = $request->tax;
        $advance = $request->advance;
        $show_model = $request->show_model;
        $model = $request->model;
        $reference_number = $request->reference_number;
        $status = $request->status;
        $partial_paid_sum = $request->partial_paid_sum;
        $products = $request->products;
        $notes = $request->notes;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Invoice::validateInvoiceForm($company_id, 'F', $id, $status));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call updateInvoice method from InvoiceRepository to update invoice
        $response = $this->repo->updateInvoice($id, $date, $client, $language, $payment_type, $currency,
            $input_currency, $due_date, $delivery_date, $note, $int_note, $tax, $advance, $show_model, $model, $reference_number,
            $status, $partial_paid_sum, $products, $notes);

        return response()->json($response);
    }

    //delete invoice
    public function deleteInvoice($id)
    {
        //call deleteInvoice method from InvoiceRepository to delete invoice
        $response = $this->repo->deleteInvoice($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetInvoices', 2)->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetInvoices', 2)->with('success_message', trans('main.invoice_delete'));
    }

    //copy invoice
    public function copyInvoice($id)
    {
        //call copyInvoice method from InvoiceRepository to copy invoice
        $response = $this->repo->copyInvoice($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetInvoices', 2)->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetInvoices', 2)->with('success_message', trans('main.invoice_copy'));
    }

    //reverse invoice
    public function reverseInvoice($type, $id)
    {
        //call reverseInvoice method from InvoiceRepository to reverse invoice
        $response = $this->repo->reverseInvoice($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetInvoices', $type)->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetInvoices', $type)->with('success_message', trans('main.invoice_reverse'));
    }

    //get products
    public function getProducts(Request $request)
    {
        $client = $request->client;
        $currency = $request->currency;
        $tax = $request->tax;
        $products = $request->products;

        //call getProducts method from InvoiceRepository to get invoice products
        $response = $this->repo->getProducts($client, $currency, $tax, $products);

        return response()->json($response);
    }

    //pdf invoice
    public function pdfInvoice($type, $id)
    {
        //call pdfData method from InvoiceRepository to get pdf data
        $data = $this->repo->pdfData($type, $id);

        //if response status = 0 return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.invoices.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['invoice']->invoice_id.'.pdf');
    }

    //fiscalization
    public function fiscalization(Request $request)
    {
        $invoice_id = $request->invoice_id;

        //call fiscalization method from InvoiceRepository to make fiscalization
        $response = $this->repo->fiscalization($invoice_id);

        return response()->json($response);
    }

    //send email
    public function sendEmail(Request $request)
    {
        $invoice_id = $request->invoice_id;

        //call sendEmail method from InvoiceRepository to send email
        $response = $this->repo->sendEmail($invoice_id);

        return response()->json($response);
    }

    //create recurring invoices
    public function createRecurringInvoices()
    {
        //set current day
        $current_day = (int)date('d');

        //set current month
        $current_month = date('Y-m');

        $emails_array = [];

        //start transaction
        DB::beginTransaction();

        $contracts = Contract::where('active', '=', 'T')->get();

        foreach ($contracts as $contract)
        {
            $create_invoice = true;

            //get contract create day
            $create_day = $contract->create_day;

            //if current date day != contract date day move to next contract
            if ($current_day != $create_day)
            {
                continue;
            }

            //get contract month
            $contract_month = date('Y-m', strtotime($contract->contract_date));

            //get previous month create status
            $previous_month_create = $contract->previous_month_create;

            if ($previous_month_create == 'T' && $contract_month == $current_month)
            {
                $create_invoice = false;
            }

            //if create invoice variable = 'false' move to next contract
            if (!$create_invoice)
            {
                continue;
            }

            //get contact create after end status
            $create_after_end = $contract->create_after_end;

            //get number of created invoices
            $created_invoices = Invoice::where('contract_id', '=', $contract->id)->count();

            if ($create_after_end == 'F' && $created_invoices >= $contract->number_of_invoices)
            {
                $create_invoice = false;
            }

            //if create invoice variable == false move to next contract
            if (!$create_invoice)
            {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Create invoice
            |--------------------------------------------------------------------------
            */

            //call getNextInvoiceId method to get next invoice id
            $response = $this->repo->getNextInvoiceId($contract->company_id, 'F', $contract->office_id, $contract->register_id);

            //format due date
            $due_date = date('Y-m-d', strtotime('+'.$contract->due_days.' day', strtotime(date('Y-m-d'))));

            $currency_ratio = 1;

            //if currency != 1 calculate currency ratio
            if ($contract->currency_id != 1)
            {
                //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                $repo = new CompanyRepository;
                $currency_response = $repo->calculateCurrencyRatio($contract->currency_id, $contract->company_id);

                //if response status = 0 return error message
                if ($currency_response['status'] == 0)
                {
                    Log::info('Greška kod kreiranja ponavljajučeg računa!!! Currency ratio');

                    return ['status' => 0];
                }

                $currency_ratio = $currency_response['data'];
            }

            $invoice = new Invoice;
            $invoice->company_id = $contract->company_id;
            $invoice->user_id = $contract->user_id;
            $invoice->doc_number = $response['doc_number'];
            $invoice->invoice_id = $response['invoice_id'];
            $invoice->retail = 'F';
            $invoice->office_id = $contract->office_id;
            $invoice->register_id = $contract->register_id;
            $invoice->invoice_date = DB::raw('NOW()');
            $invoice->client_id = $contract->client_id;
            $invoice->language_id = $contract->language_id;
            $invoice->payment_type_id = $contract->payment_type_id;
            $invoice->currency_id = $contract->currency_id;
            $invoice->input_currency_id = $contract->input_currency_id;
            $invoice->currency_ratio = $currency_ratio;
            $invoice->due_date = $due_date;
            $invoice->note = $contract->dom_note;
            $invoice->int_note = $contract->int_note;
            $invoice->reversed_id = 0;
            $invoice->tax = $contract->tax;
            $invoice->contract_id = $contract->id;
            $invoice->current_contract_invoice = $created_invoices + 1;
            $invoice->save();

            //get contract products
            $products = ContractProduct::where('contract_id', '=', $contract->id)->get();

            foreach ($products as $product)
            {
                //insert invoice product
                $invoice_product = new InvoiceProduct;
                $invoice_product->invoice_id = $invoice->id;
                $invoice_product->product_id = $product->product_id;
                $invoice_product->quantity = $product->quantity;
                $invoice_product->price = $product->price;
                $invoice_product->custom_price = $product->custom_price;
                $invoice_product->brutto = $product->brutto;
                $invoice_product->tax_group_id = $product->tax_group_id;
                $invoice_product->rebate = $product->rebate;
                $invoice_product->note = $product->note;
                $invoice_product->save();
            }

            //get client data
            $client = Client::find($contract->client_id);

            //if client email is empty move to next contract
            if ($client->email == '')
            {
                continue;
            }

            //if contract sending status = 'F' move to next contract
            if ($contract->email_sending == 'F')
            {
                continue;
            }

            //get company name
            $company_name = Company::find($contract->company_id)->name;

            $pdf_language = 'hr';

            if ($contract->language_id != 1)
            {
                $pdf_language = 'en';
            }

            $data = $this->repo->pdfData(1, $invoice->id, $contract->company_id);

            //if response status = 0 return error message
            if ($data['status'] == 0)
            {
                Log::info('Greška kod kreiranja ponavljajučeg računa!!! Generiranje pdf-a');

                return ['status' => 0];
            }

            $emails_array[] = ['client' => $client->name, 'email' => $client->email, 'invoice_id' => $invoice->invoice_id,
                'pdf_data' => $data, 'company' => $company_name, 'pdf_language' => $pdf_language];
        }

        //commit transaction
        DB::commit();

        foreach ($emails_array as $email)
        {
            $invoice_id = str_replace('/', '-', $email['invoice_id']);

            //set email details
            $subject = trans('main.invoice').' '.$email['invoice_id'];
            $greeting = 'Poštovani,';
            $text = 'u prilogu dostavljamo mjesečni račun za usluge prema važećem ugovoru.';
            $salutation = 'S poštovanjem,<br>'.$email['company'];

            if ($email['pdf_language'] == 'en')
            {
                $greeting = 'Dear Sir/Madam,';
                $text = 'we are sending you monthly invoice according to valid written agreement.';
                $salutation = 'Best regards,<br>'.$email['company'];
            }

            //create temp user and send mail to client
            (new User)->forceFill([
                'name' => $email['client'],
                'email' => $email['email']
            ])->notify(new sendRecurringInvoice($subject, $greeting, $text, $salutation, $email['pdf_data'], $invoice_id));
        }

        return 'OK';
    }
}
