<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Contract;
use App\Repositories\ContractRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\TaxGroupRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OfficeRepository;

class ContractController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new ContractRepository;
    }

    //get contracts
    public function getContracts(Request $request)
    {
        //get search parameters
        $search_string = $request->search_string;

        //call getContracts method from ContractRepository to get contracts
        $this->repo = new ContractRepository;
        $contracts = $this->repo->getContracts($search_string);

        //if response status = '0' show error page
        if ($contracts['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.contracts.list', ['search_string' => $search_string, 'contracts' => $contracts['data']]);
    }

    //add contract
    public function addContract()
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

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect();

        //call getRegisterSelect method from OfficeRepository to get registers - select
        $registers = $this->repo->getRegistersSelect();

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $payment_types['status'] == 0 ||
            $currencies['status'] == 0 || $languages['status'] == 0 || $categories['status'] == 0 || $tax_groups['status'] == 0 ||
            $offices['status'] == 0 || $registers['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.contracts.addContract', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'payment_types' => $payment_types['data'], 'currencies' => $currencies['data'],
            'languages' => $languages['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'offices' => $offices['data'], 'registers' => $registers['data']]);
    }

    //insert contract
    public function insertContract(Request $request)
    {
        $office = $request->office;
        $register = $request->register;
        $contract_number = $request->contract_number;
        $client = $request->client;
        $language = $request->language;
        $payment_type = $request->payment_type;
        $currency = $request->currency;
        $input_currency = $request->input_currency;
        $due_days = $request->due_days;
        $note = $request->note;
        $int_note = $request->int_note;
        $tax = $request->tax;
        $number_of_invoices = $request->number_of_invoices;
        $create_day = $request->create_day;
        $previous_month_create = $request->previous_month_create;
        $create_after_end = $request->create_after_end;
        $email_sending = $request->email_sending;
        $active = $request->active;
        $products = $request->products;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Contract::validateContractForm($company_id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call insertContract method from ContractRepository to insert contract
        $response = $this->repo->insertContract($office, $register, $contract_number, $client, $language, $payment_type, $currency,
            $input_currency, $due_days, $note, $int_note, $tax, $number_of_invoices, $create_day, $previous_month_create,
            $create_after_end, $email_sending, $active, $products);

        return response()->json($response);
    }

    //edit contract
    public function editContract($id)
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

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect();

        //call getRegisterSelect method from OfficeRepository to get registers - select
        $registers = $this->repo->getRegistersSelect();

        //call getContractDetails method from ContractRepository to get contract details
        $this->repo = new ContractRepository;
        $contract = $this->repo->getContractDetails($id);

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $payment_types['status'] == 0 ||
            $currencies['status'] == 0 || $languages['status'] == 0 || $categories['status'] == 0 || $tax_groups['status'] == 0 ||
            $offices['status'] == 0 || $registers['status'] == 0 || $contract['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.contracts.editContract', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'payment_types' => $payment_types['data'], 'currencies' => $currencies['data'],
            'languages' => $languages['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'offices' => $offices['data'], 'registers' => $registers['data'], 'contract' => $contract['data']]);
    }

    //update contract
    public function updateContract(Request $request)
    {
        $id = $request->id;
        $office = $request->office;
        $register = $request->register;
        $contract_number = $request->contract_number;
        $client = $request->client;
        $language = $request->language;
        $payment_type = $request->payment_type;
        $currency = $request->currency;
        $input_currency = $request->input_currency;
        $due_days = $request->due_days;
        $note = $request->note;
        $int_note = $request->int_note;
        $tax = $request->tax;
        $number_of_invoices = $request->number_of_invoices;
        $create_day = $request->create_day;
        $previous_month_create = $request->previous_month_create;
        $create_after_end = $request->create_after_end;
        $email_sending = $request->email_sending;
        $active = $request->active;
        $products = $request->products;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Contract::validateContractForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call updateContract method from ContractRepository to update contract
        $response = $this->repo->updateContract($id, $office, $register, $contract_number, $client, $language, $payment_type,
            $currency, $input_currency, $due_days, $note, $int_note, $tax, $number_of_invoices, $create_day, $previous_month_create,
            $create_after_end, $email_sending, $active, $products);

        return response()->json($response);
    }

    //delete contract
    public function deleteContract($id)
    {
        //call deleteContract method from ContractRepository to delete contract
        $response = $this->repo->deleteContract($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetContracts')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetContracts')->with('success_message', trans('main.contract_delete'));
    }

    //copy contract
    public function copyContract($id)
    {
        //call copyContract method from ContractRepository to copy contract
        $response = $this->repo->copyContract($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetContracts')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetContracts')->with('success_message', trans('main.contract_copy'));
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
}
