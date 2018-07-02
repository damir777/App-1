<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Notifications\newUser;
use App\Role;
use App\RoleUser;
use App\User;
use App\Company;
use App\Offer;
use App\Invoice;
use App\Dispatch;
use App\Contract;

class UserRepository
{
    //get company id
    public function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    //get user id
    public function getUserId()
    {
        return Auth::user()->id;
    }

    //get user role
    public function getUserRole()
    {
        $user = Auth::user();

        return $user->roles;
    }

    //get company admin
    public function getCompanyAdmin($company_id)
    {
        $admin = User::with('role')
            ->where('company_id', '=', $company_id)
            ->whereHas('role', function($query) {
               $query->where('role_id', '=', 2);
            })->first();

        return $admin->first_name.' '.$admin->last_name;
    }

    //create admin
    public function createAdmin($company_id, $first_name, $last_name, $email, $password, $token, $phone)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //get admin role
            $role = Role::where('name', '=', 'Admin')->first();

            $admin = new User;
            $admin->company_id = $company_id;
            $admin->first_name = $first_name;
            $admin->last_name = $last_name;
            $admin->email = $email;
            $admin->password = Hash::make($password);
            $admin->remember_token = $token;
            $admin->phone = $phone;
            $admin->save();

            //attach admin role to admin
            $admin->attachRole($role);

            //commit transaction
            DB::commit();

            return ['status' => 1, 'user' => $admin];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //confirm admin account
    public function confirmAccount($token)
    {
        try
        {
            $user = User::where('remember_token', '=', $token)->where('active', '=', 'F')->first();

            //if user doesn't exist return error status
            if (!$user)
            {
                return array('status' => 0);
            }

            //start transaction
            DB::beginTransaction();

            //change user active status to 'T' and set remember token to 'NULL'
            $user->active = 'T';
            $user->remember_token = NULL;
            $user->save();

            //set licence and date
            $date = date('Y-m-d', strtotime('+1 month', strtotime(date('Y-m-d'))));

            //update company active status and licence end
            $company = Company::find($user->company_id);
            $company->active = 'T';
            $company->licence_end = $date;
            $company->save();
            /*
			//call insertDefaultData method to insert default data
            $response = $this->insertDefaultData($company_id->company_id);

            //if response status = 0 print error message
            if ($response['status'] == 0)
            {
                return array('status' => 0);
            }
            */
            //create temp user and send mail to info@xxs.com
            (new User)->forceFill([
                'name' => 'xx',
                'email' => 'info@xx.com'
            ])->notify(new newUser($user));

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get users
    public function getUsers()
    {
        try
        {
            //call getCompanyId method to get company id
            $company_id = $this->getCompanyId();

            $users = User::with('role')
                ->select('id', 'first_name', 'last_name', 'email', 'phone')
                ->where('company_id', '=', $company_id)->where('active', '=', 'T')->where('id', '!=', 32)->paginate(30);

            return ['status' => 1, 'data' => $users];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert user
    public function insertUser($first_name, $last_name, $email, $password, $phone, $office, $register)
    {
        try
        {
            //call getCompanyId method to get company id
            $company_id = $this->getCompanyId();

            //start transaction
            DB::beginTransaction();

            //get user role
            $role = Role::where('name', '=', 'User')->first();

            $user = new User;
            $user->company_id = $company_id;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->phone = $phone;
            $user->office_id = $office;
            $user->register_id = $register;
            $user->active = 'T';
            $user->save();

            //attach user role to user
            $user->attachRole($role);

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get user details
    public function getUserDetails($id)
    {
        try
        {
            //call getCompanyId method to get company id
            $company_id = $this->getCompanyId();

            $user = User::where('company_id', '=', $company_id)->where('id', '=', $id)->where('active', '=', 'T')
                ->where('id', '!=', 32)->first();

            //if user doesn't exist return error status
            if (!$user)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $user];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update user
    public function updateUser($id, $first_name, $last_name, $email, $password, $phone, $office, $register)
    {
        try
        {
            $user = User::find($id);
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->email = $email;

            if ($password)
            {
                $user->password = Hash::make($password);
            }

            $user->phone = $phone;
            $user->office_id = $office;
            $user->register_id = $register;
            $user->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete user
    public function deleteUser($id)
    {
        try
        {
            //call getCompanyId method to get company id
            $company_id = $this->getCompanyId();

            $user = User::where('company_id', '=', $company_id)->where('id', '=', $id)->where('active', '=', 'T')->first();

            //if user doesn't exist return error status
            if (!$user)
            {
                return ['status' => 0];
            }

            //check offers users
            $offers_check = Offer::where('user_id', '=', $id)->count();

            //check invoices users
            $invoices_check = Invoice::where('user_id', '=', $id)->count();

            //check dispatches users
            $dispatches_check = Dispatch::where('user_id', '=', $id)->count();

            //check contracts users
            $contracts_check = Contract::where('user_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if user is assigned to some offer, invoice, dispatch or contract set active status to 'F', else delete user
            if ($offers_check > 0 || $invoices_check > 0 || $dispatches_check > 0 || $contracts_check > 0)
            {
                //set active status to 'F'
                $user->active = 'F';
                $user->save();
            }
            else
            {
                //delete user assigned role
                RoleUser::where('user_id', '=', $id)->where('role_id', '=', 3)->delete();

                //delete user
                $user->delete();
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

    //get user settings
    public function getUserSettings()
    {
        try
        {
            $user = User::find($this->getUserId());
            $office = $user->office_id;
            $register = $user->register_id;

            $location = Company::find($this->getCompanyId())->city;

            return ['status' => 1, 'office' => $office, 'register' => $register, 'location' => $location];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //login as user
    public function loginAsUser($id)
    {
        try
        {
            $user = User::find($id);

            //login user
            Auth::login($user);

            //set super admin session
            Session::put('super_admin', 1);

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //deactivate user
    public function deactivateUser($id)
    {
        try
        {
            //set active status to 'F'
            $user = User::find($id);
            $user->active = 'F';
            $user->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //activate user
    public function activateUser($id)
    {
        try
        {
            //set active status to 'T'
            $user = User::find($id);
            $user->active = 'T';
            $user->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
