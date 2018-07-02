<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Contract;
use App\ContractProduct;
use App\Product;
use App\Unit;
use App\Client;
use App\ClientPrice;

class ContractRepository extends UserRepository
{
    //get contracts
    public function getContracts($search_string)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $contracts = Contract::with('client')
                ->select('id', DB::raw('DATE_FORMAT(contract_date, "%d.%m.%Y.") AS date'), 'contract_number', 'client_id',
                    'active')
                ->where('company_id', '=', $company_id);

            if ($search_string)
            {
                $contracts->whereHas('client', function($query) use ($search_string) {
                    $query->whereRaw('name LIKE ?', ['%'.$search_string.'%']);
                });
            }

            $contracts = $contracts->paginate(30);

            foreach ($contracts as $contract)
            {
                //set default contract status
                $contract->status = trans('main.active');

                //set contract status
                if ($contract->active == 'F')
                {
                    $contract->status = trans('main.inactive');
                }
            }

            return ['status' => 1, 'data' => $contracts];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert contract
    public function insertContract($office, $register, $contract_number, $client, $language, $payment_type, $currency,
        $input_currency, $due_days, $note, $int_note, $tax, $number_of_invoices, $create_day, $previous_month_create,
        $create_after_end, $email_sending, $active, $products)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getUserId method from UserRepository to get user id
            $user_id = $this->getUserId();

            //start transaction
            DB::beginTransaction();

            $contract = new Contract;
            $contract->company_id = $company_id;
            $contract->user_id = $user_id;
            $contract->office_id = $office;
            $contract->register_id = $register;
            $contract->contract_date = DB::raw('NOW()');
            $contract->contract_number = $contract_number;
            $contract->client_id = $client;
            $contract->language_id = $language;
            $contract->payment_type_id = $payment_type;
            $contract->currency_id = $currency;
            $contract->input_currency_id = $input_currency;
            $contract->due_days = $due_days;
            $contract->note = $note;
            $contract->int_note = $int_note;
            $contract->tax = $tax;
            $contract->number_of_invoices = $number_of_invoices;
            $contract->create_day = $create_day;
            $contract->previous_month_create = $previous_month_create;
            $contract->create_after_end = $create_after_end;
            $contract->email_sending = $email_sending;
            $contract->active = $active;
            $contract->save();

            foreach ($products as $product)
            {
                //get product tax group
                $tax_group = Product::find($product['id'])->tax_group_id;

                //insert contract product
                $contract_product = new ContractProduct;
                $contract_product->contract_id = $contract->id;
                $contract_product->product_id = $product['id'];
                $contract_product->quantity = $product['quantity'];
                $contract_product->price = $product['price'];
                $contract_product->custom_price = $product['custom_price'];
                $contract_product->brutto = $product['brutto'];
                $contract_product->tax_group_id = $tax_group;
                $contract_product->rebate = $product['rebate'];
                $contract_product->note = $product['note'];
                $contract_product->save();
            }

            //commit transaction
            DB::commit();

            //set insert contract flash
            Session::flash('success_message', trans('main.contract_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get contract details
    public function getContractDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $contract = Contract::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if contract doesn't exist return error status
            if (!$contract)
            {
                return ['status' => 0];
            }

            $client_address = '';
            $client_oib = '';

            if ($contract->client->address)
            {
                $client_address .= $contract->client->address;

                if ($contract->client->city)
                {
                    $client_address .= ', '.$contract->client->city;
                }

                if ($contract->client->oib && $contract->client->oib != '')
                {
                    $client_oib .= $contract->client->oib;
                }
            }

            //add client address and oib to contract object
            $contract->client_address = $client_address;
            $contract->client_oib = $client_oib;

            /*
            |--------------------------------------------------------------------------
            | Contract products
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
            if ($contract->currency_id != 1)
            {
                //call calculateCurrencyRatio method from CompanyRepository to calculate currency ratio
                $repo = new CompanyRepository;
                $currency_response = $repo->calculateCurrencyRatio($contract->currency_id);

                //if response status = 0 return error message
                if ($currency_response['status'] == 0)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }

                $currency_ratio = $currency_response['data'];
            }

            //get products
            $products = ContractProduct::with('product')
                ->where('contract_id', '=', $id)->orderBy('id', 'asc')->get();

            foreach ($products as $product)
            {
                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //call getProductsListPrice method to get product price
                $price_response = $this->getProductsListPrice($product, $currency_ratio,  $product->price, $product->custom_price,
                    $product->brutto, null);

                $product_tax = $price_response['tax_percentage'];

                //get rebate amount
                $rebate = (($price_response['price'] / 100) * $product->rebate) * $product->quantity;

                //get product sum
                $sum = ($price_response['price'] * $product->quantity) - $rebate;

                $total += $sum;

                $rebate_sum += $rebate;

                //if tax = 'T' format tax groups array and add tax sum to grand total
                if ($contract->tax == 'T')
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

            //add products to contract object
            $contract->products = $products;

            if ($contract->tax == 'T')
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

            //add tax array, total, rebate sum and grand total to contract object
            $contract->tax_array = $tax_array;
            $contract->total = number_format($total, 2, ',' , '.');
            $contract->rebate_sum = number_format($rebate_sum, 2, ',' , '.');
            $contract->grand_total = number_format($grand_total, 2, ',' , '.');

            return ['status' => 1, 'data' => $contract];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //update contract
    public function updateContract($id, $office, $register, $contract_number, $client, $language, $payment_type, $currency,
        $input_currency, $due_days, $note, $int_note, $tax, $number_of_invoices, $create_day, $previous_month_create,
        $create_after_end, $email_sending, $active, $products)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            $contract = Contract::find($id);
            $contract->office_id = $office;
            $contract->register_id = $register;
            $contract->contract_number = $contract_number;
            $contract->client_id = $client;
            $contract->language_id = $language;
            $contract->payment_type_id = $payment_type;
            $contract->currency_id = $currency;
            $contract->input_currency_id = $input_currency;
            $contract->due_days = $due_days;
            $contract->note = $note;
            $contract->int_note = $int_note;
            $contract->tax = $tax;
            $contract->number_of_invoices = $number_of_invoices;
            $contract->create_day = $create_day;
            $contract->previous_month_create = $previous_month_create;
            $contract->create_after_end = $create_after_end;
            $contract->email_sending = $email_sending;
            $contract->active = $active;
            $contract->save();

            /*
            |--------------------------------------------------------------------------
            | Contract products
            |--------------------------------------------------------------------------
            */

            //set exclude cp ids array
            $exclude_cp_ids_array = [];

            foreach ($products as $product)
            {
                //if cp id exists update product, else insert new product
                if ($product['cp_id'])
                {
                    //update contract product
                    $contract_product = ContractProduct::where('contract_id', '=', $id)->where('id', '=', $product['cp_id'])
                        ->first();
                    $contract_product->quantity = $product['quantity'];
                    $contract_product->price = $product['price'];
                    $contract_product->custom_price = $product['custom_price'];
                    $contract_product->brutto = $product['brutto'];
                    $contract_product->rebate = $product['rebate'];
                    $contract_product->note = $product['note'];
                    $contract_product->save();
                }
                else
                {
                    //get product tax group
                    $tax_group = Product::find($product['id'])->tax_group_id;

                    //insert contract product
                    $contract_product = new ContractProduct;
                    $contract_product->contract_id = $id;
                    $contract_product->product_id = $product['id'];
                    $contract_product->quantity = $product['quantity'];
                    $contract_product->price = $product['price'];
                    $contract_product->custom_price = $product['custom_price'];
                    $contract_product->brutto = $product['brutto'];
                    $contract_product->tax_group_id = $tax_group;
                    $contract_product->rebate = $product['rebate'];
                    $contract_product->note = $product['note'];
                    $contract_product->save();
                }

                //add cp id to exclude array
                $exclude_cp_ids_array[] = $contract_product->id;
            }

            //delete all contract products which are not in exclude cp id array
            ContractProduct::where('contract_id', '=', $id)->whereNotIn('id', $exclude_cp_ids_array)->delete();

            //commit transaction
            DB::commit();

            //set update contract flash
            Session::flash('success_message', trans('main.contract_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete contract
    public function deleteContract($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $contract = Contract::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if contract doesn't exist return error status
            if (!$contract)
            {
                return ['status' => 0];
            }

            $contract->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //copy contract
    public function copyContract($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $contract = Contract::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if contract doesn't exist return error status
            if (!$contract)
            {
                return ['status' => 0];
            }

            //get contract products
            $products = ContractProduct::where('contract_id', '=', $id)->get();

            //start transaction
            DB::beginTransaction();

            $contract_model = new Contract;
            $contract_model->company_id = $company_id;
            $contract_model->user_id = $contract->user_id;
            $contract_model->office_id = $contract->office_id;
            $contract_model->register_id = $contract->register_id;
            $contract_model->contract_date = DB::raw('NOW()');
            $contract_model->contract_number = $contract->contract_number;
            $contract_model->client_id = $contract->client_id;
            $contract_model->language_id = $contract->language_id;
            $contract_model->payment_type_id = $contract->payment_type_id;
            $contract_model->currency_id = $contract->currency_id;
            $contract_model->input_currency_id = $contract->input_currency_id;
            $contract_model->due_days = $contract->due_days;
            $contract_model->note = $contract->note;
            $contract_model->int_note = $contract->int_note;
            $contract_model->tax = $contract->tax;
            $contract_model->number_of_invoices = $contract->number_of_invoices;
            $contract_model->create_day = $contract->create_day;
            $contract_model->previous_month_create = $contract->previous_month_create;
            $contract_model->create_after_end = $contract->create_after_end;
            $contract_model->email_sending = $contract->email_sending;
            $contract_model->active = $contract->active;
            $contract_model->save();

            foreach ($products as $product)
            {
                //insert contract product
                $contract_product = new ContractProduct;
                $contract_product->contract_id = $contract_model->id;
                $contract_product->product_id = $product->product_id;
                $contract_product->quantity = $product->quantity;
                $contract_product->price = $product->price;
                $contract_product->custom_price = $product->custom_price;
                $contract_product->brutto = $product->brutto;
                $contract_product->tax_group_id = $product->tax_group_id;
                $contract_product->rebate = $product->rebate;
                $contract_product->note = $product->note;
                $contract_product->save();
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

                    //if response status = 0 return error message
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
                    //if product doesn't exist return error status
                    $product = Product::with('unit')
                        ->select('unit_id', 'code', 'tax_group_id', 'name', 'price', 'description')
                        ->where('company_id', '=', $company_id)->where('id', '=', $invoice_product['id'])->first();

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

                    //if price == '0' and client has rebate set client rebate as product rebate
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
                        'sum' => number_format($sum, 2, ',' , '.'), 'cp_id' => $invoice_product['cp_id']);
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
}
