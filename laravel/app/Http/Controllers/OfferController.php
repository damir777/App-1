<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use App\Offer;
use App\Repositories\OfferRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\TaxGroupRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OfficeRepository;
use App\Repositories\NoteRepository;
use App\Repositories\UserRepository;

class OfferController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new OfferRepository;
    }

    //get offers
    public function getOffers(Request $request)
    {
        //get search parameters
        $search_string = $request->search_string;
        $office = $request->office;
        $year = $request->year;

        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getOffers method from OfferReportRepository to get offers
        $this->repo = new OfferRepository;
        $offers = $this->repo->getOffers($search_string, $office, $year);

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $offers['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.offers.list', ['offices' => $offices['data'], 'search_string' => $search_string, 'office' => $office,
            'year' => $year, 'offers' => $offers['data']]);
    }

    //add offer
    public function addOffer()
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

        //call getNotesSelect method from NoteRepository to get notes - select
        $this->repo = new NoteRepository;
        $notes = $this->repo->getNotesSelect();

        //call getUserSettings method from UserRepository to get user settings
        $this->repo = new UserRepository;
        $user_settings = $this->repo->getUserSettings();

        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $payment_types['status'] == 0 ||
            $currencies['status'] == 0 || $languages['status'] == 0 || $categories['status'] == 0 || $tax_groups['status'] == 0 ||
            $offices['status'] == 0 || $notes['status'] == 0 || $user_settings['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.offers.addOffer', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'payment_types' => $payment_types['data'], 'currencies' => $currencies['data'],
            'languages' => $languages['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'offices' => $offices['data'], 'notes' => $notes['data'], 'office' => $user_settings['office']]);
    }

    //insert offer
    public function insertOffer(Request $request)
    {
        $office = $request->office;
        $client = $request->client;
        $language = $request->language;
        $payment_type = $request->payment_type;
        $currency = $request->currency;
        $input_currency = $request->input_currency;
        $valid_date = $request->valid_date;
        $tax = $request->tax;
        $note = $request->note;
        $int_note = $request->int_note;
        $products = $request->products;
        $notes = $request->notes;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Offer::validateOfferForm($company_id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call insertOffer method from OfferRepository to insert offer
        $response = $this->repo->insertOffer($office, $client, $language, $payment_type, $currency, $input_currency, $valid_date,
            $tax, $note, $int_note, $products, $notes);

        return response()->json($response);
    }

    //edit offer
    public function editOffer($id)
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

        //call getRegistersSelect method from OfficeRepository to get registers - select
        $this->repo = new OfficeRepository;
        $registers = $this->repo->getRegistersSelect();

        //call getNotesSelect method from NoteRepository to get notes - select
        $this->repo = new NoteRepository;
        $notes = $this->repo->getNotesSelect();

        //call getOfferDetails method from OfferRepository to get offer details
        $this->repo = new OfferRepository;
        $offer = $this->repo->getOfferDetails($id);
        //return $offer;
        //if response status = '0' show error page
        if ($countries['status'] == 0 || $zip_codes['status'] == 0 || $units['status'] == 0 || $payment_types['status'] == 0 ||
            $currencies['status'] == 0 || $languages['status'] == 0 || $categories['status'] == 0 || $tax_groups['status'] == 0 ||
            $registers['status'] == 0 || $notes['status'] == 0 || $offer['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.offers.editOffer', ['countries' => $countries['data'], 'zip_codes' => $zip_codes['data'],
            'units' => $units['data'], 'payment_types' => $payment_types['data'], 'currencies' => $currencies['data'],
            'languages' => $languages['data'], 'categories' => $categories['data'], 'tax_groups' => $tax_groups['data'],
            'registers' => $registers['data'], 'notes' => $notes['data'], 'offer' => $offer['data'],
            'document_notes' => $offer['data']->offer_notes]);
    }

    //update offer
    public function updateOffer(Request $request)
    {
        $id = $request->id;
        $date = $request->date;
        $client = $request->client;
        $language = $request->language;
        $payment_type = $request->payment_type;
        $currency = $request->currency;
        $input_currency = $request->input_currency;
        $valid_date = $request->valid_date;
        $tax = $request->tax;
        $note = $request->note;
        $int_note = $request->int_note;
        $products = $request->products;
        $notes = $request->notes;
        $create_invoice = $request->create_invoice;
        $register = $request->register;
        $due_date = $request->due_date;
        $merchandise = $request->merchandise;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Offer::validateOfferForm($company_id, $id, $create_invoice));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return response()->json(['status' => 0, 'error' => $validator->errors()->all()[0]]);
        }

        //call updateOffer method from OfferRepository to update offer
        $response = $this->repo->updateOffer($id, $date, $client, $language, $payment_type, $currency, $input_currency,
            $valid_date, $tax, $note, $int_note, $products, $notes, $create_invoice, $register, $due_date, $merchandise);

        return response()->json($response);
    }

    //delete offer
    public function deleteOffer($id)
    {
        //call deleteOffer method from OfferRepository to delete offer
        $response = $this->repo->deleteOffer($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetOffers')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetOffers')->with('success_message', trans('main.offer_delete'));
    }

    //copy offer
    public function copyOffer($id)
    {
        //call copyOffer method from OfferRepository to copy offer
        $response = $this->repo->copyOffer($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetOffers')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetOffers')->with('success_message', trans('main.offer_copy'));
    }

    //get products
    public function getProducts(Request $request)
    {
        $client = $request->client;
        $currency = $request->currency;
        $tax = $request->tax;
        $products = $request->products;

        //call getProducts method from OfferRepository to get offer products
        $response = $this->repo->getProducts($client, $currency, $tax, $products);

        return response()->json($response);
    }

    //pdf offer
    public function pdfOffer($type, $id)
    {
        //call pdfData method from OfferRepository to get pdf data
        $data = $this->repo->pdfData($type, $id);

        //if response status = 0 return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.offers.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['offer']->offer_id.'.pdf');
    }

    //send email
    public function sendEmail(Request $request)
    {
        $offer_id = $request->offer_id;

        //call sendEmail method from OfferRepository to send email
        $response = $this->repo->sendEmail($offer_id);

        return response()->json($response);
    }
}
