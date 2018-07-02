<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use App\Dispatch;
use App\Repositories\DispatchRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TaxGroupRepository;

class DispatchController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new DispatchRepository;
    }

    //get dispatches
    public function getDispatches(Request $request)
    {
        //get search parameter
        $search_string = $request->search_string;

        //call getDispatches method from DispatchRepository to get dispatches
        $dispatches = $this->repo->getDispatches($search_string);

        //if response status = '0' show error page
        if ($dispatches['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.dispatches.list', ['search_string' => $search_string, 'dispatches' => $dispatches['data']]);
    }

    //add dispatch
    public function addDispatch()
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getZipCodesSelect method from CompanyRepository to get zip codes - select
        $zip_codes = $this->repo->getZipCodesSelect();

        //call getUnitsSelect method from CompanyRepository to get units - select
        $units = $this->repo->getUnitsSelect();

        //call getCategoriesSelect method from ProductRepository to get categories - select
        $this->repo = new ProductRepository;
        $categories = $this->repo->getCategoriesSelect();

        //call getTaxGroupsSelect method from TaxGroupRepository to get tax groups - select
        $this->repo = new TaxGroupRepository;
        $tax_groups = $this->repo->getTaxGroupsSelect();

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $categories['status'] == 0 ||
            $tax_groups['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.dispatches.addDispatch', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data']]);
    }

    //insert dispatch
    public function insertDispatch(Request $request)
    {
        $client = $request->client;
        $note = $request->note;
        $show_prices = $request->show_prices;
        $products = $request->products;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Dispatch::validateDispatchForm($company_id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call insertDispatch method from DispatchRepository to insert dispatch
        $response = $this->repo->insertDispatch($client, $note, $show_prices, $products);

        return response()->json($response);
    }

    //edit dispatch
    public function editDispatch($id)
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getZipCodesSelect method from CompanyRepository to get zip codes - select
        $zip_codes = $this->repo->getZipCodesSelect();

        //call getUnitsSelect method from CompanyRepository to get units - select
        $units = $this->repo->getUnitsSelect();

        //call getCategoriesSelect method from ProductRepository to get categories - select
        $this->repo = new ProductRepository;
        $categories = $this->repo->getCategoriesSelect();

        //call getTaxGroupsSelect method from TaxGroupRepository to get tax groups - select
        $this->repo = new TaxGroupRepository;
        $tax_groups = $this->repo->getTaxGroupsSelect();

        //call getDispatchDetails method from DispatchRepository to get dispatch details
        $this->repo = new DispatchRepository;
        $dispatch = $this->repo->getDispatchDetails($id);

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $categories['status'] == 0 ||
            $tax_groups['status'] == 0 || $dispatch['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.dispatches.editDispatch', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'dispatch' => $dispatch['data']]);
    }

    //update dispatch
    public function updateDispatch(Request $request)
    {
        $id = $request->id;
        $date = $request->date;
        $client = $request->client;
        $note = $request->note;
        $show_prices = $request->show_prices;
        $products = $request->products;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Dispatch::validateDispatchForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call updateDispatch method from DispatchRepository to update dispatch
        $response = $this->repo->updateDispatch($id, $date, $client, $note, $show_prices, $products);

        return response()->json($response);
    }

    //delete dispatch
    public function deleteDispatch($id)
    {
        //call deleteDispatch method from DispatchRepository to delete dispatch
        $response = $this->repo->deleteDispatch($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetDispatches')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetDispatches')->with('success_message', trans('main.dispatch_delete'));
    }

    //get products
    public function getProducts(Request $request)
    {
        $client = $request->client;
        $products = $request->products;

        //call getProducts method from DispatchRepository to get dispatch products
        $response = $this->repo->getProducts($client, $products);

        return response()->json($response);
    }

    //pdf dispatch
    public function pdfDispatch($id)
    {
        //call pdfData method from DispatchRepository to get pdf data
        $data = $this->repo->pdfData($id);

        //if response status = '0' return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.dispatches.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['dispatch']->dispatch_id.'.pdf');
    }
}
