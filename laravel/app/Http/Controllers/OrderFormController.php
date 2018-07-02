<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use App\OrderForm;
use App\Repositories\OrderFormRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TaxGroupRepository;

class OrderFormController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new OrderFormRepository;
    }

    //get order forms
    public function getOrderForms(Request $request)
    {
        //get search parameter
        $search_string = $request->search_string;

        //call getOrderForms method from OrderFormRepository to get order forms
        $order_forms = $this->repo->getOrderForms($search_string);

        //if response status = '0' show error page
        if ($order_forms['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.orderForms.list', ['search_string' => $search_string, 'order_forms' => $order_forms['data']]);
    }

    //add order form
    public function addOrderForm()
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

        return view('app.orderForms.addOrderForm', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data']]);
    }

    //insert order form
    public function insertOrderForm(Request $request)
    {
        $client = $request->client;
        $delivery_date = $request->delivery_date;
        $location = $request->location;
        $note = $request->note;
        $products = $request->products;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), OrderForm::validateOrderFormForm($company_id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call insertOrderForm method from OrderFormRepository to insert order form
        $response = $this->repo->insertOrderForm($client, $delivery_date, $location, $note, $products);

        return response()->json($response);
    }

    //edit order form
    public function editOrderForm($id)
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

        //call getOrderFormDetails method from OrderFormRepository to get order form details
        $this->repo = new OrderFormRepository;
        $order_form = $this->repo->getOrderFormDetails($id);

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $categories['status'] == 0 ||
            $tax_groups['status'] == 0 || $order_form['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.orderForms.editOrderForm', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'order_form' => $order_form['data']]);
    }

    //update order form
    public function updateOrderForm(Request $request)
    {
        $id = $request->id;
        $date = $request->date;
        $client = $request->client;
        $delivery_date = $request->delivery_date;
        $location = $request->location;
        $note = $request->note;
        $products = $request->products;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), OrderForm::validateOrderFormForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call updateOrderForm method from OrderFormRepository to update order form
        $response = $this->repo->updateOrderForm($id, $date, $client, $delivery_date, $location, $note, $products);

        return response()->json($response);
    }

    //delete order form
    public function deleteOrderForm($id)
    {
        //call deleteOrderForm method from OrderFormRepository to delete order form
        $response = $this->repo->deleteOrderForm($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetOrderForms')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetOrderForms')->with('success_message', trans('main.order_form_delete'));
    }

    //get products
    public function getProducts(Request $request)
    {
        $products = $request->products;

        //call getProducts method from OrderFormRepository to get order form products
        $response = $this->repo->getProducts($products);

        return response()->json($response);
    }

    //pdf order form
    public function pdfOrderForm($id)
    {
        //call pdfData method from OrderFormRepository to get pdf data
        $data = $this->repo->pdfData($id);

        //if response status = '0' return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.orderForms.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['order_form']->order_form_id.'.pdf');
    }
}
