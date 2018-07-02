<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Client;
use App\ZipCode;
use App\Offer;
use App\Invoice;
use App\Dispatch;
use App\Contract;
use App\ClientPrice;

class ClientRepository extends UserRepository
{
    //get clients
    public function getClients($search_string, $type)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $clients = Client::select('id', 'name', 'address', 'city', 'phone', 'email')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F');

            if ($search_string)
            {
                $clients->where('name', 'like', '%'.$search_string.'%');
            }

            if ($type)
            {
                $clients->where('client_type', '=', $type);
            }

            $clients = $clients->paginate(30);

            return ['status' => 1, 'data' => $clients];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert client
    public function insertClient($document_insert, $type, $name, $oib, $tax_number, $address, $city, $zip_code, $zip_code_id,
        $country, $phone, $email, $int_client, $rebate)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            if ($country == 'HR')
            {
                //get zip code code
                $code = ZipCode::select('code')->where('id', '=', $zip_code_id)->first();
                $zip_code = $code->code;
            }

            $client = new Client;
            $client->company_id = $company_id;
            $client->client_type = $type;
            $client->name = $name;
            $client->oib = $oib;
            $client->tax_number = $tax_number;
            $client->address = $address;
            $client->city = $city;
            $client->zip_code = $zip_code;
            $client->zip_code_id = $zip_code_id;
            $client->country = $country;
            $client->phone = $phone;
            $client->email = $email;
            $client->int_client = $int_client;
            $client->rebate = $rebate;
            $client->save();

            //if document insert = 'F' set update client flash
            if ($document_insert == 'F')
            {
                Session::flash('success_message', trans('main.client_insert'));
            }

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get client details
    public function getClientDetails($id)
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

            //get client products prices
            $products_prices = ClientPrice::with('product')->where('client_id', '=', $client->id)->get();

            foreach ($products_prices as $product)
            {
                //format product price
                $product->price = number_format($product->price, 2, ',', '.');
            }

            //add products prices to client object
            $client->prices = $products_prices;

            return ['status' => 1, 'data' => $client];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update product
    public function updateClient($id, $type, $name, $oib, $tax_number, $address, $city, $zip_code, $zip_code_id, $country, $phone,
        $email, $int_client, $rebate)
    {
        try
        {
            if ($country == 'HR')
            {
                //get zip code code
                $code = ZipCode::select('code')->where('id', '=', $zip_code_id)->first();
                $zip_code = $code->code;
            }

            $client = Client::find($id);
            $client->client_type = $type;
            $client->name = $name;
            $client->oib = $oib;
            $client->tax_number = $tax_number;
            $client->address = $address;
            $client->city = $city;
            $client->zip_code = $zip_code;
            $client->zip_code_id = $zip_code_id;
            $client->country = $country;
            $client->phone = $phone;
            $client->email = $email;
            $client->int_client = $int_client;
            $client->rebate = $rebate;
            $client->save();

            //set update client flash
            Session::flash('success_message', trans('main.client_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete client
    public function deleteClient($id)
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

            //check offers clients
            $offers_check = Offer::where('client_id', '=', $id)->count();

            //check invoices clients
            $invoices_check = Invoice::where('client_id', '=', $id)->count();

            //check dispatches clients
            $dispatches_check = Dispatch::where('client_id', '=', $id)->count();

            //check contracts clients
            $contracts_check = Contract::where('client_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if client is assigned to some offer, invoice, dispatch or contract set deleted status to 'T', else delete product
            if ($offers_check > 0 || $invoices_check > 0 || $dispatches_check > 0 || $contracts_check > 0)
            {
                //set deleted status to 'T'
                $client->deleted = 'T';
                $client->save();
            }
            else
            {
                //delete client
                $client->delete();
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

    //search clients
    public function searchClients($search_string)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $clients = Client::select('id', 'name', 'oib', 'address', 'city')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->where('name', 'like', '%'.$search_string.'%')
                ->take(30)->get();

            foreach ($clients as $client)
            {
                //add html special characters to client name
                $client->name = htmlspecialchars($client->name);
            }

            return ['status' => 1, 'clients' => $clients];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //insert client price
    public function insertClientPrice($client_id, $product_id, $price)
    {
        try
        {
            $client_price = new ClientPrice;
            $client_price->client_id = $client_id;
            $client_price->product_id = $product_id;
            $client_price->price = $price;
            $client_price->save();

            //get client products prices
            $products_prices = ClientPrice::with('product')->where('client_id', '=', $client_id)->get();

            foreach ($products_prices as $product)
            {
                $product->id = $product->product->id;
                $product->name = $product->product->name;

                //format product price
                $product->price = number_format($product->price, 2, ',', '.');
            }

            return ['status' => 1, 'prices' => $products_prices, 'success' => trans('main.client_price_insert')];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete client price
    public function deleteClientPrice($client_id, $product_id)
    {
        try
        {
            $client_price = ClientPrice::where('client_id', '=', $client_id)->where('product_id', '=', $product_id)->first();

            //if client price doesn't exist return error message
            if (!$client_price)
            {
                return ['status' => 0];
            }

            //delete client price
            $client_price->delete();

            //get client products prices
            $products_prices = ClientPrice::with('product')->where('client_id', '=', $client_id)->get();

            foreach ($products_prices as $product)
            {
                $product->id = $product->product->id;
                $product->name = $product->product->name;

                //format product price
                $product->price = number_format($product->price, 2, ',', '.');
            }

            return ['status' => 1, 'prices' => $products_prices, 'success' => trans('main.client_price_delete')];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }
}
