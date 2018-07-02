<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Company;
use App\Repositories\CompanyRepository;

class AdminController extends Controller
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
        //call getStatistics method from CompanyRepository to get statistics
        $statistics = $this->repo->getStatistics();

        //if response status = '0' show error page
        if ($statistics['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.statistics', ['statistics' => $statistics['data']]);
    }

    //get company info
    public function getCompanyInfo()
    {
        //call getCompanyInfo method from CompanyRepository to get company info
        $company = $this->repo->getCompanyInfo();

        //if response status = '0' show error page
        if ($company['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.companyInfo', ['company' => $company['data']]);
    }

    //update company info
    public function updateCompanyInfo(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $oib = $request->oib;
        $address = $request->address;
        $city = $request->city;
        $zip_code = $request->zip_code;
        $phone = $request->phone;
        $website = $request->website;
        $bank_account = $request->bank_account;
        $iban = $request->iban;
        $swift = $request->swift;
        $document_footer = $request->document_footer;
        $pdv_user = $request->pdv_user;
        $sljednost_prostor = $request->sljednost_prostor;
        $payment_terms = $request->payment_terms;
        $general_terms = $request->general_terms;
        $legal_form = $request->legal_form;

        //validate form inputs
        $validator = Validator::make($request->all(), Company::$company_info);

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('CompanyInfo')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateCompanyInfo method from CompanyRepository to update company info
        $response = $this->repo->updateCompanyInfo($name, $email, $oib, $address, $city, $zip_code, $phone, $website, $bank_account,
            $iban, $swift, $document_footer, $pdv_user, $sljednost_prostor, $payment_terms, $general_terms, $legal_form);

        //if response status != '1' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('CompanyInfo')->with('error_message', trans('errors.error'))->withInput();
        }
        elseif ($response['status'] == 2)
        {
            return redirect()->route('CompanyInfo')->with('error_message', trans('errors.logo'))->withInput();
        }

        return redirect()->route('AdminStatistics')->with('success_message', trans('main.company_info_update'));
    }

    //upload logo
    public function uploadLogo(Request $request)
    {
        //decode json
        $input = $request->json()->all();

        $logo = $input['logo'];

        //validate form inputs
        $validator = Validator::make($input, Company::$logo);

        //if form input is not correct return error status
        if (!$validator->passes())
        {
            return response()->json(['status' => 2]);
        }

        //call uploadLogo method from CompanyRepository to upload logo
        $response = $this->repo->uploadLogo($logo);

        return response()->json($response);
    }

    //get certificate info
    public function getCertificateInfo()
    {
        //call getCertificateInfo method from CompanyRepository to get certificate info
        $certificate = $this->repo->getCertificateInfo();

        //if response status = '0' show error page
        if ($certificate['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.certificate', ['certificate' => $certificate['data']]);
    }

    //update certificate info
    public function updateCertificateInfo(Request $request)
    {
        $certificate = $request->certificate;
        $password = $request->password;

        //validate form inputs
        $validator = Validator::make($request->all(), Company::$fiscal_certificate);

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('CertificateInfo')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateCertificateInfo method from CompanyRepository to update certificate info
        $response = $this->repo->updateCertificateInfo($certificate, $password);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('CertificateInfo')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('CertificateInfo')->with('success_message', trans('main.fiscalization_save'));
    }
}
