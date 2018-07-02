<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Notifications\documentEmail;
use App\Offer;
use App\OfferProduct;
use App\Product;
use App\Unit;
use App\Client;
use App\Office;
use App\OfferNote;
use App\Invoice;
use App\InvoiceProduct;
use App\Dispatch;
use App\DispatchProduct;
use App\Company;
use App\TaxGroup;
use App\User;
use App\ClientPrice;

class OfferRepository extends UserRepository
{
    //get offers
    public function getOffers($search_string, $office, $year)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offers = Offer::with('client')
                ->select('id', 'offer_id', DB::raw('DATE_FORMAT(offer_date, "%d.%m.%Y.") AS date'), 'client_id', 'language_id',
                    'currency_id', 'realized')
                ->where('company_id', '=', $company_id);

            if ($search_string)
            {
                $offers->whereHas('client', function($query) use ($search_string) {
                    $query->whereRaw('name LIKE ?', ['%'.$search_string.'%']);
                });
            }

            if ($office)
            {
                $offers->where('office_id', '=', $office);
            }

            if ($year)
            {
                $offers->whereRaw('YEAR(offer_date) = ?', [$year]);
            }

            $offers = $offers->orderBy('offer_date', 'desc')->orderBy('id', 'desc')->paginate(30);

            foreach ($offers as $offer)
            {
                //set default offer status
                $offer->status = trans('main.not_realized');

                //set offer status
                if ($offer->realized == 'T')
                {
                    $offer->status = trans('main.realized');
                }

                //call getOfferSum method to get offer sum
                $offer_sum = $this->getOfferSum($offer->id);

                //set offer sum
                $offer->sum = number_format($offer_sum, 2, ',', '.');

                //set default int pdf status
                $offer->int_pdf = 'F';

                //check language and currency
                if ($offer->language_id != 1 || $offer->currency_id != 1)
                {
                    $offer->int_pdf = 'T';
                }
            }

            return ['status' => 1, 'data' => $offers];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert offer
    public function insertOffer($office, $client, $language, $payment_type, $currency, $input_currency, $valid_date, $tax, $note,
        $int_note, $products, $notes)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getUserId method from UserRepository to get user id
            $user_id = $this->getUserId();

            //start transaction
            DB::beginTransaction();

            //call getNextOfferId method to get next offer id
            $response = $this->getNextOfferId($company_id, $office);

            //format valid date
            $valid_date = date('Y-m-d', strtotime($valid_date));

            $currency_ratio = 1;

            //if currency != 1 calculate currency ration
            if ($currency != 1)
            {
                //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                $repo = new CompanyRepository;
                $currency_response = $repo->calculateCurrencyRatio($currency);

                //if response status = 0 return error message
                if ($currency_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }

                $currency_ratio = $currency_response['data'];
            }

            $offer = new Offer;
            $offer->company_id = $company_id;
            $offer->user_id = $user_id;
            $offer->doc_number = $response['doc_number'];
            $offer->offer_id = $response['offer_id'];
            $offer->office_id = $office;
            $offer->offer_date = DB::raw('NOW()');
            $offer->client_id = $client;
            $offer->language_id = $language;
            $offer->payment_type_id = $payment_type;
            $offer->currency_id = $currency;
            $offer->input_currency_id = $input_currency;
            $offer->currency_ratio = $currency_ratio;
            $offer->valid_date = $valid_date;
            $offer->tax = $tax;
            $offer->note = $note;
            $offer->int_note = $int_note;
            $offer->save();

            foreach ($products as $product)
            {
                //get product tax group
                $tax_group = Product::find($product['id'])->tax_group_id;

                //insert offer product
                $offer_product = new OfferProduct;
                $offer_product->offer_id = $offer->id;
                $offer_product->product_id = $product['id'];
                $offer_product->quantity = $product['quantity'];
                $offer_product->price = $product['price'];
                $offer_product->custom_price = $product['custom_price'];
                $offer_product->brutto = $product['brutto'];
                $offer_product->tax_group_id = $tax_group;
                $offer_product->rebate = $product['rebate'];
                $offer_product->note = $product['note'];
                $offer_product->save();
            }

            //insert offer notes if exist
            if ($notes)
            {
                foreach ($notes as $note)
                {
                    if ($note['note'] != '')
                    {
                        $offer_note = new OfferNote;
                        $offer_note->offer_id = $offer->id;
                        $offer_note->note = $note['note'];
                        $offer_note->save();
                    }
                }
            }

            //commit transaction
            DB::commit();

            //set insert offer flash
            Session::flash('success_message', trans('main.offer_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get offer details
    public function getOfferDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offer = Offer::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->where('realized', '=', 'F')->first();

            //if offer doesn't exist return error status
            if (!$offer)
            {
                return ['status' => 0];
            }

            //format offer and valid date
            $offer->offer_date = date('d.m.Y. H:i:s', strtotime($offer->offer_date));
            $offer->valid_date = date('d.m.Y.', strtotime($offer->valid_date));

            $client_address = '';
            $client_oib = '';

            if ($offer->client->address)
            {
                $client_address .= $offer->client->address;

                if ($offer->client->city)
                {
                    $client_address .= ', '.$offer->client->city;
                }

                if ($offer->client->oib && $offer->client->oib != '')
                {
                    $client_oib .= $offer->client->oib;
                }
            }

            //add client address and oib to offer object
            $offer->client_address = $client_address;
            $offer->client_oib = $client_oib;

            /*
            |--------------------------------------------------------------------------
            | Offer products
            |--------------------------------------------------------------------------
            */

            //set tax array
            $tax_array = [];

            //set tax groups array
            $tax_groups_array = [];

            $counter = 1;
            $currency_ratio = 1;
            $rebate_sum = 0;
            $total = 0;
            $grand_total = 0;

            //if offer currency != 1 calculate currency ration
            if ($offer->currency_id != 1)
            {
                //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                $repo = new CompanyRepository;
                $currency_response = $repo->calculateCurrencyRatio($offer->currency_id);

                //if response status = 0 return error message
                if ($currency_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }

                $currency_ratio = $currency_response['data'];
            }

            //get products
            $products = OfferProduct::with('product')
                ->where('offer_id', '=', $id)->orderBy('id', 'asc')->get();

            foreach ($products as $product)
            {
                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //call getProductsListPrice method to get product price
                $price_response = $this->getProductsListPrice($product, $currency_ratio, $product->price,
                    $product->custom_price, $product->brutto, null);

                //get rebate amount
                $rebate = (($price_response['price'] / 100) * $product->rebate) * $product->quantity;

                //get product sum
                $sum = ($price_response['price'] * $product->quantity) - $rebate;

                $total += $sum;

                $rebate_sum += $rebate;

                //if tax = 'T' format tax groups array and add tax sum to grand total
                if ($offer->tax == 'T')
                {
                    $tax_groups_array[$price_response['tax_percentage']][] = $sum / 100 * $price_response['tax_percentage'];

                    $grand_total += $sum + ($sum / 100 * $price_response['tax_percentage']);
                }
                else
                {
                    $grand_total += $sum;
                }

                //add product unit, list quantity, list price, tax, rebate sum, sum and counter to product object
                $product->unit = trans('main.'.$unit);
                $product->list_quantity = number_format($product->quantity, 2, ',' , '.');
                $product->list_price = number_format($price_response['price'], 2, ',' , '.');
                $product->tax = number_format($price_response['tax_percentage'], 2, ',' , '.');
                $product->rebate_sum = number_format($rebate, 2, ',' , '.');
                $product->sum = number_format($sum, 2, ',' , '.');
                $product->counter = $counter;

                //format note - remove breaks
                $product->note = str_replace(["\r", "\n"], ' ', $product->note);

                $counter++;
            }

            //add products to offer object
            $offer->products = $products;

            if ($offer->tax == 'T')
            {
                //sort array - high to low
                arsort($tax_groups_array);

                foreach ($tax_groups_array as $tax => $tax_amounts)
                {
                    $taxes_sum = 0;

                    //get all sum amounts of current tax
                    foreach ($tax_amounts as $tax_amount)
                    {
                        $taxes_sum += $tax_amount;
                    }

                    //make array with tax and sum keys
                    $tax_array[] = array('tax' => $tax, 'sum' => number_format($taxes_sum, 2, "," , "."));
                }
            }

            //add tax array, total, rebate sum and grand total to offer object
            $offer->tax_array = $tax_array;
            $offer->total = number_format($total, 2, ',' , '.');
            $offer->rebate_sum = number_format($rebate_sum, 2, ',' , '.');
            $offer->grand_total = number_format($grand_total, 2, ',' , '.');

            /*
            |--------------------------------------------------------------------------
            | Offer notes
            |--------------------------------------------------------------------------
            */

            //get offer notes
            $offer_notes = OfferNote::select('id', 'note')->where('offer_id', '=', $id)->get();

            //add offer notes to offer object
            $offer->offer_notes = $offer_notes;

            return ['status' => 1, 'data' => $offer];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //update offer
    public function updateOffer($id, $date, $client, $language, $payment_type, $currency, $input_currency, $valid_date, $tax,
        $note, $int_note, $products, $notes, $create_invoice, $register, $due_date, $merchandise)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //format offer date and valid date
            $date = date('Y-m-d H:i:s', strtotime($date));
            $valid_date = date('Y-m-d', strtotime($valid_date));

            $currency_ratio = 1;

            //if currency != 1 calculate currency ration
            if ($currency != 1)
            {
                //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                $repo = new CompanyRepository;
                $currency_response = $repo->calculateCurrencyRatio($currency);

                //if response status = 0 return error message
                if ($currency_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }

                $currency_ratio = $currency_response['data'];
            }

            $offer = Offer::find($id);
            $offer->offer_date = $date;
            $offer->client_id = $client;
            $offer->language_id = $language;
            $offer->payment_type_id = $payment_type;
            $offer->currency_id = $currency;
            $offer->input_currency_id = $input_currency;
            $offer->currency_ratio = $currency_ratio;
            $offer->valid_date = $valid_date;
            $offer->tax = $tax;
            $offer->note = $note;
            $offer->int_note = $int_note;
            $offer->save();

            /*
            |--------------------------------------------------------------------------
            | Offer products
            |--------------------------------------------------------------------------
            */

            //set exclude op ids array
            $exclude_op_ids_array = [];

            foreach ($products as $product)
            {
                //if op id exists update product, else insert new product
                if ($product['op_id'])
                {
                    //update offer product
                    $offer_product = OfferProduct::where('offer_id', '=', $id)->where('id', '=', $product['op_id'])->first();
                    $offer_product->quantity = $product['quantity'];
                    $offer_product->price = $product['price'];
                    $offer_product->custom_price = $product['custom_price'];
                    $offer_product->brutto = $product['brutto'];
                    $offer_product->rebate = $product['rebate'];
                    $offer_product->note = $product['note'];
                    $offer_product->save();
                }
                else
                {
                    //get product tax group
                    $tax_group = Product::find($product['id'])->tax_group_id;

                    //insert offer product
                    $offer_product = new OfferProduct;
                    $offer_product->offer_id = $id;
                    $offer_product->product_id = $product['id'];
                    $offer_product->quantity = $product['quantity'];
                    $offer_product->price = $product['price'];
                    $offer_product->custom_price = $product['custom_price'];
                    $offer_product->brutto = $product['brutto'];
                    $offer_product->tax_group_id = $tax_group;
                    $offer_product->rebate = $product['rebate'];
                    $offer_product->note = $product['note'];
                    $offer_product->save();
                }

                //add op id to exclude array
                $exclude_op_ids_array[] = $offer_product->id;
            }

            //delete all offer products which are not in exclude op id array
            OfferProduct::where('offer_id', '=', $id)->whereNotIn('id', $exclude_op_ids_array)->delete();

            /*
            |--------------------------------------------------------------------------
            | Offer notes
            |--------------------------------------------------------------------------
            */

            //set exclude note ids array
            $exclude_note_ids_array = [];

            //insert offer notes if exist
            if ($notes)
            {
                foreach ($notes as $note)
                {
                    if ($note['note'] != '')
                    {
                        //if note id exists update note, else insert new note
                        if ($note['note_id'])
                        {
                            //update offer note
                            $offer_note = OfferNote::where('offer_id', '=', $id)->where('id', '=', $note['note_id'])->first();
                            $offer_note->note = $note['note'];
                            $offer_note->save();
                        }
                        else
                        {
                            //insert offer note
                            $offer_note = new OfferNote;
                            $offer_note->offer_id = $id;
                            $offer_note->note = $note['note'];
                            $offer_note->save();
                        }

                        //add note id to exclude array
                        $exclude_note_ids_array[] = $offer_note->id;
                    }
                }
            }

            //delete all offer notes which are not in exclude note id array
            OfferNote::where('offer_id', '=', $id)->whereNotIn('id', $exclude_note_ids_array)->delete();

            //if create invoice = 'T' copy offer info invoice
            if ($create_invoice == 'T')
            {
                //format due date
                $due_date = date('Y-m-d', strtotime($due_date));

                //call getNextInvoiceId method from InvoiceRepository to get next invoice id
                $repo = new InvoiceRepository;
                $invoice_next_id_response = $repo->getNextInvoiceId($offer->company_id, 'F', $offer->office_id, $register);

                //call getReferenceNumber method from InvoiceRepository to get reference number
                $repo = new InvoiceRepository;
                $reference_number_response = $repo->getReferenceNumber($offer->office_id, $register);

                //if response status = '0' return error message
                if ($reference_number_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }

                $invoice = new Invoice;
                $invoice->company_id = $offer->company_id;
                $invoice->user_id = $offer->user_id;
                $invoice->doc_number = $invoice_next_id_response['doc_number'];
                $invoice->invoice_id = $invoice_next_id_response['invoice_id'];
                $invoice->retail = 'F';
                $invoice->office_id = $offer->office_id;
                $invoice->register_id = $register;
                $invoice->invoice_date = DB::raw('NOW()');
                $invoice->client_id = $offer->client_id;
                $invoice->language_id = $offer->language_id;
                $invoice->payment_type_id = $offer->payment_type_id;
                $invoice->currency_id = $offer->currency_id;
                $invoice->input_currency_id = $offer->input_currency_id;
                $invoice->currency_ratio = $offer->currency_ratio;
                $invoice->due_date = $due_date;
                $invoice->note = $offer->note;
                $invoice->int_note = $offer->int_note;
                $invoice->reversed_id = 0;
                $invoice->tax = $offer->tax;
                $invoice->model = 'HR99';
                $invoice->reference_number = $reference_number_response['data'];
                $invoice->save();

                foreach ($products as $product)
                {
                    //get product tax group
                    $tax_group = Product::find($product['id'])->tax_group_id;

                    //insert invoice product
                    $invoice_product = new InvoiceProduct;
                    $invoice_product->invoice_id = $invoice->id;
                    $invoice_product->product_id = $product['id'];
                    $invoice_product->quantity = $product['quantity'];
                    $invoice_product->price = $product['price'];
                    $invoice_product->custom_price = $product['custom_price'];
                    $invoice_product->brutto = $product['brutto'];
                    $invoice_product->tax_group_id = $tax_group;
                    $invoice_product->rebate = $product['rebate'];
                    $invoice_product->note = $product['note'];
                    $invoice_product->save();
                }

                //if merchandise = 'T' create dispatch
                if ($merchandise == 'T')
                {
                    //call getNextDispatchId method from DispatchRepository to get next dispatch id
                    $repo = new DispatchRepository;
                    $dispatch_next_id_response = $repo->getNextDispatchId($offer->company_id);

                    $dispatch = new Dispatch;
                    $dispatch->company_id = $offer->company_id;
                    $dispatch->user_id = $offer->user_id;
                    $dispatch->doc_number = $dispatch_next_id_response['doc_number'];
                    $dispatch->dispatch_id = $dispatch_next_id_response['dispatch_id'];
                    $dispatch->dispatch_date = DB::raw('NOW()');
                    $dispatch->client_id = $client;
                    $dispatch->save();

                    foreach ($products as $product)
                    {
                        //get product tax group
                        $tax_group = Product::find($product['id'])->tax_group_id;

                        //insert dispatch product
                        $dispatch_product = new DispatchProduct;
                        $dispatch_product->dispatch_id = $dispatch->id;
                        $dispatch_product->product_id = $product['id'];
                        $dispatch_product->quantity = $product['quantity'];
                        $dispatch_product->price = $product['price'];
                        $dispatch_product->tax_group_id = $tax_group;
                        $dispatch_product->note = $product['note'];
                        $dispatch_product->save();
                    }
                }

                //set offer realized status to 'T'
                $offer->realized = 'T';
                $offer->save();
            }

            //commit transaction
            DB::commit();

            //set update offer flash
            Session::flash('success_message', trans('main.offer_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete offer
    public function deleteOffer($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offer = Offer::where('company_id', '=', $company_id)->where('id', '=', $id)->where('realized', '=', 'F')->first();

            //if offer doesn't exist return error status
            if (!$offer)
            {
                return ['status' => 0];
            }

            $offer->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //copy offer
    public function copyOffer($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offer = Offer::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if offer doesn't exist return error status
            if (!$offer)
            {
                return ['status' => 0];
            }

            //get offer products
            $products = OfferProduct::where('offer_id', '=', $id)->get();

            //get offer notes
            $notes = OfferNote::where('offer_id', '=', $id)->get();

            //start transaction
            DB::beginTransaction();

            //call getNextOfferId method to get next offer id
            $response = $this->getNextOfferId($company_id, $offer->office_id);

            $offer_model = new Offer;
            $offer_model->company_id = $company_id;
            $offer_model->user_id = $offer->user_id;
            $offer_model->doc_number = $response['doc_number'];
            $offer_model->offer_id = $response['offer_id'];
            $offer_model->office_id = $offer->office_id;
            $offer_model->offer_date = DB::raw('NOW()');
            $offer_model->client_id = $offer->client_id;
            $offer_model->language_id = $offer->language_id;
            $offer_model->payment_type_id = $offer->payment_type_id;
            $offer_model->currency_id = $offer->currency_id;
            $offer_model->input_currency_id = $offer->input_currency_id;
            $offer_model->currency_ratio = $offer->currency_ratio;
            $offer_model->valid_date = $offer->valid_date;
            $offer_model->tax = $offer->tax;
            $offer_model->note = $offer->note;
            $offer_model->int_note = $offer->int_note;
            $offer_model->save();

            foreach ($products as $product)
            {
                //insert offer product
                $offer_product = new OfferProduct;
                $offer_product->offer_id = $offer_model->id;
                $offer_product->product_id = $product->product_id;
                $offer_product->quantity = $product->quantity;
                $offer_product->price = $product->price;
                $offer_product->custom_price = $product->custom_price;
                $offer_product->brutto = $product->brutto;
                $offer_product->tax_group_id = $product->tax_group_id;
                $offer_product->rebate = $product->rebate;
                $offer_product->note = $product->note;
                $offer_product->save();
            }

            foreach ($notes as $note)
            {
                //insert offer note
                $offer_note = new OfferNote;
                $offer_note->offer_id = $offer_model->id;
                $offer_note->note = $note->note;
                $offer_note->save();
            }

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get next offer id
    private function getNextOfferId($company_id, $office_id)
    {
        //get current year
        $year = date('Y');

        //set offer year
        $offer_year = $year[2].$year[3];

        //set default doc number
        $doc_number = 1;

        //get max doc number
        $max_doc_number = Offer::where('company_id', '=', $company_id)->where('office_id', '=', $office_id)
            ->whereRaw('YEAR(offer_date) = ?', [$year])->max('doc_number');

        if ($max_doc_number)
        {
            //set doc_number
            $doc_number = $max_doc_number + 1;
        }

        //get office label
        $office_label = Office::find($office_id)->label;

        //set offer id
        $offer_id = $doc_number.'/'.$office_label.'/'.$offer_year;

        return ['offer_id' => $offer_id, 'doc_number' => $doc_number];
    }

    //get products
    public function getProducts($client, $currency, $tax, $products)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set products array
            $products_array = [];

            //set tax array
            $tax_array = [];

            //set tax groups array
            $tax_groups_array = [];

            if (count($products) > 0)
            {
                $currency_ratio = 1;
                $client_rebate = 0;
                $client_product_price = null;
                $rebate_sum = 0;
                $total = 0;
                $grand_total = 0;

                //if currency != 1 calculate currency ration
                if ($currency != 1)
                {
                    //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                    $repo = new CompanyRepository;
                    $currency_response = $repo->calculateCurrencyRatio($currency);

                    //if response status = '0' return error message
                    if ($currency_response['status'] == 0)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }

                    $currency_ratio = $currency_response['data'];
                }

                if ($client != 0)
                {
                    //set client rebate
                    $client_rebate = Client::find($client)->rebate;
                }

                foreach ($products as $key => $offer_product)
                {
                    $product = Product::with('unit')
                        ->select('unit_id', 'code', 'tax_group_id', 'name', 'price', 'description')
                        ->where('company_id', '=', $company_id)->where('id', '=', $offer_product['id'])->first();

                    //if product doesn't exist return error status
                    if (!$product)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }

                    $note = '';

                    if ($client != 0)
                    {
                        //get client product price
                        $client_product_price = ClientPrice::select('price')->where('client_id', '=', $client)
                            ->where('product_id', '=', $offer_product['id'])->first();
                    }

                    //call getProductsListPrice method to get product price
                    $price_response = $this->getProductsListPrice($product, $currency_ratio, $offer_product['price'],
                        $offer_product['custom_price'], $offer_product['brutto'], $client_product_price);

                    $product_tax = $price_response['tax_percentage'];

                    //set default product rebate
                    $product_rebate = $offer_product['rebate'];

                    //if price = '0' and client has rebate set client rebate as product rebate
                    if ($price_response['assign_client_rebate'] && $client_rebate)
                    {
                        $product_rebate = $client_rebate;
                    }

                    //get rebate amount
                    $rebate = (($price_response['price'] / 100) * $product_rebate) * $offer_product['quantity'];

                    //get product sum
                    $sum = ($price_response['price'] * $offer_product['quantity']) - $rebate;

                    $total += $sum;

                    $rebate_sum += $rebate;

                    //if tax = 'T' format tax groups array and add tax sum to grand total
                    if ($tax == 'T')
                    {
                        $tax_groups_array[$price_response['tax_percentage']][] = $sum / 100 * $price_response['tax_percentage'];

                        $grand_total += $sum + ($sum / 100 * $price_response['tax_percentage']);
                    }
                    else
                    {
                        $product_tax = 0;

                        $grand_total += $sum;
                    }

                    if ($offer_product['custom_price'] == 'F' && $offer_product['price'] == 0)
                    {
                        $note = $product->description;
                    }
                    else
                    {
                        if ($offer_product['note'])
                        {
                            $note = $offer_product['note'];
                        }
                    }

                    //add product to products array
                    $products_array[] = array('id' => $key, 'product_id' => $offer_product['id'], 'code' => $product->code,
                        'name' => $product->name, 'unit' => trans('main.'.$product->unit->code),
                        'quantity' => $offer_product['quantity'],
                        'list_quantity' => number_format($offer_product['quantity'], 2, ',' , '.'),
                        'price' => $price_response['object_price'],
                        'list_price' => number_format($price_response['price'], 2, ',' , '.'),
                        'custom_price' => $offer_product['custom_price'], 'brutto' => $offer_product['brutto'],
                        'note' => htmlspecialchars($note), 'tax' => number_format($product_tax, 2, ',' , '.'),
                        'rebate' => $product_rebate, 'rebate_sum' => number_format($rebate, 2, ',' , '.'),
                        'sum' => number_format($sum, 2, ',' , '.'), 'op_id' => $offer_product['op_id']);
                }

                if ($tax == 'T')
                {
                    //sort array - high to low
                    arsort($tax_groups_array);

                    foreach ($tax_groups_array as $tax => $tax_amounts)
                    {
                        $taxes_sum = 0;

                        //get all sum amounts of current tax
                        foreach ($tax_amounts as $tax_amount)
                        {
                            $taxes_sum += $tax_amount;
                        }

                        //make array with tax and sum keys
                        $tax_array[] = array('tax' => $tax, 'sum' => number_format($taxes_sum, 2, ',' , '.'));
                    }
                }

                return ['status' => 1, 'quantity' => 1, 'products' => $products_array, 'tax_array' => $tax_array,
                    'total' => number_format($total, 2, ',' , '.'), 'rebate_sum' => number_format($rebate_sum, 2, ',' , '.'),
                    'grand_total' => number_format($grand_total, 2, ',' , '.')];
            }

            return ['status' => 1, 'products' => $products_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get products list price
    private function getProductsListPrice($product_object, $currency_ratio, $price, $custom_price, $brutto, $client_product_price)
    {
        //set assign client rebate variable
        $assign_client_rebate = false;

        //get current date
        $today = date('Y-m-d');

        //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
        $repo = new TaxGroupRepository;
        $tax_percentage_response = $repo->getCurrentTaxPercentage($product_object->tax_group_id, $today);

        //if response status = '0' return error message
        if ($tax_percentage_response['status'] == 0)
        {
            return ['status' => 0, 'error' => trans('errors.current_tax_percentage')];
        }

        $tax_percentage = $tax_percentage_response['data'];

        if ($custom_price == 'T')
        {
            if ($brutto == 'F')
            {
                $product_price = $price;
            }
            else
            {
                $product_price = $price / (1 + ($tax_percentage / 100));
            }

            $object_price = $price;
        }
        else
        {
            if ($price == 0)
            {
                if ($client_product_price)
                {
                    $product_price = $client_product_price->price * $currency_ratio;

                    $object_price = $client_product_price->price;
                }
                else
                {
                    $product_price = $product_object->price * $currency_ratio;

                    $object_price = $product_object->price;

                    $assign_client_rebate = true;
                }
            }
            else
            {
                if ($brutto == 'F')
                {
                    $product_price = $price * $currency_ratio;
                }
                else
                {
                    $product_price = ($price * $currency_ratio) / (1 + ($tax_percentage / 100));
                }

                $object_price = $price;
            }
        }

        return ['price' => $product_price, 'object_price' => $object_price, 'tax_percentage' => $tax_percentage,
            'assign_client_rebate' => $assign_client_rebate];
    }

    //get offer sum
    public function getOfferSum($offer_id)
    {
        $offer = Offer::select(DB::raw('DATE_FORMAT(offer_date, "%Y-%m-%d") AS date'), 'currency_ratio', 'currency_id',
                'input_currency_id', 'tax')
            ->where('id', '=', $offer_id)->first();

        $products = OfferProduct::select('quantity', 'price', 'custom_price', 'brutto', 'tax_group_id', 'rebate')
            ->where('offer_id', '=', $offer_id)->get();

        $grand_total = 0;

        foreach ($products as $product)
        {
            $tax_percentage = 0;

            //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
            $repo = new TaxGroupRepository;
            $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $offer->date);

            //if response status = 0 return error message
            if ($tax_percentage_response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.current_tax_percentage')];
            }

            $tax_percentage = $tax_percentage_response['data'];

            //check custom price
            if ($product->custom_price == 'T')
            {
                //compare currency and input currency
                if ($offer->currency_id == $offer->input_currency_id)
                {
                    if ($product->brutto == 'F')
                    {
                        $product_price = $product->price / $offer->currency_ratio;
                    }
                    else
                    {
                        $product_price = ($product->price / $offer->currency_ratio) / (1 + ($tax_percentage / 100));
                    }
                }
                else
                {
                    if ($product->brutto == 'F')
                    {
                        $product_price = $product->price;
                    }
                    else
                    {
                        $product_price = $product->price / (1 + ($tax_percentage / 100));
                    }
                }
            }
            else
            {
                if ($product->brutto == 'F')
                {
                    $product_price = $product->price;
                }
                else
                {
                    $product_price = $product->price / (1 + ($tax_percentage / 100));
                }
            }

            $rebate = (($product_price / 100) * $product->rebate) * $product->quantity;

            $sum = ($product_price * $product->quantity) - $rebate;

            //if tax = 'T' add tax sum to grand total
            if ($offer->tax == 'T')
            {
                $grand_total += $sum + ($sum / 100 * $tax_percentage);
            }
            else
            {
                $grand_total += $sum;
            }
        }

        return $grand_total;
    }

    //pdf data
    public function pdfData($type, $id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offer = Offer::with('client', 'office', 'paymentType', 'currency', 'user')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)
                ->first();

            //if offer doesn't exist return error status
            if (!$offer)
            {
                return ['status' => 0];
            }

            //get products
            $products = OfferProduct::with('product')
                ->where('offer_id', '=', $id)->orderBy('id', 'asc')->get();

            //get offer notes
            $offer->notes = OfferNote::select('note')->where('offer_id', '=', $id)->get();

            $offer_date = date('Y-m-d', strtotime($offer->offer_date));

            $company = Company::find($company_id);

            $admin = $this->getCompanyAdmin($company_id);

            //set language
            if ($type == 1 && $offer->language_id != 1)
            {
                //set app language
                App::setLocale('en');
            }

            $offer->logo = public_path().'/logo/'.$company->logo;
            $offer->date = date('d.m.Y. H:i', strtotime($offer->offer_date));
            $offer->valid_date = date('d.m.Y.', strtotime($offer->valid_date));

            /*
            |--------------------------------------------------------------------------
            |--------------------------------------------------------------------------
            */

            //set products array
            $products_array = [];

            //set tax array
            $tax_array = [];

            //set tax groups array
            $tax_groups_array = [];

            //set tax groups notes array
            $tax_groups_notes_array = [];

            //set tax notes array
            $tax_notes_array = [];

            //set sum array
            $sum_array = [];

            $rebate_sum = 0;
            $dom_rebate_sum = 0;
            $total = 0;
            $dom_total = 0;
            $tax_sum = 0;
            $dom_tax_sum = 0;
            $grand_total = 0;
            $dom_grand_total = 0;
            $i = 1;

            foreach ($products as $product)
            {
                //add tax group to tax groups notes array
                $tax_groups_notes_array[] = $product->tax_group_id;

                //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
                $repo = new TaxGroupRepository;
                $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $offer_date);

                //if response status = 0 return error message
                if ($tax_percentage_response['status'] == 0)
                {
                    return ['status' => 0];
                }

                $tax_percentage = $tax_percentage_response['data'];

                //check custom price
                if ($product->custom_price == 'T')
                {
                    //compare currency and input currency
                    if ($offer->currency_id == $offer->input_currency_id)
                    {
                        if ($product->brutto == 'F')
                        {
                            $product_price = $product->price;
                            $dom_product_price = $product->price / $offer->currency_ratio;
                        }
                        else
                        {
                            $product_price = $product->price / (1 + ($tax_percentage / 100));
                            $dom_product_price = ($product->price / $offer->currency_ratio) / (1 + ($tax_percentage / 100));
                        }
                    }
                    else
                    {
                        if ($product->brutto == 'F')
                        {
                            $product_price = $product->price * $offer->currency_ratio;
                            $dom_product_price = $product->price;
                        }
                        else
                        {
                            $product_price = ($product->price * $offer->currency_ratio) / (1 + ($tax_percentage / 100));
                            $dom_product_price = $product->price / (1 + ($tax_percentage / 100));
                        }
                    }
                }
                else
                {
                    if ($product->brutto == 'F')
                    {
                        $product_price = $product->price * $offer->currency_ratio;
                        $dom_product_price = $product->price;
                    }
                    else
                    {
                        $product_price = ($product->price / (1 + ($tax_percentage / 100))) * $offer->currency_ratio;
                        $dom_product_price = $product->price / (1 + ($tax_percentage / 100));
                    }
                }

                //get rebate amount
                $rebate = (($product_price / 100) * $product->rebate) * $product->quantity;

                $dom_rebate = (($dom_product_price / 100) * $product->rebate) * $product->quantity;

                //get product sum
                $sum = ($product_price * $product->quantity) - $rebate;

                $dom_sum = ($dom_product_price * $product->quantity) - $dom_rebate;

                $total += $sum;

                $dom_total += $dom_sum;

                $rebate_sum += $rebate;

                $dom_rebate_sum += $dom_rebate;

                //if tax = 'T' add tax sum to grand total
                if ($offer->tax == 'T')
                {
                    $tax_sum += $sum / 100 * $tax_percentage;

                    $dom_tax_sum += $dom_sum / 100 * $tax_percentage;

                    if ($type == 2)
                    {
                        $tax_groups_array[$tax_percentage][] = $dom_sum / 100 * $tax_percentage;
                    }
                    else
                    {
                        $tax_groups_array[$tax_percentage][] = $sum / 100 * $tax_percentage;
                    }

                    $grand_total += $sum + ($sum / 100 * $tax_percentage);

                    $dom_grand_total += $dom_sum + ($dom_sum / 100 * $tax_percentage);
                }
                else
                {
                    $tax_percentage = 0;

                    $grand_total += $sum;

                    $dom_grand_total += $dom_sum;
                }

                $quantity = $product->quantity;

                if ($type == 2)
                {
                    $dom_rebate = number_format($dom_rebate, 2, ',', '.');
                }
                else
                {
                    $rebate = number_format($rebate, 2, ',', '.');
                }

                $var_prefix = '';

                if ($type == 2)
                {
                    $var_prefix = 'dom_';
                }

                $array_product_price = ${$var_prefix.'product_price'};
                $array_rebate = ${$var_prefix.'rebate'};
                $array_sum = ${$var_prefix.'sum'};

                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //add product to products array
                $products_array[] = ['rb' => $i, 'code' => $product->product->code, 'name' => $product->product->name,
                    'unit' => trans('main.'.$unit), 'quantity' => $quantity,
                    'price' => number_format($array_product_price, 2, ',', '.'), 'rebate' => $product->rebate,
                    'note' => $product->note, 'tax' => number_format($tax_percentage, 2, ',', '.'), 'rebate_sum' => $array_rebate,
                    'sum' => number_format($array_sum, 2, ',', '.')];

                $i++;
            }

            //if tax = 'T' create tax array
            if ($offer->tax == 'T')
            {
                //sort array - high to low
                arsort($tax_groups_array);

                foreach ($tax_groups_array as $tax => $tax_amounts)
                {
                    $taxes_sum = 0;

                    //get all sum amounts of current tax
                    foreach ($tax_amounts as $tax_amount)
                    {
                        $taxes_sum += $tax_amount;
                    }

                    //add tax and tax sum to tax array
                    $tax_array[] = ['tax' => $tax, 'sum' => number_format($taxes_sum, 2, ',', '.')];
                }
            }

            $sum_array['grand_total'] = number_format($grand_total, 2, ',', '.');
            $sum_array['dom_grand_total'] = number_format($dom_grand_total, 2, ',', '.');

            if ($type == 2)
            {
                $sum_array['total'] = number_format($dom_total, 2, ',', '.');
                $sum_array['rebate_sum'] = number_format($dom_rebate_sum, 2, ',', '.');
            }
            else
            {
                $sum_array['total'] = number_format($total, 2, ',', '.');
                $sum_array['rebate_sum'] = number_format($rebate_sum, 2, ',', '.');
            }

            //remove duplicate tax groups
            $tax_groups_notes_array = array_unique($tax_groups_notes_array);

            foreach ($tax_groups_notes_array as $group_id)
            {
                //add tax group note to tax notes array
                $tax_notes_array[] = TaxGroup::find($group_id)->note;
            }

            return ['status' => 1, 'type' => $type, 'offer' => $offer, 'company' => $company, 'products' => $products_array,
                'tax_array' => $tax_array, 'tax_notes_array' => $tax_notes_array, 'sum_array' => $sum_array, 'admin' => $admin];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //send email
    public function sendEmail($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offer = Offer::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if offer doesn't exist return error message
            if (!$offer)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //if client email doesn't exist return warning message
            if (!$offer->client->email)
            {
                return ['status' => 2, 'warning' => trans('errors.send_email_no_email')];
            }

            //call pdfData method to get pdf data
            $data = $this->pdfData(1, $id);

            //if response status = 0 return error message
            if ($data['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //create temp user and send mail to client
            (new User)->forceFill([
                'name' => $offer->client->name,
                'email' => $offer->client->email
            ])->notify(new documentEmail(Company::find($company_id)->name, $offer, $data, 'T'));

            return ['status' => 1, 'success' => trans('main.email_sent')];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }
}
