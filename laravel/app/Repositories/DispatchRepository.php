<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Dispatch;
use App\DispatchProduct;
use App\Product;
use App\Unit;
use App\Company;
use App\ClientPrice;

class DispatchRepository extends UserRepository
{
    //get dispatches
    public function getDispatches($search_string)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $dispatches = Dispatch::with('client')
                ->select('id', 'dispatch_id', DB::raw('DATE_FORMAT(dispatch_date, "%d.%m.%Y.") AS date'), 'client_id')
                ->where('company_id', '=', $company_id);

            if ($search_string)
            {
                $dispatches->whereHas('client', function($query) use ($search_string) {
                    $query->whereRaw('name LIKE ?', ['%'.$search_string.'%']);
                });
            }

            $dispatches = $dispatches->orderBy('id', 'desc')->orderBy('dispatch_date', 'desc')->paginate(30);

            foreach ($dispatches as $dispatch)
            {
                //call getDispatchSum method to get dispatch sum
                $dispatch_sum = $this->getDispatchSum($dispatch->id);

                //set dispatch sum
                $dispatch->sum = number_format($dispatch_sum, 2, ',', '.');
            }

            return ['status' => 1, 'data' => $dispatches];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert dispatch
    public function insertDispatch($client, $note, $show_prices, $products)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getUserId method from UserRepository to get user id
            $user_id = $this->getUserId();

            //start transaction
            DB::beginTransaction();

            //call getNextDispatchId method to get next dispatch id
            $response = $this->getNextDispatchId($company_id);

            $dispatch = new Dispatch;
            $dispatch->company_id = $company_id;
            $dispatch->user_id = $user_id;
            $dispatch->doc_number = $response['doc_number'];
            $dispatch->dispatch_id = $response['dispatch_id'];
            $dispatch->dispatch_date = DB::raw('NOW()');
            $dispatch->client_id = $client;
            $dispatch->note = $note;
            $dispatch->show_prices = $show_prices;
            $dispatch->save();

            foreach ($products as $key => $product)
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

            //commit transaction
            DB::commit();

            //set insert dispatch flash
            Session::flash('success_message', trans('main.dispatch_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get dispatch details
    public function getDispatchDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $dispatch = Dispatch::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if dispatch doesn't exist return error status
            if (!$dispatch)
            {
                return ['status' => 0];
            }

            //format dispatch date
            $dispatch->dispatch_date = date('d.m.Y. H:i:s', strtotime($dispatch->dispatch_date));

            $client_address = '';
            $client_oib = '';

            if ($dispatch->client->address)
            {
                $client_address .= $dispatch->client->address;

                if ($dispatch->client->city)
                {
                    $client_address .= ', '.$dispatch->client->city;
                }

                if ($dispatch->client->oib && $dispatch->client->oib != '')
                {
                    $client_oib .= $dispatch->client->oib;
                }
            }

            //add client address and oib to dispatch object
            $dispatch->client_address = $client_address;
            $dispatch->client_oib = $client_oib;

            /*
            |--------------------------------------------------------------------------
            | Dispatch products
            |--------------------------------------------------------------------------
            */

            //set tax array
            $tax_array = [];

            //set tax groups array
            $tax_groups_array = [];

            $counter = 1;
            $total = 0;
            $grand_total = 0;

            //get products
            $products = DispatchProduct::with('product')
                ->where('dispatch_id', '=', $id)->orderBy('id', 'asc')->get();

            foreach ($products as $product)
            {
                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //call getProductsListPrice method to get product price
                $price_response = $this->getProductsListPrice($product, $product->price, null);

                //get product sum
                $sum = $price_response['price'] * $product->quantity;

                $total += $sum;

                //format tax groups array and add tax sum to grand total
                $tax_groups_array[$price_response['tax_percentage']][] = $sum / 100 * $price_response['tax_percentage'];

                $grand_total += $sum + ($sum / 100 * $price_response['tax_percentage']);

                //add product unit, list quantity, list price, tax, rebate sum, sum and counter to product object
                $product->unit = trans('main.'.$unit);
                $product->list_quantity = number_format($product->quantity, 2, ',' , '.');
                $product->list_price = number_format($price_response['price'], 2, ',' , '.');
                $product->tax = number_format($price_response['tax_percentage'], 2, ',' , '.');
                $product->sum = number_format($sum, 2, ',' , '.');
                $product->counter = $counter;

                $counter++;
            }

            //add products to dispatch object
            $dispatch->products = $products;

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

            //add tax array, total and grand total to dispatch object
            $dispatch->tax_array = $tax_array;
            $dispatch->total = number_format($total, 2, ',' , '.');
            $dispatch->grand_total = number_format($grand_total, 2, ',' , '.');

            return ['status' => 1, 'data' => $dispatch];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //update dispatch
    public function updateDispatch($id, $date, $client, $note, $show_prices, $products)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //format dispatch date
            $date = date('Y-m-d H:i:s', strtotime($date));

            $dispatch = Dispatch::find($id);
            $dispatch->dispatch_date = $date;
            $dispatch->client_id = $client;
            $dispatch->note = $note;
            $dispatch->show_prices = $show_prices;
            $dispatch->save();

            //set exclude dp ids array
            $exclude_dp_ids_array = [];

            foreach ($products as $key => $product)
            {
                //if dp id exists update product, else insert new product
                if ($product['dp_id'])
                {
                    //update dispatch product
                    $dispatch_product = DispatchProduct::where('dispatch_id', '=', $id)
                        ->where('id', '=', $product['dp_id'])->first();
                    $dispatch_product->quantity = $product['quantity'];
                    $dispatch_product->price = $product['price'];
                    $dispatch_product->note = $product['note'];
                    $dispatch_product->save();
                }
                else
                {
                    //get product tax group
                    $tax_group = Product::find($product['id'])->tax_group_id;

                    //insert dispatch product
                    $dispatch_product = new DispatchProduct;
                    $dispatch_product->dispatch_id = $id;
                    $dispatch_product->product_id = $product['id'];
                    $dispatch_product->quantity = $product['quantity'];
                    $dispatch_product->price = $product['price'];
                    $dispatch_product->tax_group_id = $tax_group;
                    $dispatch_product->note = $product['note'];
                    $dispatch_product->save();
                }

                //add dp id to exclude array
                $exclude_dp_ids_array[] = $dispatch_product->id;
            }

            //delete all dispatch products which are not in exclude dp id array
            DispatchProduct::where('dispatch_id', '=', $id)->whereNotIn('id', $exclude_dp_ids_array)->delete();

            //commit transaction
            DB::commit();

            //set update dispatch flash
            Session::flash('success_message', trans('main.dispatch_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete dispatch
    public function deleteDispatch($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $dispatch = Dispatch::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if dispatch doesn't exist return error status
            if (!$dispatch)
            {
                return ['status' => 0];
            }

            $dispatch->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get next dispatch id
    public function getNextDispatchId($company_id)
    {
        //get current year
        $year = date('Y');

        //set dispatch year
        $dispatch_year = $year[2].$year[3];

        //set default doc number
        $doc_number = 1;

        //get max doc number
        $max_doc_number = Dispatch::where('company_id', '=', $company_id)->whereRaw('YEAR(dispatch_date) = ?', [$year])
            ->max('doc_number');

        if ($max_doc_number)
        {
            //set doc_number
            $doc_number = $max_doc_number + 1;
        }

        //set dispatch id
        $dispatch_id = $doc_number.'/'.$dispatch_year;

        return ['dispatch_id' => $dispatch_id, 'doc_number' => $doc_number];
    }

    //get products
    public function getProducts($client, $products)
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
                $total = 0;
                $grand_total = 0;

                foreach ($products as $key => $dispatch_product)
                {
                    //if product doesn't exist return error status
                    $product = Product::with('unit')
                        ->select('unit_id', 'code', 'tax_group_id', 'name', 'price', 'description')
                        ->where('company_id', '=', $company_id)->where('id', '=', $dispatch_product['id'])->first();

                    if (!$product)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }

                    $note = '';

                    //get client product price
                    $client_product_price = ClientPrice::select('price')->where('client_id', '=', $client)
                        ->where('product_id', '=', $dispatch_product['id'])->first();


                    //call getProductsListPrice method to get product price
                    $price_response = $this->getProductsListPrice($product, $dispatch_product['price'], $client_product_price);

                    //get product sum
                    $sum = $price_response['price'] * $dispatch_product['quantity'];

                    $total += $sum;

                    //format tax groups array and add tax sum to grand total
                    $tax_groups_array[$price_response['tax_percentage']][] = $sum / 100 * $price_response['tax_percentage'];

                    $grand_total += $sum + ($sum / 100 * $price_response['tax_percentage']);

                    if ($dispatch_product['price'] == 0)
                    {
                        $note = $product->description;
                    }
                    else
                    {
                        if ($dispatch_product['note'])
                        {
                            $note = $dispatch_product['note'];
                        }
                    }

                    //add product to products array
                    $products_array[] = array('id' => $key, 'product_id' => $dispatch_product['id'], 'code' => $product->code,
                        'name' => $product->name, 'unit' => trans('main.'.$product->unit->code),
                        'quantity' => $dispatch_product['quantity'],
                        'list_quantity' => number_format($dispatch_product['quantity'], 2, ',' , '.'),
                        'price' => $price_response['object_price'],
                        'list_price' => number_format($price_response['price'], 2, ',' , '.'), 'note' => htmlspecialchars($note),
                        'tax' => number_format($price_response['tax_percentage'], 2, ',' , '.'),
                        'sum' => number_format($sum, 2, ',' , '.'), 'dp_id' => $dispatch_product['dp_id']);
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

                    //make array with tax and sum keys
                    $tax_array[] = array('tax' => $tax, 'sum' => number_format($taxes_sum, 2, ',' , '.'));
                }

                return ['status' => 1, 'quantity' => 1, 'products' => $products_array, 'tax_array' => $tax_array,
                    'total' => number_format($total, 2, ',' , '.'), 'grand_total' => number_format($grand_total, 2, ',' , '.')];
            }

            return ['status' => 1, 'products' => $products_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get products list price
    private function getProductsListPrice($product_object, $price, $client_product_price)
    {
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

        if ($price == 0)
        {
            if ($client_product_price)
            {
                $product_price = $client_product_price->price;

                $object_price = $client_product_price->price;
            }
            else
            {
                $product_price = $product_object->price;

                $object_price = $product_object->price;
            }
        }
        else
        {
            $product_price = $price;

            $object_price = $price;
        }

        return ['price' => $product_price, 'object_price' => $object_price, 'tax_percentage' => $tax_percentage];
    }

    //get dispatch sum
    public function getDispatchSum($dispatch_id)
    {
        $dispatch = Dispatch::select('client_id', DB::raw('DATE_FORMAT(dispatch_date, "%Y-%m-%d") AS date'))
            ->where('id', '=', $dispatch_id)->first();

        $products = DispatchProduct::select('quantity', 'price', 'tax_group_id')->where('dispatch_id', '=', $dispatch_id)->get();

        $grand_total = 0;

        foreach ($products as $product)
        {
            //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
            $repo = new TaxGroupRepository;
            $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $dispatch->date);

            //if response status = 0 return error message
            if ($tax_percentage_response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.current_tax_percentage')];
            }

            $tax_percentage = $tax_percentage_response['data'];

            $sum = $product->price * $product->quantity;

            $grand_total += $sum + ($sum / 100 * $tax_percentage);
        }

        return $grand_total;
    }

    //pdf data
    public function pdfData($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $dispatch = Dispatch::with('client', 'user')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)
                ->first();

            //if dispatch doesn't exist return error status
            if (!$dispatch)
            {
                return ['status' => 0];
            }

            //get products
            $products = DispatchProduct::with('product')
                ->where('dispatch_id', '=', $id)->orderBy('id', 'asc')->get();

            $dispatch_date = date('Y-m-d', strtotime($dispatch->dispatch_date));

            $company = Company::find($company_id);

            $admin = $this->getCompanyAdmin($company_id);

            $dispatch_no_text = trans('main.dispatch_no');

            $dispatch->logo = public_path().'/logo/'.$company->logo;
            $dispatch->dispatch_no_text = $dispatch_no_text;
            $dispatch->date = date('d.m.Y. H:i', strtotime($dispatch->dispatch_date));

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

            //set sum array
            $sum_array = [];

            $total = 0;
            $tax_sum = 0;
            $grand_total = 0;
            $i = 1;

            foreach ($products as $product)
            {
                //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
                $repo = new TaxGroupRepository;
                $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $dispatch_date);

                //if response status = 0 return error message
                if ($tax_percentage_response['status'] == 0)
                {
                    return ['status' => 0];
                }

                $tax_percentage = $tax_percentage_response['data'];

                //get product sum
                $sum = $product->price * $product->quantity;

                $total += $sum;

                $tax_sum += $sum / 100 * $tax_percentage;

                $tax_groups_array[$tax_percentage][] = $sum / 100 * $tax_percentage;

                $grand_total += $sum + ($sum / 100 * $tax_percentage);

                $quantity = $product->quantity;

                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //add product to products array
                $products_array[] = ['rb' => $i, 'code' => $product->product->code, 'name' => $product->product->name,
                    'unit' => trans('main.'.$unit), 'quantity' => $quantity, 'price' => number_format($product->price, 2, ',', '.'),
                    'note' => $product->note, 'tax' => number_format($tax_percentage, 2, ',', '.'),
                    'sum' => number_format($sum, 2, ',', '.')];

                $i++;
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

                //add tax and tax sum to tax array
                $tax_array[] = ['tax' => $tax, 'sum' => number_format($taxes_sum, 2, ',', '.')];
            }

            $sum_array['total'] = number_format($total, 2, ',', '.');
            $sum_array['grand_total'] = number_format($grand_total, 2, ',', '.');

            return ['status' => 1, 'dispatch' => $dispatch, 'company' => $company, 'products' => $products_array,
                'tax_array' => $tax_array, 'sum_array' => $sum_array, 'admin' => $admin];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
