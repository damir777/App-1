<?php

namespace App\CustomValidator;

use Illuminate\Support\Facades\Validator as DefaultValidator;
use Illuminate\Support\Facades\Request as Request;
use Illuminate\Validation\Rule;
use App\Tax;
use App\WarrantWage;
use App\Direction;
use App\Cost;
use App\PayoutSlipItem;
use App\RegisterReport;
use App\ClientPrice;

class Validator
{
    //validate clients form
    public static function clients($retail_client, $type, $int_client, $company_id = false, $id = false)
    {
        //get form input
        $input = Request::all();

        $rules = [
            'client_type' => 'required|integer|in:1,2',
            'name' => 'required',
            'zip_code_text' => 'required_unless:country,HR',
            'zip_code_select' => 'required|integer|exists:zip_codes,id',
            'country' => 'required|exists:countries,code',
            'email' => 'nullable|email',
            'int_client' => 'required|in:T,F',
            'rebate' => 'nullable|decimal'
        ];

        if ($type == 1)
        {
            if ($int_client == 'F')
            {
                $rules['oib'] = 'nullable|oib';
            }
        }
        else
        {
            if ($int_client == 'F')
            {
                $rules['oib'] = 'required|oib';
            }
            else
            {
                $rules['tax_number'] = 'required';
            }
        }

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            $rules['address'] = 'required';
            $rules['city'] = 'required';
        }
        else
        {
            $rules['document_insert'] = 'required|in:T,F';

            if ($retail_client == 'F')
            {
                $rules['address'] = 'required';
                $rules['city'] = 'required';
            }
        }

        $validation = DefaultValidator::make($input, $rules);

        //if form input is not correct return error message
        if (!$validation->passes())
        {
            return ['status' => 0, 'error' => $validation->errors()->all()[0]];
        }

        return ['status' => 1];
    }

    //validate client price
    public static function clientPrice($company_id)
    {
        //get form input
        $input = Request::all();

        $rules = [
            'client' => ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'product' => ['required', 'integer', Rule::exists('products', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'price' => 'required|decimal'
        ];

        $validation = DefaultValidator::make($input, $rules);

        //if form input is not correct return error message
        if (!$validation->passes())
        {
            return ['status' => 0, 'error' => $validation->errors()->all()[0]];
        }

        $product_exists = ClientPrice::where('client_id', '=', $input['client'])->where('product_id', '=', $input['product'])
            ->first();

        //if product already exists return error message
        if ($product_exists)
        {
            return ['status' => 0, 'error' => trans('errors.client_price')];
        }

        return ['status' => 1];
    }

    //validate tax groups form
    public static function taxGroups($company_id = false, $id = false)
    {
        //get form input
        $input = Request::all();

        $rules = [
            'name' => 'required'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('tax_groups', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }

        $validation = DefaultValidator::make($input, $rules);

        //if form input is not correct return error message
        if (!$validation->passes())
        {
            return ['status' => 0, 'error' => trans('errors.validation_error')];
        }

        //if taxes array is not set return error message
        if (!isset($input['tax']))
        {
            return ['status' => 0, 'error' => trans('errors.taxes_array')];
        }

        foreach ($input['tax'] as $key => $tax)
        {
            if ($id && array_key_exists($key, $input['tax_id']))
            {
                $tax_model = Tax::where('group_id', '=', $id)->where('id', '=', $key)->first();

                //if tax doesn't exist return error message
                if (!$tax_model)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }
            }

            //validate tax percentage
            $validation = DefaultValidator::make(['tax' => $tax], Tax::$tax_percentage);

            //if tax percentage is not correct return error message
            if (!$validation->passes())
            {
                return ['status' => 0, 'error' => trans('errors.tax_percentage')];
            }

            //if tax date is not exist return error message
            if (!array_key_exists($key, $input['tax_date']))
            {
                return ['status' => 0, 'error' => trans('errors.tax_date')];
            }

            //validate tax date
            $validation = DefaultValidator::make(['date' => $input['tax_date'][$key]], Tax::$tax_date);

            //if tax date is not correct return error message
            if (!$validation->passes())
            {
                return ['status' => 0, 'error' => trans('errors.tax_date')];
            }
        }

        return ['status' => 1];
    }

    //validate travel warrants form
    public static function travelWarrants($company_id, $id = false)
    {
        //get form input
        $input = Request::all();

        $rules = [
            'creator' => ['required', 'integer', Rule::exists('employees', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'user' => ['required', 'integer', Rule::exists('employees', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'start_date' => 'required|custom_date',
            'end_date' => 'required|custom_date',
            'vehicle' => ['required', 'integer', Rule::exists('vehicles', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'start_mileage' => 'required|integer',
            'end_mileage' => 'required|integer',
            'duration' => 'required',
            'location' => 'required',
            'purpose' => 'required',
            'description' => 'required',
            'advance' => 'nullable|decimal',
            'non_costs' => 'nullable|decimal'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('travel_warrants', 'id')
                ->where(function($query) use ($company_id) {
                    $query->where('company_id', '=', $company_id); })];
        }

        $validation = DefaultValidator::make($input, $rules);

        //if form input is not correct return error message
        if (!$validation->passes())
        {
            return ['status' => 0, 'error' => $validation->errors()->all()[0]];
        }

        //if wages array exists validate wages
        if (isset($input['wages']))
        {
            foreach ($input['wages'] as $wage)
            {
                if ($id && $wage['id'])
                {
                    $wage_model = WarrantWage::where('warrant_id', '=', $id)->where('id', '=', $wage['id'])->first();

                    //if wage doesn't exist return error message
                    if (!$wage_model)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }
                }

                $start_time = $wage['departure_date'].' '.$wage['departure_time'].':00';
                $end_time = $wage['arrival_date'].' '.$wage['arrival_time'].':00';

                //validate wage
                $validation = DefaultValidator::make(['country' => $wage['country'], 'date' => $wage['date'],
                    'wage' => $wage['wage'], 'wage_type' => $wage['wage_type'], 'start_time' => $start_time,
                    'end_time' => $end_time], WarrantWage::validateWageForm($company_id));

                //if wage is not correct return error message
                if (!$validation->passes())
                {
                    return ['status' => 0, 'error' => $validation->errors()->all()[0]];
                }
            }
        }

        //if directions array exists validate directions
        if (isset($input['directions']))
        {
            foreach ($input['directions'] as $direction)
            {
                if ($id && $direction['id'])
                {
                    $direction_model = Direction::where('warrant_id', '=', $id)->where('id', '=', $direction['id'])->first();

                    //if direction doesn't exist return error message
                    if (!$direction_model)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }
                }

                //validate direction
                $validation = DefaultValidator::make(['date' => $direction['date'],
                    'transport_type' => $direction['transport_type'], 'start_location' => $direction['start_location'],
                    'end_location' => $direction['end_location'], 'distance' => $direction['distance'],
                    'km_price' => $direction['km_price']], Direction::validateDirectionForm());

                //if direction is not correct return error message
                if (!$validation->passes())
                {
                    return ['status' => 0, 'error' => $validation->errors()->all()[0]];
                }
            }
        }

        //if costs array exists validate costs
        if (isset($input['costs']))
        {
            foreach ($input['costs'] as $cost)
            {
                if ($id && $cost['id'])
                {
                    $cost_model = Cost::where('warrant_id', '=', $id)->where('id', '=', $cost['id'])->first();

                    //if cost doesn't exist return error message
                    if (!$cost_model)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }
                }

                //validate cost
                $validation = DefaultValidator::make(['date' => $cost['date'], 'cost_type' => $cost['cost_type'],
                    'description' => $cost['description'], 'sum' => $cost['sum'], 'non_costs' => $cost['non_costs']],
                    Cost::validateCostForm());

                //if cost is not correct return error message
                if (!$validation->passes())
                {
                    return ['status' => 0, 'error' => $validation->errors()->all()[0]];
                }
            }
        }

        return ['status' => 1];
    }

    //validate payout slips form
    public static function payoutSlips($company_id, $office_id, $id = false)
    {
        //get form input
        $input = Request::all();

        $rules = [
            'employee' => ['required', 'integer', Rule::exists('employees', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'income' => 'required|in:T,F'
        ];

        if ($office_id)
        {
            $rules['office'] = ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }
        else
        {
            $rules['office'] = 'required|integer';
        }

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('payout_slips', 'id')
                ->where(function($query) use ($company_id) {
                    $query->where('company_id', '=', $company_id); })];
        }

        $validation = DefaultValidator::make($input, $rules);

        //if form input is not correct return error message
        if (!$validation->passes())
        {
            return ['status' => 0, 'error' => $validation->errors()->all()[0]];
        }

        //if items array doesn't exist return error message
        if (!isset($input['items']))
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }

        //validate items
        foreach ($input['items'] as $item)
        {
            if ($id && $item['id'])
            {
                $item_model = PayoutSlipItem::where('slip_id', '=', $id)->where('id', '=', $item['id'])->first();

                //if item doesn't exist return error message
                if (!$item_model)
                {
                    return ['status' => 0, 'error' => trans('errors.error')];
                }
            }

            //validate item
            $validation = DefaultValidator::make(['item' => $item['item'], 'sum' => $item['sum']], PayoutSlipItem::$items);

            //if item is not correct return error message
            if (!$validation->passes())
            {
                return ['status' => 0, 'error' => $validation->errors()->all()[0]];
            }
        }

        return ['status' => 1];
    }

    //validate register reports form
    public static function registerReports($company_id)
    {
        //get form input
        $input = Request::all();

        $rules = [
            'start_date' => 'required|custom_date',
            'end_date' => 'required|custom_date',
            'office' => ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })]
        ];

        $validation = DefaultValidator::make($input, $rules);

        //if form input is not correct return error message
        if (!$validation->passes())
        {
            return ['status' => 0, 'error' => $validation->errors()->all()[0]];
        }

        //format start and end date
        $start_date = date('Y-m-d', strtotime($input['start_date']));
        $end_date = date('Y-m-d', strtotime($input['end_date']));

        //if end date < start date return error message
        if ($end_date < $start_date)
        {
            return ['status' => 0, 'error' => trans('errors.report_dates')];
        }

        $check_report = RegisterReport::where('company_id', '=', $company_id)->where('office_id', '=', $input['office'])
            ->where('end_date', '>=', $start_date)->count();

        //if report exists for selected date interval return warning message
        if ($check_report > 0)
        {
            return ['status' => 2, 'warning' => trans('errors.report_exists')];
        }

        return ['status' => 1];
    }
}
