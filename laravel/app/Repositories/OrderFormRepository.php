<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\OrderForm;
use App\OrderFormProduct;
use App\Product;
use App\Unit;
use App\Company;

class OrderFormRepository extends UserRepository
{
    //get order forms
    public function getOrderForms($search_string)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $order_forms = OrderForm::with('client')
                ->select('id', 'order_form_id', DB::raw('DATE_FORMAT(order_form_date, "%d.%m.%Y.") AS date'), 'client_id')
                ->where('company_id', '=', $company_id);

            if ($search_string)
            {
                $order_forms->whereHas('client', function($query) use ($search_string) {
                    $query->whereRaw('name LIKE ?', ['%'.$search_string.'%']);
                });
            }

            $order_forms = $order_forms->orderBy('id', 'desc')->orderBy('order_form_date', 'desc')->paginate(30);

            foreach ($order_forms as $order_form)
            {
                //call getOrderFormSum method to get order form sum
                $order_form_sum = $this->getOrderFormSum($order_form->id);

                //set order form sum
                $order_form->sum = number_format($order_form_sum, 2, ',', '.');
            }

            return ['status' => 1, 'data' => $order_forms];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert order form
    public function insertOrderForm($client, $delivery_date, $location, $note, $products)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getUserId method from UserRepository to get user id
            $user_id = $this->getUserId();

            //start transaction
            DB::beginTransaction();

            //call getNextOrderFormId method to get next order form id
            $response = $this->getNextOrderFormId($company_id);

            //format delivery date
            $delivery_date = date('Y-m-d', strtotime($delivery_date));

            $order_form = new OrderForm;
            $order_form->company_id = $company_id;
            $order_form->user_id = $user_id;
            $order_form->doc_number = $response['doc_number'];
            $order_form->order_form_id = $response['order_form_id'];
            $order_form->order_form_date = DB::raw('NOW()');
            $order_form->client_id = $client;
            $order_form->delivery_date = $delivery_date;
            $order_form->location = $location;
            $order_form->note = $note;
            $order_form->save();

            foreach ($products as $key => $product)
            {
                //get product tax group
                $tax_group = Product::find($product['id'])->tax_group_id;

                //insert order form product
                $order_form_product = new OrderFormProduct;
                $order_form_product->order_form_id = $order_form->id;
                $order_form_product->product_id = $product['id'];
                $order_form_product->quantity = $product['quantity'];
                $order_form_product->price = $product['price'];
                $order_form_product->tax_group_id = $tax_group;
                $order_form_product->note = $product['note'];
                $order_form_product->save();
            }

            //commit transaction
            DB::commit();

            //set insert order form flash
            Session::flash('success_message', trans('main.order_form_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get order form details
    public function getOrderFormDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $order_form = OrderForm::with('client')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if order form doesn't exist return error status
            if (!$order_form)
            {
                return ['status' => 0];
            }

            //format order form date and delivery date
            $order_form->order_form_date = date('d.m.Y. H:i:s', strtotime($order_form->order_form_date));
            $order_form->delivery_date = date('d.m.Y.', strtotime($order_form->delivery_date));

            $client_address = '';
            $client_oib = '';

            if ($order_form->client->address)
            {
                $client_address .= $order_form->client->address;

                if ($order_form->client->city)
                {
                    $client_address .= ', '.$order_form->client->city;
                }

                if ($order_form->client->oib && $order_form->client->oib != '')
                {
                    $client_oib .= $order_form->client->oib;
                }
            }

            //add client address and oib to order form object
            $order_form->client_address = $client_address;
            $order_form->client_oib = $client_oib;

            /*
            |--------------------------------------------------------------------------
            | Order form products
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
            $products = OrderFormProduct::with('product')
                ->where('order_form_id', '=', $id)->orderBy('id', 'asc')->get();

            foreach ($products as $product)
            {
                //get product unit
                $unit = Unit::find($product->product->unit_id)->code;

                //call getProductsListPrice method to get product price
                $price_response = $this->getProductsListPrice($product, $product->price);

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

            //add products to order form object
            $order_form->products = $products;

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

            //add tax array, total and grand total to order form object
            $order_form->tax_array = $tax_array;
            $order_form->total = number_format($total, 2, ',' , '.');
            $order_form->grand_total = number_format($grand_total, 2, ',' , '.');

            return ['status' => 1, 'data' => $order_form];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //update order form
    public function updateOrderForm($id, $date, $client, $delivery_date, $location, $note, $products)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //format order form date and delivery date
            $date = date('Y-m-d H:i:s', strtotime($date));
            $delivery_date = date('Y-m-d', strtotime($delivery_date));

            $order_form = OrderForm::find($id);
            $order_form->order_form_date = $date;
            $order_form->client_id = $client;
            $order_form->delivery_date = $delivery_date;
            $order_form->location = $location;
            $order_form->note = $note;
            $order_form->save();

            //set exclude ofp ids array
            $exclude_ofp_ids_array = [];

            foreach ($products as $key => $product)
            {
                //if ofp id exists update product, else insert new product
                if ($product['ofp_id'])
                {
                    //update order form product
                    $order_form_product = OrderFormProduct::where('order_form_id', '=', $id)
                        ->where('id', '=', $product['ofp_id'])->first();
                    $order_form_product->quantity = $product['quantity'];
                    $order_form_product->price = $product['price'];
                    $order_form_product->note = $product['note'];
                    $order_form_product->save();
                }
                else
                {
                    //get product tax group
                    $tax_group = Product::find($product['id'])->tax_group_id;

                    //insert order form product
                    $order_form_product = new OrderFormProduct;
                    $order_form_product->order_form_id = $id;
                    $order_form_product->product_id = $product['id'];
                    $order_form_product->quantity = $product['quantity'];
                    $order_form_product->price = $product['price'];
                    $order_form_product->tax_group_id = $tax_group;
                    $order_form_product->note = $product['note'];
                    $order_form_product->save();
                }

                //add ofp id to exclude array
                $exclude_ofp_ids_array[] = $order_form_product->id;
            }

            //delete all order form products which are not in exclude ofp id array
            OrderFormProduct::where('order_form_id', '=', $id)->whereNotIn('id', $exclude_ofp_ids_array)->delete();

            //commit transaction
            DB::commit();

            //set update order form flash
            Session::flash('success_message', trans('main.order_form_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete order form
    public function deleteOrderForm($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $order_form = OrderForm::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if order form doesn't exist return error status
            if (!$order_form)
            {
                return ['status' => 0];
            }

            $order_form->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get next order form id
    public function getNextOrderFormId($company_id)
    {
        //get current year
        $year = date('Y');

        //set order form year
        $order_form_year = $year[2].$year[3];

        //set default doc number
        $doc_number = 1;

        //get max doc number
        $max_doc_number = OrderForm::where('company_id', '=', $company_id)->whereRaw('YEAR(order_form_date) = ?', [$year])
            ->max('doc_number');

        if ($max_doc_number)
        {
            //set doc_number
            $doc_number = $max_doc_number + 1;
        }

        //set order form id
        $order_form_id = $doc_number.'/'.$order_form_year;

        return ['order_form_id' => $order_form_id, 'doc_number' => $doc_number];
    }

    //get products
    public function getProducts($products)
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

                foreach ($products as $key => $order_form_product)
                {
                    //if product doesn't exist return error status
                    $product = Product::with('unit')
                        ->select('unit_id', 'code', 'tax_group_id', 'name', 'price', 'description')
                        ->where('company_id', '=', $company_id)->where('id', '=', $order_form_product['id'])->first();

                    if (!$product)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }

                    $note = '';

                    //call getProductsListPrice method to get product price
                    $price_response = $this->getProductsListPrice($product, $order_form_product['price']);

                    //get product sum
                    $sum = $price_response['price'] * $order_form_product['quantity'];

                    $total += $sum;

                    //format tax groups array and add tax sum to grand total
                    $tax_groups_array[$price_response['tax_percentage']][] = $sum / 100 * $price_response['tax_percentage'];

                    $grand_total += $sum + ($sum / 100 * $price_response['tax_percentage']);

                    if ($order_form_product['price'] == 0)
                    {
                        $note = $product->description;
                    }
                    else
                    {
                        if ($order_form_product['note'])
                        {
                            $note = $order_form_product['note'];
                        }
                    }

                    //add product to products array
                    $products_array[] = array('id' => $key, 'product_id' => $order_form_product['id'], 'code' => $product->code,
                        'name' => $product->name, 'unit' => trans('main.'.$product->unit->code),
                        'quantity' => $order_form_product['quantity'],
                        'list_quantity' => number_format($order_form_product['quantity'], 2, ',' , '.'),
                        'price' => $price_response['object_price'],
                        'list_price' => number_format($price_response['price'], 2, ',' , '.'), 'note' => htmlspecialchars($note),
                        'tax' => number_format($price_response['tax_percentage'], 2, ',' , '.'),
                        'sum' => number_format($sum, 2, ',' , '.'), 'ofp_id' => $order_form_product['ofp_id']);
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
    private function getProductsListPrice($product_object, $price)
    {
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

        if ($price == 0)
        {
            $product_price = $product_object->price;

            $object_price = $product_object->price;
        }
        else
        {
            $product_price = $price;

            $object_price = $price;
        }

        return ['price' => $product_price, 'object_price' => $object_price, 'tax_percentage' => $tax_percentage];
    }

    //get order form sum
    public function getOrderFormSum($order_form_id)
    {
        $order_form = OrderForm::select('client_id', DB::raw('DATE_FORMAT(order_form_date, "%Y-%m-%d") AS date'))
            ->where('id', '=', $order_form_id)->first();

        $products = OrderFormProduct::select('quantity', 'price', 'tax_group_id')->where('order_form_id', '=', $order_form_id)
            ->get();

        $grand_total = 0;

        foreach ($products as $product)
        {
            //call getCurrentTaxPercentage method from TaxGroupRepository to get current tax percentage
            $repo = new TaxGroupRepository;
            $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $order_form->date);

            //if response status = '0' return error message
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

            $order_form = OrderForm::with('client', 'user')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)
                ->first();

            //if order form doesn't exist return error status
            if (!$order_form)
            {
                return ['status' => 0];
            }

            //get products
            $products = OrderFormProduct::with('product')
                ->where('order_form_id', '=', $id)->orderBy('id', 'asc')->get();

            $order_form_date = date('Y-m-d', strtotime($order_form->order_form_date));

            $company = Company::find($company_id);

            $admin = $this->getCompanyAdmin($company_id);

            $order_form_no_text = trans('main.order_form_no');

            $order_form->logo = public_path().'/logo/'.$company->logo;
            $order_form->order_form_no_text = $order_form_no_text;
            $order_form->date = date('d.m.Y. H:i', strtotime($order_form->order_form_date));
            $order_form->delivery_date = date('d.m.Y.', strtotime($order_form->delivery_date));

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
                $tax_percentage_response = $repo->getCurrentTaxPercentage($product->tax_group_id, $order_form_date);

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

            return ['status' => 1, 'order_form' => $order_form, 'company' => $company, 'products' => $products_array,
                'tax_array' => $tax_array, 'sum_array' => $sum_array, 'admin' => $admin];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
