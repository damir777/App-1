<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Employee;
use App\Repositories\UserRepository;
use App\Repositories\OfficeRepository;
use App\Repositories\EmployeeRepository;

class UserController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new UserRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    //get users
    public function getUsers()
    {
        //call getUsers method from UserRepository to get users
        $users = $this->repo->getUsers();

        //if response status = '0' show error page
        if ($users['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.users.list', ['users' => $users['data']]);
    }

    //add user
    public function addUser()
    {
        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getRegisterSelect method from OfficeRepository to get registers - select
        $registers = $this->repo->getRegistersSelect(1);

        //if response status = '0' show error page
        if ($offices['status'] == 0 || $registers['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.users.addUser', ['offices' => $offices['data'], 'registers' => $registers['data']]);
    }

    //insert user
    public function insertUser(Request $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $password = $request->password;
        $phone = $request->phone;
        $office = $request->office;
        $register = $request->register;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), User::validateUserForm($company_id, $office, $register));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddUser')->withErrors($validator)->with('error_message', trans('errors.validation_error'))
                ->withInput();
        }

        //call insertUser method from UserRepository to insert user
        $response = $this->repo->insertUser($first_name, $last_name, $email, $password, $phone, $office, $register);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddUser')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetUsers')->with('success_message', trans('main.user_insert'));
    }

    //edit user
    public function editUser($id)
    {
        //call getOfficesSelect method from OfficeRepository to get offices - select
        $this->repo = new OfficeRepository;
        $offices = $this->repo->getOfficesSelect(1);

        //call getRegisterSelect method from OfficeRepository to get registers - select
        $registers = $this->repo->getRegistersSelect(1);

        //call getUserDetails method from UserRepository to get user details
        $this->repo = new UserRepository;
        $user = $this->repo->getUserDetails($id);

        //if response status = '0' return error message
        if ($offices['status'] == 0 || $registers['status'] == 0 || $user['status'] == 0)
        {
            return redirect()->route('GetUsers')->with('error_message', trans('errors.error'));
        }

        return view('app.users.editUser', ['offices' => $offices['data'], 'registers' => $registers['data'],
            'user' => $user['data']]);
    }

    //update user
    public function updateUser(Request $request)
    {
        $id = $request->id;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $password = $request->password;
        $phone = $request->phone;
        $office = $request->office;
        $register = $request->register;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), User::validateUserForm($company_id, $office, $register, $id, $password));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditUser', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateUser method from UserRepository to update user
        $response = $this->repo->updateUser($id, $first_name, $last_name, $email, $password, $phone, $office, $register);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditUser', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetUsers')->with('success_message', trans('main.user_update'));
    }

    //delete user
    public function deleteUser($id)
    {
        //call deleteUser method from UserRepository to delete user
        $response = $this->repo->deleteUser($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetUsers')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetUsers')->with('success_message', trans('main.user_delete'));
    }

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    //get employees
    public function getEmployees()
    {
        //call getEmployees method from EmployeeRepository to get employees
        $this->repo = new EmployeeRepository;
        $employees = $this->repo->getEmployees();

        //if response status = '0' show error page
        if ($employees['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.employees.list', ['employees' => $employees['data']]);
    }

    //add employee
    public function addEmployee()
    {
        return view('app.employees.addEmployee');
    }

    //insert employee
    public function insertEmployee(Request $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $phone = $request->phone;
        $job_title = $request->job_title;

        //validate form inputs
        $validator = Validator::make($request->all(), Employee::validateEmployeeForm());

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddEmployee')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call insertEmployee method from EmployeeRepository to insert employee
        $this->repo = new EmployeeRepository;
        $response = $this->repo->insertEmployee($first_name, $last_name, $email, $phone, $job_title);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddEmployee')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetEmployees')->with('success_message', trans('main.employee_insert'));
    }

    //edit employee
    public function editEmployee($id)
    {
        //call getEmployeeDetails method from EmployeeRepository to get employee details
        $this->repo = new EmployeeRepository;
        $employee = $this->repo->getEmployeeDetails($id);

        //if response status = '0' return error message
        if ($employee['status'] == 0)
        {
            return redirect()->route('GetEmployees')->with('error_message', trans('errors.error'));
        }

        return view('app.employees.editEmployee', ['employee' => $employee['data']]);
    }

    //update employee
    public function updateEmployee(Request $request)
    {
        $id = $request->id;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $phone = $request->phone;
        $job_title = $request->job_title;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Employee::validateEmployeeForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditEmployee', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateEmployee method from EmployeeRepository to update employee
        $this->repo = new EmployeeRepository;
        $response = $this->repo->updateEmployee($id, $first_name, $last_name, $email, $phone, $job_title);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditEmployee', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetEmployees')->with('success_message', trans('main.employee_update'));
    }

    //delete employee
    public function deleteEmployee($id)
    {
        //call deleteEmployee method from EmployeeRepository to delete employee
        $this->repo = new EmployeeRepository;
        $response = $this->repo->deleteEmployee($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetEmployees')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetEmployees')->with('success_message', trans('main.employee_delete'));
    }
}
