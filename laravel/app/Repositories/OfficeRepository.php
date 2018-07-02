<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Office;
use App\Register;
use App\Offer;
use App\Invoice;
use App\Contract;
use App\PaymentSlip;
use App\PayoutSlip;
use App\RegisterReport;
use App\User;

class OfficeRepository extends UserRepository
{
    /*
    |--------------------------------------------------------------------------
    | Offices
    |--------------------------------------------------------------------------
    */

    //get offices
    public function getOffices()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $offices = Office::select('id', 'label', 'name', 'address', 'city', 'phone')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->paginate(30);

            return ['status' => 1, 'data' => $offices];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert office
    public function insertOffice($label, $name, $address, $city, $phone)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $office = new Office;
            $office->company_id = $company_id;
            $office->label = $label;
            $office->name = $name;
            $office->address = $address;
            $office->city = $city;
            $office->phone = $phone;
            $office->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get office details
    public function getOfficeDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $office = Office::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if office doesn't exist return error status
            if (!$office)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $office];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update office
    public function updateOffice($id, $label, $name, $address, $city, $phone)
    {
        try
        {
            $office = Office::find($id);
            $office->label = $label;
            $office->name = $name;
            $office->address = $address;
            $office->city = $city;
            $office->phone = $phone;
            $office->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete office
    public function deleteOffice($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $office = Office::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if office doesn't exist return error status
            if (!$office)
            {
                return ['status' => 0];
            }

            //check offers offices
            $offers_check = Offer::where('office_id', '=', $id)->count();

            //check invoices offices
            $invoices_check = Invoice::where('office_id', '=', $id)->count();

            //check contracts offices
            $contracts_check = Contract::where('office_id', '=', $id)->count();

            //check payment slips offices
            $payment_slips_check = PaymentSlip::where('office_id', '=', $id)->count();

            //check payout slips offices
            $payout_slips_check = PayoutSlip::where('office_id', '=', $id)->count();

            //check register reports offices
            $register_reports_check = RegisterReport::where('office_id', '=', $id)->count();

            //check registers offices
            $registers_check = Register::where('office_id', '=', $id)->count();

            //check users offices
            $users_check = User::where('office_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if office is assigned to some offer, invoice, contract, payment slip, payout slip, register report, register or user
            //set deleted status to 'T', else delete office
            if ($offers_check > 0 || $invoices_check > 0 || $contracts_check > 0 || $payment_slips_check > 0 ||
                $payout_slips_check > 0 || $register_reports_check > 0 || $registers_check > 0 || $users_check > 0)
            {
                //set deleted status to 'T'
                $office->deleted = 'T';
                $office->save();
            }
            else
            {
                //delete office
                $office->delete();
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

    //get offices - select
    public function getOfficesSelect($default_option = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set offices array
            $offices_array = [];

            $offices = Office::select('id', 'label')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->get();

            if ($default_option)
            {
                //add default option to offices array
                $offices_array[0] = trans('main.choose_office');
            }

            //loop through all offices
            foreach ($offices as $office)
            {
                //add office to offices array
                $offices_array[$office->id] = $office->label;
            }

            return ['status' => 1, 'data' => $offices_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Registers
    |--------------------------------------------------------------------------
    */

    //get registers
    public function getRegisters()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $registers = Register::with('office')
                ->select('id', 'office_id', 'label')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->paginate(30);

            return ['status' => 1, 'data' => $registers];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert register
    public function insertRegister($label, $office)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $register = new Register;
            $register->company_id = $company_id;
            $register->label = $label;
            $register->office_id = $office;
            $register->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get register details
    public function getRegisterDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $register = Register::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if register doesn't exist return error status
            if (!$register)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $register];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update register
    public function updateRegister($id, $label, $office)
    {
        try
        {
            $register = Register::find($id);
            $register->label = $label;
            $register->office_id = $office;
            $register->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete register
    public function deleteRegister($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $register = Register::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if register doesn't exist return error status
            if (!$register)
            {
                return ['status' => 0];
            }

            //check invoices registers
            $invoices_check = Invoice::where('register_id', '=', $id)->count();

            //check contracts registers
            $contracts_check = Contract::where('register_id', '=', $id)->count();

            //check users registers
            $users_check = User::where('register_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if register is assigned to some invoice, contract or user set deleted status to 'T', else delete office
            if ($invoices_check > 0 || $contracts_check > 0 || $users_check > 0)
            {
                //set deleted status to 'T'
                $register->deleted = 'T';
                $register->save();
            }
            else
            {
                //delete register
                $register->delete();
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

    //get registers - select
    public function getRegistersSelect($default_option = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $repo = New UserRepository;
            $company_id = $repo->getCompanyId();

            //set registers array
            $registers_array = [];

            $registers = Register::select('id', 'label')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->get();

            if ($default_option)
            {
                //add default option to registers array
                $registers_array[0] = trans('main.choose_register');
            }

            //loop through all registers
            foreach ($registers as $register)
            {
                //add register to registers array
                $registers_array[$register->id] = $register->label;
            }

            return ['status' => 1, 'data' => $registers_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
