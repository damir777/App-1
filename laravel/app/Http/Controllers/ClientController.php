<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomValidator\Validator as CustomValidator;
use App\Repositories\ClientRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\InvoiceRepository;

class ClientController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new ClientRepository;
    }

    //get clients
    public function getClients(Request $request)
    {
        //get search parameters
        $search_string = $request->search_string;
        $type = $request->type;

        //call getClients method from ClientRepository to get clients
        $clients = $this->repo->getClients($search_string, $type);

        //if response status = '0' show error page
        if ($clients['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.clients.list', ['search_string' => $search_string, 'type' => $type, 'clients' => $clients['data']]);
    }

    //add client
    public function addClient()
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getZipCodesSelect method from CompanyRepository to get zip codes - select
        $zip_codes = $this->repo->getZipCodesSelect();

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.clients.addClient', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data']]);
    }

    //insert client
    public function insertClient(Request $request)
    {
        $document_insert = $request->document_insert;
        $retail_client = $request->retail_client;
        $type = $request->client_type;
        $name = $request->name;
        $oib = $request->oib;
        $tax_number = $request->tax_number;
        $address = $request->address;
        $city = $request->city;
        $zip_code = $request->zip_code_text;
        $zip_code_id = $request->zip_code_select;
        $country = $request->country;
        $phone = $request->phone;
        $email = $request->email;
        $int_client = $request->int_client;
        $rebate = $request->rebate;

        //validate form inputs
        $validator = CustomValidator::clients($retail_client, $type, $int_client);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call insertClient method from ClientRepository to insert client
        $response = $this->repo->insertClient($document_insert, $type, $name, $oib, $tax_number, $address, $city, $zip_code,
            $zip_code_id, $country, $phone, $email, $int_client, $rebate);

        return response()->json($response);
    }

    //edit client
    public function editClient($id)
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getZipCodesSelect method from CompanyRepository to get zip codes - select
        $zip_codes = $this->repo->getZipCodesSelect();

        //call getClientDetails method from ClientRepository to get client details
        $this->repo = new ClientRepository;
        $client = $this->repo->getClientDetails($id);

        //if response status = '0' return error message
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $client['status'] == 0)
        {
            return redirect()->route('GetClients')->with('error_message', trans('errors.error'));
        }

        return view('app.clients.editClient', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'client' => $client['data']]);
    }

    //update client
    public function updateClient(Request $request)
    {
        $retail_client = $request->retail_client;
        $id = $request->id;
        $type = $request->client_type;
        $name = $request->name;
        $oib = $request->oib;
        $tax_number = $request->tax_number;
        $address = $request->address;
        $city = $request->city;
        $zip_code = $request->zip_code_text;
        $zip_code_id = $request->zip_code_select;
        $country = $request->country;
        $phone = $request->phone;
        $email = $request->email;
        $int_client = $request->int_client;
        $rebate = $request->rebate;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::clients($retail_client, $type, $int_client, $company_id, $id);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call updateClient method from ClientRepository to update client
        $response = $this->repo->updateClient($id, $type, $name, $oib, $tax_number, $address, $city, $zip_code, $zip_code_id,
            $country, $phone, $email, $int_client, $rebate);

        return response()->json($response);
    }

    //delete client
    public function deleteClient($id)
    {
        //call deleteClient method from ClientRepository to delete client
        $response = $this->repo->deleteClient($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetClients')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetClients')->with('success_message', trans('main.client_delete'));
    }

    //search clients
    public function searchClients(Request $request)
    {
        $search_string = $request->search_string;

        //call searchClients method from ClientRepository to search clients
        $response = $this->repo->searchClients($search_string);

        return response()->json($response);
    }

    //get client invoices
    public function getClientInvoices($id)
    {
        //call getClientInvoices method from InvoiceRepository to get client details
        $this->repo = new InvoiceRepository;
        $invoices = $this->repo->getClientInvoices($id);

        //if response status = '0' return error message
        if ($invoices['status'] == 0)
        {
            return redirect()->route('GetClients')->with('error_message', trans('errors.error'));
        }

        return view('app.clients.invoices', ['invoices' => $invoices]);
    }

    //insert client price
    public function insertClientPrice(Request $request)
    {
        $client = $request->client;
        $product = $request->product;
        $price = $request->price;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::clientPrice($company_id);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call insertClientPrice method from ClientRepository to insert client price
        $response = $this->repo->insertClientPrice($client, $product, $price);

        return response()->json($response);
    }

    //delete client price
    public function deleteClientPrice(Request $request)
    {
        $client = $request->client;
        $product = $request->product;

        //call deleteClientPrice method from ClientRepository to delete client price
        $response = $this->repo->deleteClientPrice($client, $product);

        return response()->json($response);
    }
}
