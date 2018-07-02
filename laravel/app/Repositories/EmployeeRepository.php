<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Employee;
use App\TravelWarrant;
use App\PayoutSlip;

class EmployeeRepository extends UserRepository
{
    //get employees
    public function getEmployees()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $employees = Employee::select('id', 'first_name', 'last_name', 'email', 'phone', 'job_title')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->paginate(30);

            return ['status' => 1, 'data' => $employees];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert employee
    public function insertEmployee($first_name, $last_name, $email, $phone, $job_title)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $employee = new Employee;
            $employee->company_id = $company_id;
            $employee->first_name = $first_name;
            $employee->last_name = $last_name;
            $employee->email = $email;
            $employee->phone = $phone;
            $employee->job_title = $job_title;
            $employee->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get employee details
    public function getEmployeeDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $employee = Employee::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if employee doesn't exist return error status
            if (!$employee)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $employee];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update employee
    public function updateEmployee($id, $first_name, $last_name, $email, $phone, $job_title)
    {
        try
        {
            $employee = Employee::find($id);
            $employee->first_name = $first_name;
            $employee->last_name = $last_name;
            $employee->email = $email;
            $employee->phone = $phone;
            $employee->job_title = $job_title;
            $employee->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete employee
    public function deleteEmployee($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $employee = Employee::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')
                ->first();

            //if employee doesn't exist return error status
            if (!$employee)
            {
                return ['status' => 0];
            }

            //check travel warrants employees
            $travel_warrants_check = TravelWarrant::where('creator_id', '=', $id)->orWhere('user_id', '=', $id)->count();

            //check payout slips employees
            $payout_slips_check = PayoutSlip::where('employee_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if employee is assigned to some travel warrant or payout slip set deleted status to 'T', else delete employee
            if ($travel_warrants_check > 0 || $payout_slips_check > 0)
            {
                //set deleted status to 'T'
                $employee->deleted = 'T';
                $employee->save();
            }
            else
            {
                //delete employee
                $employee->delete();
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

    //get employees - select
    public function getEmployeesSelect()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set employees array
            $employees_array = [];

            $employees = Employee::select('id', 'first_name', 'last_name')->where('company_id', '=', $company_id)
                ->where('deleted', '=', 'F')->get();

            //loop through all employees
            foreach ($employees as $employee)
            {
                //add employee to employees array
                $employees_array[$employee->id] = $employee->first_name.' '.$employee->last_name;
            }

            return ['status' => 1, 'data' => $employees_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
