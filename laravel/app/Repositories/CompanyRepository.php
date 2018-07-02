<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Notifications\confirmAccount;
use App\Company;
use App\Unit;
use App\Country;
use App\ZipCode;
use App\PaymentType;
use App\Currency;
use App\Language;
use App\Offer;
use App\Invoice;

class CompanyRepository extends UserRepository
{
    //create company
    public function createCompany($first_name, $last_name, $email, $password, $phone, $company_name, $website)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //insert new company
            $company = new Company;
            $company->name = $company_name;
            $company->website = $website;
            $company->active = 'F';
            $company->licence_end = date('Y-m-d');
            $company->save();

            //make token
            $token = substr(md5(rand()), 0, 40);

            //call createAdmin method from UserRepository to create new admin
            $repo = new UserRepository;
            $response = $repo->createAdmin($company->id, $first_name, $last_name, $email, $password, $token, $phone);

            //if response status = 0 return error status
            if ($response['status'] == 0)
            {
                return ['status' => 0];
            }

            $admin = $response['user'];
            $admin->notify(new confirmAccount($admin));

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get company info
    public function getCompanyInfo()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $company = Company::where('id', '=', $company_id)->first();

            return ['status' => 1, 'data' => $company];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update company info
    public function updateCompanyInfo($name, $email, $oib, $address, $city, $zip_code, $phone, $website, $bank_account, $iban,
        $swift, $document_footer, $pdv_user, $sljednost_prostor, $payment_terms, $general_terms, $legal_form)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $company = Company::find($company_id);
            $company->name = $name;
            $company->email = $email;
            $company->oib = $oib;
            $company->address = $address;
            $company->city = $city;
            $company->zip_code = $zip_code;
            $company->phone = $phone;
            $company->website = $website;
            $company->bank_account = $bank_account;
            $company->iban = $iban;
            $company->swift = $swift;
            $company->document_footer = $document_footer;
            $company->pdv_user = $pdv_user;
            $company->sljednost_prostor = $sljednost_prostor;
            $company->payment_terms = $payment_terms;
            $company->general_terms = $general_terms;
            $company->legal_form = $legal_form;
            $company->profile = 'T';

            //if company logo doesn't exist return error status
            if (!$company->logo)
            {
                return ['status' => 2];
            }

            $company->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //upload logo
    public function uploadLogo($logo)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //decode logo
            $img = Image::make(base64_decode($logo));

            //generate logo name string
            $logo_name = substr(md5(rand()), 0, 20).'.png';

            //set logo path
            $logo_path = public_path().'/logo';

            //start transaction
            DB::beginTransaction();

            $company = Company::find($company_id);
            $old_logo = $company->logo;

            //save logo
            $img->save($logo_path.'/'.$logo_name);

            if ($old_logo)
            {
                //delete logo
                File::delete($logo_path.'/'.$old_logo);
            }

            $company->logo = $logo_name;
            $company->save();

            //commit transaction
            DB::commit();

            return ['status' => 1, 'logo_name' => $logo_name];
        }
        catch (Exception $exp)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get certificate info
    public function getCertificateInfo()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $certificate = Company::select('certificate', 'certificate_password')->where('id', '=', $company_id)->first();

            return ['status' => 1, 'data' => $certificate];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update certificate info
    public function updateCertificateInfo($certificate, $password)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $company = Company::find($company_id);
            $company->certificate_password = $password;

            if ($certificate)
            {
                //set certificate path
                $certificate_path = storage_path().'/app/public/certificates';

                //if old certificate exists delete it
                if ($company->certificate)
                {
                    //delete certificate
                    File::delete($certificate_path.'/'.$company->certificate);
                }

                //generate certificate string
                $certificate_string = substr(md5(rand()), 0, 30);

                //get certificate extension
                $extension = $certificate->getClientOriginalExtension();

                //set certificate name
                $certificate_name = $certificate_string.'.'.$extension;

                //upload certificate
                $certificate->move($certificate_path, $certificate_name);

                //update certificate name
                $company->certificate = $certificate_name;
            }

            $company->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get countries - select
    public function getCountriesSelect()
    {
        try
        {
            //set countries array
            $countries_array = [];

            $countries = Country::select('code', 'name')->orderBy('name', 'asc')->get();

            //loop through all countries
            foreach ($countries as $country)
            {
                //add country to countries array
                $countries_array[$country->code] = $country->name;
            }

            return ['status' => 1, 'data' => $countries_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get zip codes - select
    public function getZipCodesSelect()
    {
        try
        {
            //set zip codes array
            $zip_codes_array = [];

            $zip_codes = ZipCode::select('id', 'name')->orderBy('id', 'asc')->get();

            //loop through all zip codes
            foreach ($zip_codes as $zip_code)
            {
                //add zip code to zip codes array
                $zip_codes_array[$zip_code->id] = $zip_code->name;
            }

            return ['status' => 1, 'data' => $zip_codes_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get units - select
    public function getUnitsSelect()
    {
        try
        {
            //set units array
            $units_array = [];

            $units = Unit::select('id', 'code')->get();

            //loop through all units
            foreach ($units as $unit)
            {
                //add unit to units array
                $units_array[$unit->id] = trans('main.'.$unit->code);
            }

            return ['status' => 1, 'data' => $units_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get payment types - select
    public function getPaymentTypesSelect()
    {
        try
        {
            //set payment types array
            $payment_types_array = [];

            $types = PaymentType::select('id', 'code')->orderBy('id', 'asc')->get();

            //loop through all payment types
            foreach ($types as $type)
            {
                //add payment type to payment types array
                $payment_types_array[$type->id] = trans('main.'.$type->code);
            }

            return ['status' => 1, 'data' => $payment_types_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get currencies - select
    public function getCurrenciesSelect()
    {
        try
        {
            //set currencies array
            $currencies_array = [];

            $currencies = Currency::select('id', 'code')->orderBy('id', 'asc')->get();

            //loop through all currencies
            foreach ($currencies as $currency)
            {
                //add currency to currencies array
                $currencies_array[$currency->id] = $currency->code;
            }

            return ['status' => 1, 'data' => $currencies_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get languages - select
    public function getLanguagesSelect()
    {
        try
        {
            //set languages array
            $languages_array = [];

            $languages = Language::select('id', 'code')->orderBy('id', 'asc')->get();

            //loop through all languages
            foreach ($languages as $language)
            {
                //add language to languages array
                $languages_array[$language->id] = trans('main.'.$language->code);
            }

            return ['status' => 1, 'data' => $languages_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //calculate currency ratio
    public function calculateCurrencyRatio($currency, $company_id = false)
    {
        try
        {
            //if company id doesn't exist get company id
            if (!$company_id)
            {
                //call getCompanyId method from UserRepository to get company id
                $company_id = $this->getCompanyId();
            }

            //get currency code
            $currency_code = Currency::find($currency)->code;

            if ($company_id == 65 || $company_id == 66)
            {
                //get current year and month
                $year = date('Y');
                $month = date('n');

                //set currencies array
                $currencies_array = ['HRK' => 1, 'EUR' => 1, 'USD' => 1, 'CHF' => 1, 'GBP' => 1];

                $url = 'http://ec.europa.eu/budg/inforeuro/api/public/monthly-rates?year='.$year.'&month='.$month;
                $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';

                $curl = curl_init();

                $options = array(
                    CURLOPT_URL => $url,
                    CURLOPT_USERAGENT => $user_agent,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type' => 'application/json; charset=UTF-8'
                    )
                );

                curl_setopt_array($curl, $options);

                $response = curl_exec($curl);

                if ($response)
                {
                    $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                    if ($response_code == 200)
                    {
                        $currencies = json_decode($response, true);

                        foreach ($currencies as $currency)
                        {
                            //if current currency exists in currencies array update currency value
                            if (array_key_exists($currency['isoA3Code'], $currencies_array))
                            {
                                $currencies_array[$currency['isoA3Code']] = $currency['value'];
                            }
                        }
                    }
                    else
                    {
                        return ['status' => 0];
                    }
                }
                else
                {
                    return ['status' => 0];
                }

                if ($currency_code == 'EUR')
                {
                    $currency_ratio = 1 / $currencies_array['HRK'];
                }
                else
                {
                    $currency_ratio = 1 / ($currencies_array['HRK'] / $currencies_array[$currency_code]);
                }
            }
            else
            {
                $currency = 1;

                //get currencies from pbz xml
                $pbz_XML = file_get_contents('https://www.pbz.hr/Downloads/HNBteclist.xml');
                $XML = new \SimpleXMLElement($pbz_XML);
                $node = $XML->ExchRate;

                foreach($node->Currency as $key)
                {
                    if ($key->Name == $currency_code)
                    {
                        $currency = (float)str_replace(',', '.', $key->MeanRate);
                    }
                }

                $currency_ratio = 1 / $currency;
            }

            $currency_ratio = number_format($currency_ratio, 5);

            return ['status' => 1, 'data' => $currency_ratio];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get statistics
    public function getStatistics()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set offer counter
            $offer_counter = 0;

            //set realized offer counter
            $realized_offer_counter = 0;

            //set invoice counter
            $invoice_counter = 0;

            //set invoice sum
            $invoice_sum = 0;

            //set paid invoice counter
            $paid_invoice_counter = 0;

            //set paid invoice sum variable
            $paid_invoice_sum = 0;

            //get current year
            $year = date('Y');

            //set statistics array
            $statistics_array = [];

            //set labels array
            $labels_array = [];

            //set offer amounts array
            $offer_amounts_array = [];

            //set invoice amounts array
            $invoice_amounts_array = [];

            //set paid invoice amounts array
            $paid_invoice_amounts_array = [];

            //set months_array
            $months_array = [trans('main.january'), trans('main.february'), trans('main.march'), trans('main.april'),
                trans('main.may'), trans('main.june'), trans('main.july'), trans('main.august'), trans('main.september'),
                trans('main.october'), trans('main.november'), trans('main.december')];

            for ($i = 1; $i < 13; $i++)
            {
                //set graph offer sum variable
                $graph_offer_sum = 0;

                //set graph invoice sum variable
                $graph_invoice_sum = 0;

                //set graph paid invoice sum variable
                $graph_paid_invoice_sum = 0;

                $offers = Offer::select('id', 'realized')->where('company_id', '=', $company_id)
                    ->whereRaw('MONTH(offer_date) = ? AND YEAR(offer_date) = ?', [$i, $year])->get();

                foreach ($offers as $offer)
                {
                    //call getOfferSum method from OfferRepository to get offer sum
                    $repo = new OfferRepository;
                    $current_offer_sum = $repo->getOfferSum($offer->id);

                    $offer_counter++;
                    $graph_offer_sum += $current_offer_sum;

                    if ($offer->realized == 'T')
                    {
                        $realized_offer_counter++;
                    }
                }

                $offer_amounts_array[] = number_format($graph_offer_sum, 2, '.', '');

                /*
                |--------------------------------------------------------------------------
                |--------------------------------------------------------------------------
                */

                $invoices = Invoice::select('id', 'paid', 'partial_paid_sum')->where('company_id', '=', $company_id)
                    ->whereRaw('MONTH(invoice_date) = ? AND YEAR(invoice_date) = ?', [$i, $year])->where('reversed_id', '=', 0)
                    ->get();

                foreach ($invoices as $invoice)
                {
                    //call getInvoiceSum method from InvoiceRepository to get invoice sum
                    $repo = new InvoiceRepository;
                    $current_invoice_sum = $repo->getInvoiceSum($invoice->id);

                    if ($invoice->partial_paid_sum)
                    {
                        $current_invoice_sum = $invoice->partial_paid_sum;
                    }

                    $invoice_counter++;

                    $graph_invoice_sum += $current_invoice_sum;

                    if ($invoice->paid == 'T')
                    {
                        $paid_invoice_counter++;

                        $graph_paid_invoice_sum += $current_invoice_sum;
                    }
                }

                $invoice_amounts_array[] = number_format($graph_invoice_sum, 2, '.', '');
                $paid_invoice_amounts_array[] = number_format($graph_paid_invoice_sum, 2, '.', '');

                $invoice_sum += $graph_invoice_sum;
                $paid_invoice_sum += $graph_paid_invoice_sum;

                $labels_array[] = $months_array[$i - 1];
            }

            $statistics_array['offer_counter'] = $offer_counter;
            $statistics_array['realized_offer_counter'] = $realized_offer_counter;
            $statistics_array['invoice_counter'] = $invoice_counter;
            $statistics_array['paid_invoice_counter'] = $paid_invoice_counter;
            $statistics_array['paid_invoice_sum'] = number_format($paid_invoice_sum, 2, ',', '.').' HRK';
            $statistics_array['unpaid_invoice_sum'] = number_format($invoice_sum - $paid_invoice_sum, 2, ',', '.').' HRK';
            $statistics_array['labels'] = $labels_array;
            $statistics_array['offers'] = $offer_amounts_array;
            $statistics_array['invoices'] = $invoice_amounts_array;
            $statistics_array['paid_invoices'] = $paid_invoice_amounts_array;

            /*
            |--------------------------------------------------------------------------
            | Unpaid invoices
            |--------------------------------------------------------------------------
            */

            //get current date
            $current_date = date('Y-m-d');

            $unpaid_invoices = Invoice::with('client')
                ->select('id', 'invoice_id', DB::raw('DATE_FORMAT(invoice_date, "%d.%m.%Y.") AS date'),
                    DB::raw('DATE_FORMAT(due_date, "%d.%m.%Y.") AS due_date'), 'client_id', 'language_id', 'currency_id')
                ->where('company_id', '=', $company_id)->where('paid', '=', 'F')->where('reversed_id', '=', 0)
                ->where('due_date', '<', $current_date)->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(10);

            foreach ($unpaid_invoices as $invoice)
            {
                //call getInvoiceSum method from InvoiceRepository to get invoice sum
                $repo = new InvoiceRepository;
                $invoice_sum = $repo->getInvoiceSum($invoice->id);

                $sum = number_format($invoice_sum, 2, ',', '.');

                //set invoice sum
                $invoice->sum = $sum;

                //set default int pdf status
                $invoice->int_pdf = 'F';

                //check language and currency
                if ($invoice->language_id != 1 || $invoice->currency_id != 1)
                {
                    $invoice->int_pdf = 'T';
                }
            }

            $statistics_array['unpaid_invoices'] = $unpaid_invoices;

            return ['status' => 1, 'data' => $statistics_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get super admin statistics
    public function getSuperAdminStatistics()
    {
        try
        {
            //set current date
            $current_date = date('Y-m-d');

            //set statistics array
            $statistics_array = [];

            $total_companies = Company::count();

            $active_companies = Company::where('licence_end', '>', $current_date)->count();

            $subscribers = Company::whereRaw('(YEAR(licence_end) = 2050 OR YEAR(licence_end) = 2040)')
                ->whereNotIn('id', [5,6,7,8,57,62,65,66])->count();

            $uncompleted_profile = Company::where('profile', '=', 'F')->count();

            $statistics_array['total_companies'] = $total_companies;
            $statistics_array['active_companies'] = $active_companies;
            $statistics_array['subscribers'] = $subscribers;
            $statistics_array['uncompleted_profile'] = $uncompleted_profile;

            return ['status' => 1, 'data' => $statistics_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get companies
    public function getCompanies()
    {
        try
        {
            //set current date
            $current_date = date('Y-m-d');

            $companies = Company::select('id', 'name', 'city', 'phone', 'active', 'licence_end as sql_licence_end',
                DB::raw('DATE_FORMAT(licence_end, "%d.%m.%Y.") as licence_end'))->orderBy('name', 'asc')->paginate(30);

            foreach ($companies as $company)
            {
                $company->active = trans('main.no');

                if ($company->sql_licence_end > $current_date)
                {
                    $company->active = trans('main.yes');
                }
            }

            return ['status' => 1, 'data' => $companies];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update licence
    public function updateLicence($company_id, $licence_end)
    {
        try
        {
            //format end date
            $licence_end = date('Y-m-d', strtotime($licence_end));

            $company = Company::find($company_id);
            $company->licence_end = $licence_end;
            $company->active = 'T';
            $company->save();

            //set update licence flash
            Session::flash('success_message', trans('main.licence_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get users
    public function getUsers()
    {
        try
        {
            $users = DB::table('users AS u')
                ->join('companies as c', 'u.company_id', '=', 'c.id')
                ->select('u.id AS id', 'u.first_name AS first_name', 'u.last_name AS last_name', 'u.active as active',
                    'c.name as company')
                ->where('u.id', '!=', 32)->orderBy('company', 'asc')->paginate(30);

            return ['status' => 1, 'data' => $users];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get licence info
    public function getLicenceInfo()
    {
        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->getCompanyId();

        //get active status and licence end
        $company = Company::find($company_id);
        $active = $company->active;
        $licence_end = $company->licence_end;

        //set show licence info variable
        $show_licence_info = 'F';

        //set days remaining variable
        $days_remaining = 0;

        //get current date
        $current_date = date('Y-m-d');

        if ($licence_end <= $current_date)
        {
            //update active status to 'F'
            $company->active = 'F';
            $company->save();

            $active = 'F';
            $show_licence_info = 'T';
        }
        else
        {
            //get days remaining
            $date1 = date_create(date('Y-m-d', strtotime($licence_end)));
            $date2 = date_create(date('Y-m-d'));
            $diff = date_diff($date1, $date2);

            $days_remaining = $diff->format('%a');

            if ($days_remaining < 6)
            {
                $days_remaining = $days_remaining.' '.trans_choice('main.days_remaining', $days_remaining);
                $show_licence_info = 'T';
            }
        }

        return ['active' => $active, 'show_licence_info' => $show_licence_info, 'days_remaining' => $days_remaining];
    }

    //get monthly subscribers
    public function getMonthlySubscribers()
    {
        try
        {
            $companies = Company::select('name', 'city', 'phone', 'invoice_email')
                ->whereRaw('YEAR(licence_end) = 2050')->whereNotIn('id', [5,6,7,8,57,62,65,66])->orderBy('name', 'asc')
                ->paginate(30);

            return ['status' => 1, 'data' => $companies];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get annual subscribers
    public function getAnnualSubscribers()
    {
        try
        {
            $companies = Company::select('id', 'name', 'city', 'phone', 'invoice_email')
                ->whereRaw('YEAR(licence_end) = 2040')->whereNotIn('id', [5,6,7,8,57,62,65,66])->orderBy('name', 'asc')
                ->paginate(30);

            foreach ($companies as $company)
            {
                $first_invoice = Invoice::with('client')
                    ->select('invoice_date')
                    ->whereHas('client', function($query) use ($company) {
                        $query->where('company_id', '=', 7)->whereRaw('name LIKE ?', ['%'.$company->name.'%']);
                    })
                    ->where('company_id', '=', 7)
                    ->orderBy('id', 'desc')->first();

                $company->first_invoice = date('d.m.Y.', strtotime('+12 months', strtotime($first_invoice->invoice_date)));
            }

            return ['status' => 1, 'data' => $companies];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
