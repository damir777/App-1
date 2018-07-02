<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Notifications\documentEmail;
use App\Invoice;
use App\InvoiceProduct;
use App\Product;
use App\Unit;
use App\Office;
use App\Register;
use App\InvoiceNote;
use App\Company;
use App\Dispatch;
use App\DispatchProduct;
use App\PaymentSlip;
use App\Contract;
use App\TaxGroup;
use App\User;
use App\Client;
use App\ClientPrice;

class InvoiceRepository extends UserRepository
{
    //get invoices
    public function getInvoices($type, $search_string, $office, $register, $year)
    {
        try
        {
            $retail = 'F';

            if ($type == 1)
            {
                $retail = 'T';
            }

            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $invoices = Invoice::with('client', 'paymentType')
                ->select('id', 'invoice_id', 'retail', DB::raw('DATE_FORMAT(invoice_date, "%d.%m.%Y.") AS date'), 'client_id',
                    'language_id', 'payment_type_id', 'currency_id', 'paid', 'reversed', 'reversed_id', 'jir', 'partial_paid_sum')
                ->where('company_id', '=', $company_id)->where('retail', '=', $retail);

            if ($search_string)
            {
                $invoices->whereHas('client', function($query) use ($search_string) {
                    $query->whereRaw('name LIKE ?', ['%'.$search_string.'%']);
                });
            }

            if ($office)
            {
                $invoices->where('office_id', '=', $office);
            }

            if ($register)
            {
                $invoices->where('register_id', '=', $register);
            }

            if ($year)
            {
                $invoices->whereRaw('YEAR(invoice_date) = ?', [$year]);
            }

            $invoices = $invoices->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(30);

            foreach ($invoices as $invoice)
            {
                //set default invoice status
                $invoice->status = trans('main.unpaid');

                //set invoice status
                if ($invoice->paid == 'T')
                {
                    if ($invoice->partial_paid_sum)
                    {
                        $partial_sum = number_format($invoice->partial_paid_sum, 2, ',', '.');

                        if ($invoice->reversed == 'T')
                        {
                            $partial_sum = '-'.$partial_sum;
                        }

                        $invoice->status = trans('main.partial_paid').' ('.$partial_sum.')';
                    }
                    else
                    {
                        $invoice->status = trans('main.paid');
                    }
                }

                //call getInvoiceSum method to get invoice sum
                $invoice_sum = $this->getInvoiceSum($invoice->id);

                $sum = number_format($invoice_sum, 2, ',', '.');

                if ($invoice->reversed == 'T')
                {
                    $sum = '-'.$sum;
                }

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

            return ['status' => 1, 'data' => $invoices];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert invoice
    public function insertInvoice($retail, $office, $register, $client, $language, $payment_type, $currency, $input_currency,
        $due_date, $delivery_date, $note, $int_note, $tax, $advance, $show_model, $model, $reference_number, $products, $notes,
        $merchandise, $print, $email)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getUserId method from UserRepository to get user id
            $user_id = $this->getUserId();

            //start transaction
            DB::beginTransaction();

            //call getNextInvoiceId method to get next invoice id
            $response = $this->getNextInvoiceId($company_id, $retail, $office, $register);

            //format due date
            $due_date = date('Y-m-d', strtotime($due_date));

            if ($delivery_date)
            {
                //format delivery date
                $delivery_date = date('Y-m-d', strtotime($delivery_date));
            }

            $currency_ratio = 1;

            //if currency != 1 calculate currency ratio
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

            $paid = 'F';

            if ($retail == 'T')
            {
                $paid = 'T';
                $tax = 'T';
            }

            $invoice = new Invoice;
            $invoice->company_id = $company_id;
            $invoice->user_id = $user_id;
            $invoice->doc_number = $response['doc_number'];
            $invoice->invoice_id = $response['invoice_id'];
            $invoice->retail = $retail;
            $invoice->office_id = $office;
            $invoice->register_id = $register;
            $invoice->invoice_date = DB::raw('NOW()');
            $invoice->client_id = $client;
            $invoice->language_id = $language;
            $invoice->payment_type_id = $payment_type;
            $invoice->currency_id = $currency;
            $invoice->input_currency_id = $input_currency;
            $invoice->currency_ratio = $currency_ratio;
            $invoice->due_date = $due_date;
            $invoice->delivery_date = $delivery_date;
            $invoice->note = $note;
            $invoice->int_note = $int_note;
            $invoice->paid = $paid;
            $invoice->reversed_id = 0;
            $invoice->tax = $tax;
            $invoice->advance = $advance;
            $invoice->show_model = $show_model;
            $invoice->model = $model;
            $invoice->reference_number = $reference_number;
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

            //insert invoice notes if exist
            if ($notes)
            {
                foreach ($notes as $note)
                {
                    if ($note['note'] != '')
                    {
                        $invoice_note = new InvoiceNote;
                        $invoice_note->invoice_id = $invoice->id;
                        $invoice_note->note = $note['note'];
                        $invoice_note->save();
                    }
                }
            }

            //if merchandise = 'T' insert new dispatch
            if ($merchandise == 'T')
            {
                //call getNextDispatchId method from DispatchRepository to get next dispatch id
                $repo = new DispatchRepository;
                $response = $repo->getNextDispatchId($company_id);

                $dispatch = new Dispatch;
                $dispatch->company_id = $company_id;
                $dispatch->user_id = $user_id;
                $dispatch->doc_number = $response['doc_number'];
                $dispatch->dispatch_id = $response['dispatch_id'];
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

            $fiscalization = false;

            //if retail = 'T' make fiscalization
            if ($retail == 'T')
            {
                //if payment type = 'Gotovina' create new payment slip
                if ($payment_type == 1)
                {
                    //call getInvoiceSum method to get invoice sum
                    $sum = $this->getInvoiceSum($invoice->id);

                    //call insertPaymentSlip method from RegisterReportRepository to insert payment slip
                    $repo = new RegisterReportRepository;
                    $payment_slip_response = $repo->insertPaymentSlip('T', '', $office, '', 'mp račun',
                        'rn. br. '.$invoice->invoice_id, $sum, $invoice->id, $client);

                    //if response status = 0 return error message
                    if ($payment_slip_response['status'] == 0)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }
                }

                //call makeFiscalization method to make fiscalization
                $fiscalization_response = $this->makeFiscalization($company_id, $invoice, 'F');

                //if response status = 0 return error message
                if ($fiscalization_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }
                elseif ($fiscalization_response['status'] == 1)
                {
                    $fiscalization = true;
                }
            }

            //if print = 'T' set print flash
            if ($print == 'T')
            {
                Session::flash('print', $invoice->id);
            }

            //if email variable = 'T' send email to client
            if ($email == 'T')
            {
                //call sendEmail method to send email to client
                $email_response = $this->sendEmail($invoice->id);

                //if response status !=  return warning/error message
                if ($email_response['status'] != 1)
                {
                    return $email_response;
                }
            }

            //commit transaction
            DB::commit();

            //set insert invoice flash
            Session::flash('success_message', trans('main.invoice_insert'));

            if ($retail == 'T' && !$fiscalization)
            {
                //set fiscalization flash
                Session::flash('error_message', trans('errors.fiscalization_error'));
            }

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get invoice details
    public function getInvoiceDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $invoice = Invoice::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->where('retail', '=', 'F')
                ->where('reversed_id', '=', 0)
                ->where(function($query) {
                    $query->where('paid', '=', 'F')->orWhereNotNull('partial_paid_sum');
                })
                ->first();

            //if invoice doesn't exist return error status
            if (!$invoice)
            {
                return ['status' => 0];
            }

            //format invoice and due date
            $invoice->invoice_date = date('d.m.Y. H:i:s', strtotime($invoice->invoice_date));
            $invoice->due_date = date('d.m.Y.', strtotime($invoice->due_date));

            if ($invoice->delivery_date)
            {
                //format delivery date
                $invoice->delivery_date = date('d.m.Y.', strtotime($invoice->delivery_date));
            }

            //set default invoice status
            $invoice->status = 1;

            if ($invoice->partial_paid_sum)
            {
                $invoice->status = 3;
            }

            $client_address = '';
            $client_oib = '';

            if ($invoice->client->address)
            {
                $client_address .= $invoice->client->address;

                if ($invoice->client->city)
                {
                    $client_address .= ', '.$invoice->client->city;
                }

                if ($invoice->client->oib && $invoice->client->oib != '')
                {
                    $client_oib .= $invoice->client->oib;
                }
            }

            //add client address and oib to invoice object
            $invoice->client_address = $client_address;
            $invoice->client_oib = $client_oib;

            /*
            |--------------------------------------------------------------------------
            | Invoice products
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

            //if contract currency != 1 calculate currency ration
            if ($invoice->currency_id != 1)
            {
                //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                $repo = new CompanyRepository;
                $currency_response = $repo->calculateCurrencyRatio($invoice->currency_id);

                //if response status = 0 return error message
                if ($currency_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }

                $currency_ratio = $currency_response['data'];
            }

            //get products
            $products = InvoiceProduct::with('product')
                ->where('invoice_id', '=', $id)->orderBy('id', 'asc')->get();

            foreach ($products as $product)
            {
                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //call getProductsListPrice method to get product price
                $price_response = $this->getProductsListPrice($product, $currency_ratio, $product->price, $product->custom_price,
                    $product->brutto, null);

                $product_tax = $price_response['tax_percentage'];

                //get rebate amount
                $rebate = (($price_response['price'] / 100) * $product->rebate) * $product->quantity;

                //get product sum
                $sum = ($price_response['price'] * $product->quantity) - $rebate;

                $total += $sum;

                $rebate_sum += $rebate;

                //if tax = 'T' format tax groups array and add tax sum to grand total
                if ($invoice->tax == 'T')
                {
                    $tax_groups_array[$price_response['tax_percentage']][] = $sum / 100 * $price_response['tax_percentage'];

                    $grand_total += $sum + ($sum / 100 * $price_response['tax_percentage']);
                }
                else
                {
                    $product_tax = 0;

                    $grand_total += $sum;
                }

                //add product unit, list quantity, list price, tax, rebate sum, sum and counter to product object
                $product->unit = trans('main.'.$unit);
                $product->list_quantity = number_format($product->quantity, 2, ',' , '.');
                $product->list_price = number_format($price_response['price'], 2, ',' , '.');
                $product->tax = number_format($product_tax, 2, ',' , '.');
                $product->rebate_sum = number_format($rebate, 2, ',' , '.');
                $product->sum = number_format($sum, 2, ',' , '.');
                $product->counter = $counter;

                //format note - remove breaks
                $product->note = str_replace(["\r", "\n"], ' ', $product->note);

                $counter++;
            }

            //add products to invoice object
            $invoice->products = $products;

            if ($invoice->tax == 'T')
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

            //add tax array, total, rebate sum and grand total to invoice object
            $invoice->tax_array = $tax_array;
            $invoice->total = number_format($total, 2, ',' , '.');
            $invoice->rebate_sum = number_format($rebate_sum, 2, ',' , '.');
            $invoice->grand_total = number_format($grand_total, 2, ',' , '.');

            /*
            |--------------------------------------------------------------------------
            | Invoice notes
            |--------------------------------------------------------------------------
            */

            //get invoice notes
            $invoice_notes = InvoiceNote::select('id', 'note')->where('invoice_id', '=', $id)->get();

            //add invoice notes to invoice object
            $invoice->invoice_notes = $invoice_notes;

            return ['status' => 1, 'data' => $invoice];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //update invoice
    public function updateInvoice($id, $date, $client, $language, $payment_type, $currency, $input_currency, $due_date,
        $delivery_date, $note, $int_note, $tax, $advance, $show_model, $model, $reference_number, $status, $partial_paid_sum,
        $products, $notes)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //format invoice date and due date
            $date = date('Y-m-d H:i:s', strtotime($date));
            $due_date = date('Y-m-d', strtotime($due_date));

            if ($delivery_date)
            {
                //format delivery date
                $delivery_date = date('Y-m-d', strtotime($delivery_date));
            }

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

            $paid = 'F';

            if ($status == 1 || $status == 2)
            {
                $partial_paid_sum = null;
            }

            if ($status == 2 || $status == 3)
            {
                $paid = 'T';
            }

            $invoice = Invoice::find($id);
            $invoice->invoice_date = $date;
            $invoice->client_id = $client;
            $invoice->language_id = $language;
            $invoice->payment_type_id = $payment_type;
            $invoice->currency_id = $currency;
            $invoice->input_currency_id = $input_currency;
            $invoice->currency_ratio = $currency_ratio;
            $invoice->due_date = $due_date;
            $invoice->delivery_date = $delivery_date;
            $invoice->note = $note;
            $invoice->int_note = $int_note;
            $invoice->paid = $paid;
            $invoice->tax = $tax;
            $invoice->advance = $advance;
            $invoice->show_model = $show_model;
            $invoice->model = $model;
            $invoice->reference_number = $reference_number;
            $invoice->partial_paid_sum = $partial_paid_sum;
            $invoice->save();

            /*
            |--------------------------------------------------------------------------
            | Invoice products
            |--------------------------------------------------------------------------
            */

            //set exclude ip ids array
            $exclude_ip_ids_array = [];

            foreach ($products as $product)
            {
                //if ip id exists update product, else insert new product
                if ($product['ip_id'])
                {
                    //update invoice product
                    $invoice_product = InvoiceProduct::where('invoice_id', '=', $id)->where('id', '=', $product['ip_id'])
                        ->first();
                    $invoice_product->quantity = $product['quantity'];
                    $invoice_product->price = $product['price'];
                    $invoice_product->custom_price = $product['custom_price'];
                    $invoice_product->brutto = $product['brutto'];
                    $invoice_product->rebate = $product['rebate'];
                    $invoice_product->note = $product['note'];
                    $invoice_product->save();
                }
                else
                {
                    //get product tax group
                    $tax_group = Product::find($product['id'])->tax_group_id;

                    //insert invoice product
                    $invoice_product = new InvoiceProduct;
                    $invoice_product->invoice_id = $id;
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

                //add ip id to exclude array
                $exclude_ip_ids_array[] = $invoice_product->id;
            }

            //delete all invoice products which are not in exclude ip id array
            InvoiceProduct::where('invoice_id', '=', $id)->whereNotIn('id', $exclude_ip_ids_array)->delete();

            /*
            |--------------------------------------------------------------------------
            | Invoice notes
            |--------------------------------------------------------------------------
            */

            //set exclude note ids array
            $exclude_note_ids_array = [];

            //insert invoice notes if exist
            if ($notes)
            {
                foreach ($notes as $note)
                {
                    if ($note['note'] != '')
                    {
                        //if note id exists update note, else insert new note
                        if ($note['note_id'])
                        {
                            //update invoice note
                            $invoice_note = InvoiceNote::where('invoice_id', '=', $id)->where('id', '=', $note['note_id'])
                                ->first();
                            $invoice_note->note = $note['note'];
                            $invoice_note->save();
                        }
                        else
                        {
                            //insert invoice note
                            $invoice_note = new InvoiceNote;
                            $invoice_note->invoice_id = $id;
                            $invoice_note->note = $note['note'];
                            $invoice_note->save();
                        }

                        //add note id to exclude array
                        $exclude_note_ids_array[] = $invoice_note->id;
                    }
                }
            }

            //delete all invoice notes which are not in exclude note id array
            InvoiceNote::where('invoice_id', '=', $id)->whereNotIn('id', $exclude_note_ids_array)->delete();

            //commit transaction
            DB::commit();

            //set update invoice flash
            Session::flash('success_message', trans('main.invoice_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete invoice
    public function deleteInvoice($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $invoice = Invoice::where('company_id', '=', $company_id)->where('id', '=', $id)->where('paid', '=', 'F')
                ->where('reversed_id', '=', 0)->first();

            //if invoice doesn't exist return error status
            if (!$invoice)
            {
                return ['status' => 0];
            }

            $invoice->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //copy invoice
    public function copyInvoice($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $invoice = Invoice::where('company_id', '=', $company_id)->where('id', '=', $id)->where('retail', '=', 'F')
                ->where('reversed', '=', 'F')->first();

            //if invoice doesn't exist return error status
            if (!$invoice)
            {
                return ['status' => 0];
            }

            //get invoice products
            $products = InvoiceProduct::where('invoice_id', '=', $id)->get();

            //get invoice notes
            $notes = InvoiceNote::where('invoice_id', '=', $id)->get();

            //start transaction
            DB::beginTransaction();

            //call getNextInvoiceId method to get next invoice id
            $next_id_response = $this->getNextInvoiceId($company_id, 'F', $invoice->office_id, $invoice->register_id);

            //call getReferenceNumber method to get reference number
            $reference_number_response = $this->getReferenceNumber($invoice->office_id, $invoice->register_id);

            //if response status = 0 return error message
            if ($reference_number_response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $invoice_model = new Invoice;
            $invoice_model->company_id = $company_id;
            $invoice_model->user_id = $invoice->user_id;
            $invoice_model->doc_number = $next_id_response['doc_number'];
            $invoice_model->invoice_id = $next_id_response['invoice_id'];
            $invoice_model->retail = 'F';
            $invoice_model->office_id = $invoice->office_id;
            $invoice_model->register_id = $invoice->register_id;
            $invoice_model->invoice_date = DB::raw('NOW()');
            $invoice_model->client_id = $invoice->client_id;
            $invoice_model->language_id = $invoice->language_id;
            $invoice_model->payment_type_id = $invoice->payment_type_id;
            $invoice_model->currency_id = $invoice->currency_id;
            $invoice_model->input_currency_id = $invoice->input_currency_id;
            $invoice_model->currency_ratio = $invoice->currency_ratio;
            $invoice_model->due_date = $invoice->due_date;
            $invoice_model->delivery_date = $invoice->delivery_date;
            $invoice_model->note = $invoice->note;
            $invoice_model->int_note = $invoice->int_note;
            $invoice_model->reversed_id = 0;
            $invoice_model->tax = $invoice->tax;
            $invoice_model->advance = $invoice->advance;
            $invoice_model->show_model = $invoice->show_model;
            $invoice_model->model = $invoice->model;
            $invoice_model->reference_number = $reference_number_response['data'];
            $invoice_model->save();

            foreach ($products as $product)
            {
                //insert invoice product
                $invoice_product = new InvoiceProduct;
                $invoice_product->invoice_id = $invoice_model->id;
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

            foreach ($notes as $note)
            {
                //insert invoice note
                $invoice_note = new InvoiceNote;
                $invoice_note->invoice_id = $invoice_model->id;
                $invoice_note->note = $note->note;
                $invoice_note->save();
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

    //reverse invoice
    public function reverseInvoice($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $invoice = Invoice::where('company_id', '=', $company_id)->where('id', '=', $id)->where('reversed_id', '=', 0)
                ->first();

            //if invoice doesn't exist return error status
            if (!$invoice)
            {
                return ['status' => 0];
            }

            //update reversed id of reversed invoice
            $invoice->reversed_id = $id;
            $invoice->save();

            //get invoice products
            $products = InvoiceProduct::where('invoice_id', '=', $id)->get();

            //get invoice notes
            $notes = InvoiceNote::where('invoice_id', '=', $id)->get();

            //start transaction
            DB::beginTransaction();

            //call getNextInvoiceId method to get next invoice id
            $next_id_response = $this->getNextInvoiceId($company_id, $invoice->retail, $invoice->office_id,
                $invoice->register_id);

            //call getReferenceNumber method to get reference number
            $reference_number_response = $this->getReferenceNumber($invoice->office_id, $invoice->register_id);

            //if response status = 0 return error message
            if ($reference_number_response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $invoice_model = new Invoice;
            $invoice_model->company_id = $company_id;
            $invoice_model->user_id = $invoice->user_id;
            $invoice_model->doc_number = $next_id_response['doc_number'];
            $invoice_model->invoice_id = $next_id_response['invoice_id'];
            $invoice_model->retail = $invoice->retail;
            $invoice_model->office_id = $invoice->office_id;
            $invoice_model->register_id = $invoice->register_id;
            $invoice_model->invoice_date = DB::raw('NOW()');
            $invoice_model->client_id = $invoice->client_id;
            $invoice_model->language_id = $invoice->language_id;
            $invoice_model->payment_type_id = $invoice->payment_type_id;
            $invoice_model->currency_id = $invoice->currency_id;
            $invoice_model->input_currency_id = $invoice->input_currency_id;
            $invoice_model->currency_ratio = $invoice->currency_ratio;
            $invoice_model->due_date = $invoice->due_date;
            $invoice_model->delivery_date = $invoice->delivery_date;
            $invoice_model->note = $invoice->note;
            $invoice_model->int_note = $invoice->int_note;
            $invoice_model->reversed = 'T';
            $invoice_model->reversed_id = $id;
            $invoice_model->tax = $invoice->tax;
            $invoice_model->advance = $invoice->advance;
            $invoice_model->show_model = $invoice->show_model;
            $invoice_model->model = $invoice->model;
            $invoice_model->reference_number = $reference_number_response['data'];
            $invoice_model->save();

            foreach ($products as $product)
            {
                //insert invoice product
                $invoice_product = new InvoiceProduct;
                $invoice_product->invoice_id = $invoice_model->id;
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

            foreach ($notes as $note)
            {
                //insert invoice note
                $invoice_note = new InvoiceNote;
                $invoice_note->invoice_id = $invoice_model->id;
                $invoice_note->note = $note->note;
                $invoice_note->save();
            }

            $fiscalization = false;

            //if retail = 'T' make fiscalization
            if ($invoice->retail == 'T')
            {
                //if payment type = 'Gotovina' update payment slip reversed status if invoice id exists and
                //create new payout slip
                if ($invoice->payment_type_id == 1)
                {
                    //update payment slip reversed status
                    PaymentSlip::where('company_id', '=', $company_id)->where('invoice_id', '=', $id)
                        ->update(array('reversed' => 'T'));

                    //call getInvoiceSum method to get invoice sum
                    $sum = $this->getInvoiceSum($id);

                    //call insertPayoutSlip method from RegisterReportRepository to insert payout slip
                    $repo = new RegisterReportRepository;
                    $payout_slip_response = $repo->insertPayoutSlip(0, $invoice->office_id, 'F', '', 'stornirani račun br. '.
                        $invoice->invoice_id, null, true, $sum);

                    //if response status = 0 return error message
                    if ($payout_slip_response['status'] == 0)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }
                }

                //call makeFiscalization method to make fiscalization
                $fiscalization_response = $this->makeFiscalization($company_id, $invoice_model, 'F');

                //if response status = 0 return error message
                if ($fiscalization_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }
                elseif ($fiscalization_response['status'] == 1)
                {
                    $fiscalization = true;
                }
            }

            //commit transaction
            DB::commit();

            //set reverse invoice flash
            Session::flash('success_message', trans('main.invoice_reverse'));

            if ($invoice->retail == 'T' && !$fiscalization)
            {
                //set fiscalization flash
                Session::flash('error_message', trans('errors.fiscalization_error'));
            }

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //fiscalization
    public function fiscalization($invoice_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $invoice = Invoice::where('company_id', '=', $company_id)->where('id', '=', $invoice_id)->where('retail', '=', 'T')
                ->whereNull('jir')->first();

            //if invoice doesn't exist return error message
            if (!$invoice)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //call makeFiscalization method to make fiscalization
            $fiscalization_response = $this->makeFiscalization($company_id, $invoice, 'T');

            //if response status != 1 return error message
            if ($fiscalization_response['status'] != 1)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //set invoice fiscalization flash
            Session::flash('success_message', trans('main.invoice_fiscalization'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get next invoice id
    public function getNextInvoiceId($company_id, $retail, $office_id, $register_id)
    {
        //get current year
        $year = date('Y');

        //get sljednost prostor status
        $sljednost_prostor = Company::find($company_id)->sljednost_prostor;

        //set default doc number
        $doc_number = 1;

        //get max doc number
        $max_doc_number = Invoice::where('company_id', '=', $company_id)->where('retail', '=', $retail)
            ->whereRaw('YEAR(invoice_date) = ?', [$year]);

        if ($sljednost_prostor == 'T')
        {
            $max_doc_number->where('office_id', '=', $office_id);
        }
        else
        {
            $max_doc_number->where('register_id', '=', $register_id);
        }

        $max_doc_number = $max_doc_number->max('doc_number');

        if ($max_doc_number)
        {
            //set doc_number
            $doc_number = $max_doc_number + 1;
        }

        //get office label
        $office_label = Office::find($office_id)->label;

        //get register label
        $register_label = Register::find($register_id)->label;

        //set invoice id
        $invoice_id = $doc_number.'/'.$office_label.'/'.$register_label;

        return ['invoice_id' => $invoice_id, 'doc_number' => $doc_number];
    }

    //get reference number
    public function getReferenceNumber($office_id = false, $register_id = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //get office label
            $office = Office::select('id', 'label')->where('company_id', '=', $company_id);

            if ($office_id)
            {
                $office->where('id', '=', $office_id);
            }

            $office = $office->first();

            //get register label
            $register = Register::select('id', 'label')->where('company_id', '=', $company_id);

            if ($register_id)
            {
                $register->where('id', '=', $register_id);
            }

            $register = $register->first();

            //if office or register doesn't exist set default reference number
            if (!$office || !$register)
            {
                $reference_number = '1-1-1';
            }
            else
            {
                //call getNextInvoiceId method to get next invoice id
                $next_id = $this->getNextInvoiceId($company_id, 'F', $office->id, $register->id);

                $invoice_id = $next_id['invoice_id'];

                $first_slash_position = strpos($invoice_id, '/');

                $reference_number = substr($invoice_id, 0, $first_slash_position);

                $counter = 1;

                for ($i = 0; $i < strlen($office->label); $i++)
                {
                    if (preg_match('/^\d+$/D', $office->label[$i]))
                    {
                        if ($counter == 1)
                        {
                            $reference_number .= '-'.$office->label[$i];
                        }
                        else
                        {
                            $reference_number .= $office->label[$i];
                        }

                        $counter++;
                    }
                }

                $reference_number .= '-'.$register->label;
            }

            return array('status' => 1, 'data' => $reference_number);
        }
        catch (Exception $exp)
        {
            return array('status' => 0);
        }
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

                foreach ($products as $key => $invoice_product)
                {
                    $product = Product::with('unit')
                        ->select('unit_id', 'code', 'tax_group_id', 'name', 'price', 'description')
                        ->where('company_id', '=', $company_id)->where('id', '=', $invoice_product['id'])->first();

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
                            ->where('product_id', '=', $invoice_product['id'])->first();
                    }

                    //call getProductsListPrice method to get product price
                    $price_response = $this->getProductsListPrice($product, $currency_ratio, $invoice_product['price'],
                        $invoice_product['custom_price'], $invoice_product['brutto'], $client_product_price);

                    $product_tax = $price_response['tax_percentage'];

                    //set default product rebate
                    $product_rebate = $invoice_product['rebate'];

                    //if price = '0' and client has rebate set client rebate as product rebate
                    if ($price_response['assign_client_rebate'] && $client_rebate)
                    {
                        $product_rebate = $client_rebate;
                    }

                    //get rebate amount
                    $rebate = (($price_response['price'] / 100) * $product_rebate) * $invoice_product['quantity'];

                    //get product sum
                    $sum = ($price_response['price'] * $invoice_product['quantity']) - $rebate;

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

                    if ($invoice_product['custom_price'] == 'F' && $invoice_product['price'] == 0)
                    {
                        $note = $product->description;
                    }
                    else
                    {
                        if ($invoice_product['note'])
                        {
                            $note = $invoice_product['note'];
                        }
                    }

                    //add product to products array
                    $products_array[] = array('id' => $key, 'product_id' => $invoice_product['id'], 'code' => $product->code,
                        'name' => $product->name, 'unit' => trans('main.'.$product->unit->code),
                        'quantity' => $invoice_product['quantity'],
                        'list_quantity' => number_format($invoice_product['quantity'], 2, ',' , '.'),
                        'price' => $price_response['object_price'],
                        'list_price' => number_format($price_response['price'], 2, ',' , '.'),
                        'custom_price' => $invoice_product['custom_price'], 'brutto' => $invoice_product['brutto'],
                        'note' => htmlspecialchars($note), 'tax' => number_format($product_tax, 2, ',' , '.'),
                        'rebate' => $product_rebate, 'rebate_sum' => number_format($rebate, 2, ',' , '.'),
                        'sum' => number_format($sum, 2, ',' , '.'), 'ip_id' => $invoice_product['ip_id']);
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

        //if response status = 0 return error message
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

    //get invoice sum
    public function getInvoiceSum($invoice_id)
    {
        $invoice = Invoice::select(DB::raw('DATE_FORMAT(invoice_date, "%Y-%m-%d") AS date'), 'currency_ratio', 'currency_id',
                'input_currency_id', 'tax')
            ->where('id', '=', $invoice_id)->first();

        $products = InvoiceProduct::select('quantity', 'price', 'custom_price', 'brutto', 'tax_group_id', 'rebate')
            ->where('invoice_id', '=', $invoice_id)->get();

        $grand_total = 0;

        foreach ($products as $product)
        {
            //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
            $repo = new TaxGroupRepository;
            $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $invoice->date);

            //if response status = '0' return error message
            if ($tax_percentage_response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.current_tax_percentage')];
            }

            $tax_percentage = $tax_percentage_response['data'];

            //check custom price
            if ($product->custom_price == 'T')
            {
                //compare currency and input currency
                if ($invoice->currency_id == $invoice->input_currency_id)
                {
                    if ($product->brutto == 'F')
                    {
                        $product_price = $product->price / $invoice->currency_ratio;
                    }
                    else
                    {
                        $product_price = ($product->price / $invoice->currency_ratio) / (1 + ($tax_percentage / 100));
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
            if ($invoice->tax == 'T')
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

    //get fiscal data
    private function getFiscalData($invoice_id, $delivery)
    {
        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->getCompanyId();

        //set fiscal data array
        $fiscal_data_array = [];

        //get oib, certificate, password, pdv user and sljednost prostor
        $company = Company::find($company_id);
        $oib = $company->oib;
        $certificate = $company->certificate;
        $password = $company->certificate_password;
        $pdv_user = $company->pdv_user;
        $sljednost_prostor = $company->sljednost_prostor;

        //add certificate, password, pdv user and sljednost prostor to fiscal data array
        $fiscal_data_array['oib'] = $oib;
        $fiscal_data_array['certificate'] = $certificate;
        $fiscal_data_array['password'] = $password;

        if ($pdv_user == 'T')
        {
            $fiscal_data_array['pdv_user'] = '1';
        }
        else
        {
            $fiscal_data_array['pdv_user'] = '0';
        }

        if ($sljednost_prostor == 'T')
        {
            $fiscal_data_array['sljednost_prostor'] = 'P';
        }
        else
        {
            $fiscal_data_array['sljednost_prostor'] = 'N';
        }

        //get invoice data
        $invoice = Invoice::with('office', 'register')
            ->select('doc_number', 'office_id', 'register_id', DB::raw('DATE_FORMAT(invoice_date, "%Y-%m-%d") AS date'),
                'invoice_date', 'currency_id', 'input_currency_id', 'currency_ratio', 'zki')->where('id', '=', $invoice_id)
            ->first();

        //add invoice number, office number and register number to fiscal data array
        $fiscal_data_array['invoice_number'] = $invoice->doc_number;
        $fiscal_data_array['office_number'] = $invoice->office->label;
        $fiscal_data_array['register_number'] = $invoice->register->label;

        /*
        |--------------------------------------------------------------------------
        |--------------------------------------------------------------------------
        */

        //set tax array
        $tax_array = [];

        //set tax groups array
        $tax_groups_array = [];

        $total = 0;
        $grand_total = 0;

        //get products
        $products = InvoiceProduct::where('invoice_id', '=', $invoice_id)->get();

        foreach ($products as $product)
        {
            //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
            $repo = new TaxGroupRepository;
            $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $invoice->date);

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
                if ($invoice->currency_id == $invoice->input_currency_id)
                {
                    if ($product->brutto == 'F')
                    {
                        $product_price = $product->price / $invoice->currency_ratio;
                    }
                    else
                    {
                        $product_price = ($product->price / $invoice->currency_ratio) / (1 + ($tax_percentage / 100));
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

            $total += $sum;

            $tax_groups_array[$tax_percentage][] = $sum;

            $grand_total += $sum + ($sum / 100 * $tax_percentage);
        }

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

            if ($tax > 0)
            {
                //make array with tax and sum keys
                $tax_array[] = array('tax' => $tax, 'sum' => number_format($taxes_sum, 2, '.', ''),
                    'tax_sum' => number_format($taxes_sum / 100 * $tax, 2, '.', ''));
            }
            else
            {
                //make array with tax and sum keys
                $tax_array[] = array('tax' => $tax, 'sum' => number_format($taxes_sum, 2, '.', ''),
                    'tax_sum' => number_format(0, 2, '.', ''));
            }
        }

        //add invoice date to fiscal data array
        $fiscal_data_array['invoice_date'] = $invoice->invoice_date;

        //add delivery check to fiscal data array
        $fiscal_data_array['delivery'] = $delivery;

        //add zki to fiscal data array
        $fiscal_data_array['zki'] = $invoice->zki;

        //add tax groups array to fiscal data array
        $fiscal_data_array['taxes'] = $tax_array;

        //add invoice grand total to fiscal data array
        $fiscal_data_array['invoice_grand_total'] = number_format($grand_total, 2, '.' , '');

        return $fiscal_data_array;
    }

    //make fiscalization
    public function makeFiscalization($company_id, $invoice, $delivery)
    {
        try
        {
            $company = Company::find($company_id);

            //if fiscal certificate doesn't exists return error message
            if ($company->certificate == '')
            {
                return ['status' => 0, 'error' => trans('errors.certificate_error')];
            }

            //if fiscal certificate password doesn't exists return error message
            if ($company->certificate_password == '')
            {
                return ['status' => 0, 'error' => trans('errors.certificate_password_error')];
            }

            //call getFiscalData method to get fiscal data
            $fiscal_data_response = $this->getFiscalData($invoice->id, $delivery);

            //call Fiskaliziraj method from Fiskalizacija to make fiscalization
            $repo = new Fiskalizacija;
            $fiscal = $repo->Fiskaliziraj($fiscal_data_response);

            if ($fiscal['status'] == 200)
            {
                //update invoice zki and jir
                $invoice->zki = $fiscal['zki'];
                $invoice->jir = $fiscal['jir'];
            }
            elseif ($fiscal['status'] != 400)
            {
                //update invoice zki
                $invoice->zki = $fiscal['zki'];
            }
            else
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $invoice->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //pdf data
    public function pdfData($type, $id, $cron_company_id = false)
    {
        try
        {
            if ($cron_company_id)
            {
                $company_id = $cron_company_id;
            }
            else
            {
                //call getCompanyId method from UserRepository to get company id
                $company_id = $this->getCompanyId();
            }

            $invoice = Invoice::with('client', 'office', 'paymentType', 'currency', 'user')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)
                ->first();

            //if invoice doesn't exist return error status
            if (!$invoice)
            {
                return ['status' => 0];
            }

            //get products
            $products = InvoiceProduct::with('product')
                ->where('invoice_id', '=', $id)->orderBy('id', 'asc')->get();

            //get invoice notes
            $invoice->notes = InvoiceNote::select('note')->where('invoice_id', '=', $id)->get();

            $invoice_date = date('Y-m-d', strtotime($invoice->invoice_date));

            //if contract id exists set number of invoices and create after end
            if ($invoice->contract_id)
            {
                $contract = Contract::find($invoice->contract_id);

                $invoice->number_of_invoices = $contract->number_of_invoices;
                $invoice->create_after_end = $contract->create_after_end;
            }

            $company = Company::find($company_id);

            $admin = $this->getCompanyAdmin($company_id);

            //set language
            if ($type == 1 && $invoice->language_id != 1)
            {
                //set app language
                App::setLocale('en');
            }

            $invoice_no_text = trans('main.invoice_no');

            if ($invoice->advance == 'T')
            {
                $invoice_no_text = trans('main.advance_invoice_no');
            }

            if ($invoice->reversed == 'T')
            {
                $invoice->reversed_id = Invoice::find($invoice->reversed_id)->invoice_id;
            }

            $invoice->logo = public_path().'/logo/'.$company->logo;
            $invoice->logo2_url = public_path().'/logo2/'.$company->logo2;
            $invoice->invoice_no_text = $invoice_no_text;
            $invoice->date = date('d.m.Y. H:i', strtotime($invoice->invoice_date));
            $invoice->due_date = date('d.m.Y.', strtotime($invoice->due_date));

            if ($invoice->delivery_date)
            {
                $invoice->delivery_date = date('d.m.Y.', strtotime($invoice->delivery_date));
            }

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
                $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $invoice_date);

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
                    if ($invoice->currency_id == $invoice->input_currency_id)
                    {
                        if ($product->brutto == 'F')
                        {
                            $product_price = $product->price;
                            $dom_product_price = $product->price / $invoice->currency_ratio;
                        }
                        else
                        {
                            $product_price = $product->price / (1 + ($tax_percentage / 100));
                            $dom_product_price = ($product->price / $invoice->currency_ratio) / (1 + ($tax_percentage / 100));
                        }
                    }
                    else
                    {
                        if ($product->brutto == 'F')
                        {
                            $product_price = $product->price * $invoice->currency_ratio;
                            $dom_product_price = $product->price;
                        }
                        else
                        {
                            $product_price = ($product->price * $invoice->currency_ratio) / (1 + ($tax_percentage / 100));
                            $dom_product_price = $product->price / (1 + ($tax_percentage / 100));
                        }
                    }
                }
                else
                {
                    if ($product->brutto == 'F')
                    {
                        $product_price = $product->price * $invoice->currency_ratio;
                        $dom_product_price = $product->price;
                    }
                    else
                    {
                        $product_price = ($product->price / (1 + ($tax_percentage / 100))) * $invoice->currency_ratio;
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
                if ($invoice->tax == 'T')
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

                if ($invoice->reversed == 'T')
                {
                    $quantity = '-'.$product->quantity;

                    if ($type == 2)
                    {
                        if ($dom_rebate != 0)
                        {
                            $dom_rebate = '-'.number_format($dom_rebate, 2, ',', '.');
                        }
                        else
                        {
                            $dom_rebate = number_format($dom_rebate, 2, ',', '.');
                        }

                        $dom_sum = '-'.$dom_sum;
                    }
                    else
                    {
                        if ($rebate != 0)
                        {
                            $rebate = '-'.number_format($rebate, 2, ',', '.');
                        }
                        else
                        {
                            $rebate = number_format($rebate, 2, ',', '.');
                        }

                        $sum = '-'.$sum;
                    }
                }
                else
                {
                    $quantity = $product->quantity;

                    if ($type == 2)
                    {
                        $dom_rebate = number_format($dom_rebate, 2, ',', '.');
                    }
                    else
                    {
                        $rebate = number_format($rebate, 2, ',', '.');
                    }
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
            if ($invoice->tax == 'T')
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

            if ($invoice->reversed == 'T')
            {
                if ($type == 2)
                {
                    $sum_array['total'] = '-'.number_format($dom_total, 2, ',', '.');
                    $sum_array['rebate_sum'] = number_format($dom_rebate_sum, 2, ',', '.');

                    if ($dom_rebate_sum)
                    {
                        $sum_array['rebate_sum'] = '-'.number_format($dom_rebate_sum, 2, ',', '.');
                    }
                }
                else
                {
                    $sum_array['total'] = '-'.number_format($total, 2, ',', '.');
                    $sum_array['rebate_sum'] = number_format($rebate_sum, 2, ',', '.');

                    if ($rebate_sum)
                    {
                        $sum_array['rebate_sum'] = '-'.number_format($rebate_sum, 2, ',', '.');
                    }
                }

                $sum_array['grand_total'] = '-'.number_format($grand_total, 2, ',', '.');
                $sum_array['dom_grand_total'] = '-'.number_format($dom_grand_total, 2, ',', '.');
            }
            else
            {
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
            }

            //remove duplicate tax groups
            $tax_groups_notes_array = array_unique($tax_groups_notes_array);

            foreach ($tax_groups_notes_array as $group_id)
            {
                //add tax group note to tax notes array
                $tax_notes_array[] = TaxGroup::find($group_id)->note;
            }

            return ['status' => 1, 'type' => $type, 'invoice' => $invoice, 'company' => $company, 'products' => $products_array,
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

            $invoice = Invoice::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if invoice doesn't exist return error message
            if (!$invoice)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //if invoice client doesn't exist return warning message
            if (!$invoice->client)
            {
                return ['status' => 2, 'warning' => trans('errors.send_email_no_client')];
            }

            //if client email doesn't exist return warning message
            if (!$invoice->client->email)
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
                'name' => $invoice->client->name,
                'email' => $invoice->client->email
            ])->notify(new documentEmail(Company::find($company_id)->name, $invoice, $data, 'F'));

            return ['status' => 1, 'success' => trans('main.email_sent')];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get client invoices
    public function getClientInvoices($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $client = Client::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if client doesn't exist return error status
            if (!$client)
            {
                return ['status' => 0];
            }

            $invoices = Invoice::with('client', 'paymentType')
                ->select('id', 'invoice_id', DB::raw('DATE_FORMAT(invoice_date, "%d.%m.%Y.") AS date'),
                    DB::raw('DATE_FORMAT(due_date, "%d.%m.%Y.") AS due_date'), 'paid', 'reversed', 'reversed_id',
                    'partial_paid_sum')
                ->where('company_id', '=', $company_id)->where('client_id', '=', $id)->orderBy('invoice_date', 'asc')
                ->orderBy('id', 'asc')->get();

            $invoices_sum = 0;
            $paid_invoices_sum = 0;

            foreach ($invoices as $invoice)
            {
                //call getInvoiceSum method to get invoice sum
                $invoice_sum = $this->getInvoiceSum($invoice->id);

                //set default invoice paid sum
                $invoice->paid_sum = '0,00';

                if ($invoice->paid == 'T')
                {
                    if ($invoice->partial_paid_sum)
                    {
                        $invoice->paid_sum = number_format($invoice->partial_paid_sum, 2, ',', '.');
                        $paid_invoices_sum += $invoice->partial_paid_sum;
                    }
                    else
                    {
                        $invoice->paid_sum = number_format($invoice_sum, 2, ',', '.');
                        $paid_invoices_sum += $invoice_sum;
                    }
                }
                else
                {
                    if ($invoice->reversed_id)
                    {
                        if ($invoice->reversed_id != $invoice->id)
                        {
                            $invoice_sum = -$invoice_sum;

                            $invoice->sum = number_format($invoice_sum, 2, ',', '.');

                            $reversed_paid = Invoice::find($invoice->reversed_id)->paid;

                            if ($reversed_paid == 'T')
                            {
                                $invoice->paid_sum = number_format($invoice_sum, 2, ',', '.');
                                $paid_invoices_sum += $invoice_sum;
                            }
                        }
                    }
                }

                $invoice->sum = number_format($invoice_sum, 2, ',', '.');
                $invoices_sum += $invoice_sum;
            }

            $debit = number_format(abs($paid_invoices_sum - $invoices_sum), 2, ',', '.');
            $invoices_sum = number_format($invoices_sum, 2, ',', '.');
            $paid_invoices_sum = number_format($paid_invoices_sum, 2, ',', '.');

            return ['status' => 1, 'client' => $client->name, 'invoices' => $invoices, 'sum' => $invoices_sum,
                'paid_sum' => $paid_invoices_sum, 'debit' => $debit];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
