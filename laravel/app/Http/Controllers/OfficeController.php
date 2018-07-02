<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Office;
use App\Register;
use App\Repositories\OfficeRepository;

class OfficeController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new OfficeRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Offices
    |--------------------------------------------------------------------------
    */

    //get offices
    public function getOffices()
    {
        //call getOffices method from OfficeRepository to get offices
        $offices = $this->repo->getOffices();

        //if response status = '0' show error page
        if ($offices['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.offices.list', ['offices' => $offices['data']]);
    }

    //add office
    public function addOffice()
    {
        return view('app.offices.addOffice');
    }

    //insert office
    public function insertOffice(Request $request)
    {
        $label = $request->label;
        $name = $request->name;
        $address = $request->address;
        $city = $request->city;
        $phone = $request->phone;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Office::validateOfficeForm($company_id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddOffice')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call insertOffice method from OfficeRepository to insert office
        $response = $this->repo->insertOffice($label, $name, $address, $city, $phone);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddOffice')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetOffices')->with('success_message', trans('main.office_insert'));
    }

    //edit office
    public function editOffice($id)
    {
        //call getOfficeDetails method from OfficeRepository to get office details
        $office = $this->repo->getOfficeDetails($id);

        //if response status = '0' return error message
        if ($office['status'] == 0)
        {
            return redirect()->route('GetOffices')->with('error_message', trans('errors.error'));
        }

        return view('app.offices.editOffice', ['office' => $office['data']]);
    }

    //update office
    public function updateOffice(Request $request)
    {
        $id = $request->id;
        $label = $request->label;
        $name = $request->name;
        $address = $request->address;
        $city = $request->city;
        $phone = $request->phone;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Office::validateOfficeForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditOffice', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateOffice method from OfficeRepository to update office
        $response = $this->repo->updateOffice($id, $label, $name, $address, $city, $phone);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditOffice', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetOffices')->with('success_message', trans('main.office_update'));
    }

    //delete office
    public function deleteOffice($id)
    {
        //call deleteOffice method from OfficeRepository to delete office
        $response = $this->repo->deleteOffice($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetOffices')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetOffices')->with('success_message', trans('main.office_delete'));
    }

    /*
    |--------------------------------------------------------------------------
    | Registers
    |--------------------------------------------------------------------------
    */

    //get registers
    public function getRegisters()
    {
        //call getRegisters method from OfficeRepository to get registers
        $registers = $this->repo->getRegisters();

        //if response status = '0' show error page
        if ($registers['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registers.list', ['registers' => $registers['data']]);
    }

    //add register
    public function addRegister()
    {
        //call getOfficesSelect method from OfficeRepository to get offices - select
        $offices = $this->repo->getOfficesSelect();

        //if response status = '0' show error page
        if ($offices['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.registers.addRegister', ['offices' => $offices['data']]);
    }

    //insert register
    public function insertRegister(Request $request)
    {
        $label = $request->label;
        $office = $request->office;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Register::validateRegisterForm($company_id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddRegister')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call insertRegister method from OfficeRepository to insert register
        $response = $this->repo->insertRegister($label, $office);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddRegister')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetRegisters')->with('success_message', trans('main.register_insert'));
    }

    //edit register
    public function editRegister($id)
    {
        //call getOfficesSelect method from OfficeRepository to get offices - select
        $offices = $this->repo->getOfficesSelect();

        //call getRegisterDetails method from OfficeRepository to get register details
        $register = $this->repo->getRegisterDetails($id);

        //if response status = '0' return error message
        if ($offices['status'] == 0 || $register['status'] == 0)
        {
            return redirect()->route('GetRegisters')->with('error_message', trans('errors.error'));
        }

        return view('app.registers.editRegister', ['offices' => $offices['data'], 'register' => $register['data']]);
    }

    //update register
    public function updateRegister(Request $request)
    {
        $id = $request->id;
        $label = $request->label;
        $office = $request->office;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Register::validateRegisterForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditRegister', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateRegister method from OfficeRepository to update register
        $response = $this->repo->updateRegister($id, $label, $office);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditRegister', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetRegisters')->with('success_message', trans('main.register_update'));
    }

    //delete register
    public function deleteRegister($id)
    {
        //call deleteRegister method from OfficeRepository to delete register
        $response = $this->repo->deleteRegister($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetRegisters')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetRegisters')->with('success_message', trans('main.register_delete'));
    }
}
