<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CustomValidator\Validator as CustomValidator;
use Barryvdh\DomPDF\Facade as PDF;
use App\Vehicle;
use App\Wage;
use App\Repositories\TravelWarrantRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\EmployeeRepository;

class TravelWarrantController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new TravelWarrantRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Vehicles
    |--------------------------------------------------------------------------
    */

    //get vehicles
    public function getVehicles()
    {
        //call getVehicles method from TravelWarrantRepository to get vehicles
        $vehicles = $this->repo->getVehicles();

        //if response status = '0' show error page
        if ($vehicles['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.travelWarrants.vehicles.list', ['vehicles' => $vehicles['data']]);
    }

    //add vehicle
    public function addVehicle()
    {
        return view('app.travelWarrants.vehicles.addVehicle');
    }

    //insert vehicle
    public function insertVehicle(Request $request)
    {
        $vehicle_type = $request->vehicle_type;
        $name = $request->name;
        $register_number = $request->register_number;
        $year = $request->year;
        $km = $request->km;

        //validate form inputs
        $validator = Validator::make($request->all(), Vehicle::validateVehicleForm());

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddVehicle')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call insertVehicle method from TravelWarrantRepository to insert vehicle
        $response = $this->repo->insertVehicle($vehicle_type, $name, $register_number, $year, $km);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddVehicle')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetVehicles')->with('success_message', trans('main.vehicle_insert'));
    }

    //edit vehicle
    public function editVehicle($id)
    {
        //call getVehicleDetails method from TravelWarrantRepository to get vehicle details
        $vehicle = $this->repo->getVehicleDetails($id);

        //if response status = '0' return error message
        if ($vehicle['status'] == 0)
        {
            return redirect()->route('GetVehicles')->with('error_message', trans('errors.error'));
        }

        return view('app.travelWarrants.vehicles.editVehicle', ['vehicle' => $vehicle['data']]);
    }

    //update vehicle
    public function updateVehicle(Request $request)
    {
        $id = $request->id;
        $vehicle_type = $request->vehicle_type;
        $name = $request->name;
        $register_number = $request->register_number;
        $year = $request->year;
        $km = $request->km;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Vehicle::validateVehicleForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditVehicle', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateVehicle method from TravelWarrantRepository to update vehicle
        $response = $this->repo->updateVehicle($id, $vehicle_type, $name, $register_number, $year, $km);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditVehicle', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetVehicles')->with('success_message', trans('main.vehicle_update'));
    }

    //delete vehicle
    public function deleteVehicle($id)
    {
        //call deleteVehicle method from TravelWarrantRepository to delete vehicle
        $response = $this->repo->deleteVehicle($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetVehicles')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetVehicles')->with('success_message', trans('main.vehicle_delete'));
    }

    /*
    |--------------------------------------------------------------------------
    | Wages
    |--------------------------------------------------------------------------
    */

    //get wages
    public function getWages()
    {
        //call getWages method from TravelWarrantRepository to get wages
        $wages = $this->repo->getWages();

        //if response status = '0' show error page
        if ($wages['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.travelWarrants.wages.list', ['wages' => $wages['data']]);
    }

    //add wage
    public function addWage()
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //if response status = '0' show error page
        if ($countries['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.travelWarrants.wages.addWage', ['countries' => $countries['data']]);
    }

    //insert wage
    public function insertWage(Request $request)
    {
        $name = $request->name;
        $country = $request->country;
        $price = $request->price;

        //validate form inputs
        $validator = Validator::make($request->all(), Wage::validateWageForm());

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('AddWage')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call insertWage method from TravelWarrantRepository to insert wage
        $response = $this->repo->insertWage($name, $country, $price);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('AddWage')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetWages')->with('success_message', trans('main.wage_insert'));
    }

    //edit wage
    public function editWage($id)
    {
        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getWageDetails method from TravelWarrantRepository to get wage details
        $this->repo = new TravelWarrantRepository;
        $wage = $this->repo->getWageDetails($id);

        //if response status = '0' return error message
        if ($wage['status'] == 0)
        {
            return redirect()->route('GetWages')->with('error_message', trans('errors.error'));
        }

        return view('app.travelWarrants.wages.editWage', ['countries' => $countries['data'], 'wage' => $wage['data']]);
    }

    //update wage
    public function updateWage(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $country = $request->country;
        $price = $request->price;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = Validator::make($request->all(), Wage::validateWageForm($company_id, $id));

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('EditWage', $id)->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //call updateWage method from TravelWarrantRepository to update wage
        $response = $this->repo->updateWage($id, $name, $country, $price);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('EditWage', $id)->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('GetWages')->with('success_message', trans('main.wage_update'));
    }

    //delete wage
    public function deleteWage($id)
    {
        //call deleteWage method from TravelWarrantRepository to delete wage
        $response = $this->repo->deleteWage($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetWages')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetWages')->with('success_message', trans('main.wage_delete'));
    }

    /*
    |--------------------------------------------------------------------------
    | Warrants
    |--------------------------------------------------------------------------
    */

    //get travel warrants
    public function getTravelWarrants()
    {
        //call getTravelWarrants method from TravelWarrantRepository to get warrants
        $warrants = $this->repo->getTravelWarrants();

        //if response status = '0' show error page
        if ($warrants['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.travelWarrants.warrants.list', ['warrants' => $warrants['data']]);
    }

    //add travel warrant
    public function addTravelWarrant()
    {
        //call getEmployeesSelect method from EmployeeRepository to get employees - select
        $this->repo = new EmployeeRepository;
        $employees = $this->repo->getEmployeesSelect();

        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getVehiclesSelect method from TravelWarrantRepository to get vehicles - select
        $this->repo = new TravelWarrantRepository;
        $vehicles = $this->repo->getVehiclesSelect();

        //call getWagesSelect method from TravelWarrantRepository to get wages - select
        $wages = $this->repo->getWagesSelect();

        //if response status = '0' show error page
        if ($employees['status'] == 0 || $countries['status'] == 0 || $vehicles['status'] == 0 || $wages['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.travelWarrants.warrants.addWarrant', ['employees' => $employees['data'],
            'countries' => $countries['data'], 'vehicles' => $vehicles['data'], 'wages' => $wages['data']]);
    }

    //insert travel warrant
    public function insertTravelWarrant(Request $request)
    {
        $creator = $request->creator;
        $user = $request->user;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $vehicle = $request->vehicle;
        $start_mileage = $request->start_mileage;
        $end_mileage = $request->end_mileage;
        $duration = $request->duration;
        $location = $request->location;
        $purpose = $request->purpose;
        $description = $request->description;
        $advance = $request->advance;
        $non_costs = $request->non_costs;
        $note = $request->note;
        $report = $request->report;
        $wages = $request->wages;
        $directions = $request->directions;
        $costs = $request->costs;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::travelWarrants($company_id);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call insertTravelWarrant method from TravelWarrantRepository to insert travel warrant
        $response = $this->repo->insertTravelWarrant($creator, $user, $start_date, $end_date, $vehicle, $start_mileage,
            $end_mileage, $duration, $location, $purpose, $description, $advance, $non_costs, $note, $report, $wages, $directions,
            $costs);

        return response()->json($response);
    }

    //edit travel warrant
    public function editTravelWarrant($id)
    {
        //call getEmployeesSelect method from EmployeeRepository to get employees - select
        $this->repo = new EmployeeRepository;
        $employees = $this->repo->getEmployeesSelect();

        //call getCountriesSelect method from CompanyRepository to get countries - select
        $this->repo = new CompanyRepository;
        $countries = $this->repo->getCountriesSelect();

        //call getVehiclesSelect method from TravelWarrantRepository to get vehicles - select
        $this->repo = new TravelWarrantRepository;
        $vehicles = $this->repo->getVehiclesSelect();

        //call getWagesSelect method from TravelWarrantRepository to get wages - select
        $wages = $this->repo->getWagesSelect();

        //call getTravelWarrantDetails method from TravelWarrantRepository to get travel warrant details
        $warrant = $this->repo->getTravelWarrantDetails($id);

        //if response status = '0' return error message
        if ($employees['status'] == 0 || $countries['status'] == 0 || $vehicles['status'] == 0 || $wages['status'] == 0 ||
            $warrant['status'] == 0)
        {
            return redirect()->route('GetTravelWarrants')->with('error_message', trans('errors.error'));
        }

        return view('app.travelWarrants.warrants.editWarrant', ['employees' => $employees['data'],
            'countries' => $countries['data'], 'vehicles' => $vehicles['data'], 'wages' => $wages['data'],
            'warrant' => $warrant['data']]);
    }

    //update travel warrant
    public function updateTravelWarrant(Request $request)
    {
        $id = $request->id;
        $creator = $request->creator;
        $user = $request->user;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $vehicle = $request->vehicle;
        $start_mileage = $request->start_mileage;
        $end_mileage = $request->end_mileage;
        $duration = $request->duration;
        $location = $request->location;
        $purpose = $request->purpose;
        $description = $request->description;
        $advance = $request->advance;
        $non_costs = $request->non_costs;
        $note = $request->note;
        $report = $request->report;
        $wages = $request->wages;
        $directions = $request->directions;
        $costs = $request->costs;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::travelWarrants($company_id);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call updateTravelWarrant method from TravelWarrantRepository to update travel warrant
        $response = $this->repo->updateTravelWarrant($id, $creator, $user, $start_date, $end_date, $vehicle, $start_mileage,
            $end_mileage, $duration, $location, $purpose, $description, $advance, $non_costs, $note, $report, $wages, $directions,
            $costs);

        return response()->json($response);
    }

    //delete travel warrant
    public function deleteTravelWarrant($id)
    {
        //call deleteTravelWarrant method from TravelWarrantRepository to delete travel warrant
        $response = $this->repo->deleteTravelWarrant($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetTravelWarrants')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetTravelWarrants')->with('success_message', trans('main.travel_warrant_delete'));
    }

    //copy travel warrant
    public function copyTravelWarrant($id)
    {
        //call copyTravelWarrant method from TravelWarrantRepository to copy travel warrant
        $response = $this->repo->copyTravelWarrant($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetTravelWarrants')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetTravelWarrants')->with('success_message', trans('main.travel_warrant_copy'));
    }

    //calculate distance
    public function calculateDistance(Request $request)
    {
        $start_location = $request->start_location;
        $end_location = $request->end_location;

        //call calculateDistance method from TravelWarrantRepository to calculate direction distance
        $response = $this->repo->calculateDistance($start_location, $end_location);

        return response()->json($response);
    }

    //delete item
    public function deleteItem(Request $request)
    {
        $warrant_id = $request->warrant_id;
        $item_type = $request->item_type;
        $item_id = $request->item_id;

        //call deleteItem method from TravelWarrantRepository to delete item
        $response = $this->repo->deleteItem($warrant_id, $item_type, $item_id);

        return response()->json($response);
    }

    //pdf travel warrant
    public function pdfTravelWarrant($id)
    {
        //call travelWarrantPdfData method from TravelWarrantRepository to get travel warrant pdf data
        $data = $this->repo->travelWarrantPdfData($id);

        //if response status = 0 return error message
        if ($data['status'] == 0)
        {
            return view('errors.500');
        }

        $pdf = PDF::loadView('app.travelWarrants.warrants.pdf', ['data' => $data]);

        return $pdf->stream('xx - '.$data['warrant']['warrant']->warrant_id.'.pdf');
    }
}
