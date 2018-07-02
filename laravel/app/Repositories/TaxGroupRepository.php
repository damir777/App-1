<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Request as Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\TaxGroup;
use App\Tax;
use App\Product;
use App\OfferProduct;
use App\InvoiceProduct;
use App\DispatchProduct;
use App\ContractProduct;

class TaxGroupRepository extends UserRepository
{
    //get tax groups
    public function getTaxGroups()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $groups = TaxGroup::select('id', 'name')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')
                ->paginate(30);

            return ['status' => 1, 'data' => $groups];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert tax group
    public function insertTaxGroup($name, $note)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //get form input
            $input = Request::all();

            //start transaction
            DB::beginTransaction();

            $group = new TaxGroup;
            $group->company_id = $company_id;
            $group->name = $name;
            $group->note = $note;
            $group->save();

            foreach ($input['tax'] as $key => $tax)
            {
                //format tax date
                $tax_date = date('Y-m-d', strtotime($input['tax_date'][$key]));

                //insert tax
                $tax_model = new Tax;
                $tax_model->group_id = $group->id;
                $tax_model->tax = $tax;
                $tax_model->tax_date = $tax_date;
                $tax_model->save();
            }

            //commit transaction
            DB::commit();

            //set insert tax group flash
            Session::flash('success_message', trans('main.tax_group_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get tax group details
    public function getTaxGroupDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $group = TaxGroup::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')->first();

            //if tax group doesn't exist return error status
            if (!$group)
            {
                return ['status' => 0];
            }

            //get taxes
            $taxes = Tax::select('id', 'tax', DB::raw('DATE_FORMAT(tax_date, "%d.%m.%Y.") AS tax_date'))
                ->where('group_id', '=', $id)->get();

            return ['status' => 1, 'group' => $group, 'taxes' => $taxes];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update tax group
    public function updateTaxGroup($id, $name, $note)
    {
        try
        {
            //get form input
            $input = Request::all();

            //start transaction
            DB::beginTransaction();

            $group = TaxGroup::find($id);
            $group->name = $name;
            $group->note = $note;
            $group->save();

            foreach ($input['tax'] as $key => $tax)
            {
                //format tax date
                $tax_date = date('Y-m-d', strtotime($input['tax_date'][$key]));

                //if tax id exists update tax, else insert new tax
                if (array_key_exists($key, $input['tax_id']))
                {
                    //update tax
                    $tax_model = Tax::find($input['tax_id'][$key]);
                    $tax_model->tax = $tax;
                    $tax_model->tax_date = $tax_date;
                    $tax_model->save();
                }
                else
                {
                    //insert tax
                    $tax_model = new Tax;
                    $tax_model->group_id = $id;
                    $tax_model->tax = $tax;
                    $tax_model->tax_date = $tax_date;
                    $tax_model->save();
                }
            }

            //commit transaction
            DB::commit();

            //set update tax group flash
            Session::flash('success_message', trans('main.tax_group_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete tax group
    public function deleteTaxGroup($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $group = TaxGroup::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')->first();

            //if tax group doesn't exist return error status
            if (!$group)
            {
                return ['status' => 0];
            }

            //check products tax groups
            $products_check = Product::where('tax_group_id', '=', $id)->count();

            //check offers products
            $offers_check = OfferProduct::where('tax_group_id', '=', $id)->count();

            //check invoices products
            $invoices_check = InvoiceProduct::where('tax_group_id', '=', $id)->count();

            //check dispatches products
            $dispatches_check = DispatchProduct::where('tax_group_id', '=', $id)->count();

            //check contracts products
            $contracts_check = ContractProduct::where('tax_group_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if tax group is assigned to some product, offer, invoice, dispatch or contract set deleted status to 'T',
            //else delete tax group
            if ($products_check > 0 || $offers_check > 0 || $invoices_check > 0 || $dispatches_check > 0 || $contracts_check > 0)
            {
                //set deleted status to 'T'
                $group->deleted = 'T';
                $group->save();
            }
            else
            {
                //delete tax group
                $group->delete();
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

    //get tax groups - select
    public function getTaxGroupsSelect()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set tax groups array
            $groups_array = [];

            $groups = TaxGroup::select('id', 'name')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->get();

            //loop through all groups
            foreach ($groups as $group)
            {
                //add group to groups array
                $groups_array[$group->id] = $group->name;
            }

            return ['status' => 1, 'data' => $groups_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get current tax percentage
    public function getCurrentTaxPercentage($tax_group, $date)
    {
        try
        {
            //set default tax percentage
            $tax_percentage = 0;

            //get all taxes from given group
            $taxes = Tax::select('tax', 'tax_date')->where('group_id', '=', $tax_group)->orderBy('tax_date', 'desc')->get();

            foreach ($taxes as $tax)
            {
                $tax_date = date('Y-m-d', strtotime(trim($tax->tax_date)));

                //if date >= tax date set tax percentage and exit loop
                if ($date >= $tax_date)
                {
                    $tax_percentage = $tax->tax;

                    break 1;
                }
            }

            return ['status' => 1, 'data' => $tax_percentage];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
