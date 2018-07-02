<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Vehicle;
use App\Wage;
use App\TravelWarrant;
use App\WarrantWage;
use App\Direction;
use App\Cost;
use App\Company;

class TravelWarrantRepository extends UserRepository
{
    /*
    |--------------------------------------------------------------------------
    | Vehicles
    |--------------------------------------------------------------------------
    */

    //get vehicles
    public function getVehicles()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $vehicles = Vehicle::select('id', 'vehicle_type', 'name', 'register_number', 'km')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->paginate(30);

            return ['status' => 1, 'data' => $vehicles];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert vehicle
    public function insertVehicle($vehicle_type, $name, $register_number, $year, $km)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $vehicle = new Vehicle;
            $vehicle->company_id = $company_id;
            $vehicle->vehicle_type = $vehicle_type;
            $vehicle->name = $name;
            $vehicle->register_number = $register_number;
            $vehicle->year = $year;
            $vehicle->km = $km;
            $vehicle->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get vehicle details
    public function getVehicleDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $vehicle = Vehicle::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')->first();

            //if vehicle doesn't exist return error status
            if (!$vehicle)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $vehicle];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update vehicle
    public function updateVehicle($id, $vehicle_type, $name, $register_number, $year, $km)
    {
        try
        {
            $vehicle = Vehicle::find($id);
            $vehicle->vehicle_type = $vehicle_type;
            $vehicle->name = $name;
            $vehicle->register_number = $register_number;
            $vehicle->year = $year;
            $vehicle->km = $km;
            $vehicle->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete vehicle
    public function deleteVehicle($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $vehicle = Vehicle::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')->first();

            //if vehicle doesn't exist return error status
            if (!$vehicle)
            {
                return ['status' => 0];
            }

            //check travel warrants vehicles
            $travel_warrants_check = TravelWarrant::where('company_id', '=', $company_id)->where('vehicle_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if vehicle is assigned to some travel warrant set deleted status to 'T', else delete vehicle
            if ($travel_warrants_check > 0)
            {
                //set deleted status to 'T'
                $vehicle->deleted = 'T';
                $vehicle->save();
            }
            else
            {
                //delete vehicle
                $vehicle->delete();
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

    //get vehicles - select
    public function getVehiclesSelect()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set vehicles array
            $vehicles_array = [];

            $vehicles = Vehicle::select('id', 'name')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->get();

            //loop through all vehicles
            foreach ($vehicles as $vehicle)
            {
                //add vehicle to vehicles array
                $vehicles_array[$vehicle->id] = $vehicle->name;
            }

            return ['status' => 1, 'data' => $vehicles_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Wages
    |--------------------------------------------------------------------------
    */

    //get wages
    public function getWages()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $wages = Wage::with('wageCountry')
                ->select('id', 'name', 'country', 'price')
                ->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->paginate(30);

            foreach ($wages as $wage)
            {
                //format wage price
                $wage->price = number_format($wage->price, 2, ',', '.');
            }

            return ['status' => 1, 'data' => $wages];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert wage
    public function insertWage($name, $country, $price)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $wage = new Wage;
            $wage->company_id = $company_id;
            $wage->name = $name;
            $wage->country = $country;
            $wage->price = $price;
            $wage->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get wage details
    public function getWageDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $wage = Wage::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')->first();

            //if wage doesn't exist return error status
            if (!$wage)
            {
                return ['status' => 0];
            }

            return ['status' => 1, 'data' => $wage];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update wage
    public function updateWage($id, $name, $country, $price)
    {
        try
        {
            $wage = Wage::find($id);
            $wage->name = $name;
            $wage->country = $country;
            $wage->price = $price;
            $wage->save();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete wage
    public function deleteWage($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $wage = Wage::where('company_id', '=', $company_id)->where('id', '=', $id)->where('deleted', '=', 'F')->first();

            //if wage doesn't exist return error status
            if (!$wage)
            {
                return ['status' => 0];
            }

            //check travel warrants wages
            $travel_warrants_check = WarrantWage::where('wage_id', '=', $id)->count();

            //start transaction
            DB::beginTransaction();

            //if wage is assigned to some travel warrant wage set deleted status to 'T', else delete wage
            if ($travel_warrants_check > 0)
            {
                //set deleted status to 'T'
                $wage->deleted = 'T';
                $wage->save();
            }
            else
            {
                //delete wage
                $wage->delete();
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

    //get wages - select
    public function getWagesSelect()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set wages array
            $wages_array = [];

            $wages = Wage::select('id', 'name')->where('company_id', '=', $company_id)->where('deleted', '=', 'F')->get();

            //loop through all wages
            foreach ($wages as $wage)
            {
                //add wage to wages array
                $wages_array[$wage->id] = $wage->name;
            }

            return ['status' => 1, 'data' => $wages_array];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Travel warrants
    |--------------------------------------------------------------------------
    */

    //get travel warrants
    public function getTravelWarrants()
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $warrants = TravelWarrant::with('user', 'creator')
                ->select('id', 'warrant_id', 'user_id', 'creator_id', DB::raw('DATE_FORMAT(start_date, "%d.%m.%Y.") AS date'),
                    'location')
                ->where('company_id', '=', $company_id)->orderBy('id', 'desc')->paginate(30);

            return ['status' => 1, 'data' => $warrants];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert travel warrant
    public function insertTravelWarrant($creator, $user, $start_date, $end_date, $vehicle, $start_mileage, $end_mileage, $duration,
        $location, $purpose, $description, $advance, $non_costs, $note, $report, $wages, $directions, $costs)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //start transaction
            DB::beginTransaction();

            //call getNextTravelWarrantId method to get next travel warrant id
			$response = $this->getNextTravelWarrantId($company_id);

            //if response status = 0 return error message
            if ($response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //format start and end date
            $start_date = date('Y-m-d', strtotime(trim($start_date)));
            $end_date = date('Y-m-d', strtotime(trim($end_date)));

            $warrant = new TravelWarrant;
            $warrant->company_id = $company_id;
            $warrant->creator_id = $creator;
            $warrant->user_id = $user;
            $warrant->doc_number = $response['doc_number'];
            $warrant->warrant_id = $response['warrant_id'];
            $warrant->warrant_date = DB::raw('NOW()');
            $warrant->start_date = $start_date;
            $warrant->end_date = $end_date;
            $warrant->vehicle_id = $vehicle;
            $warrant->start_mileage = $start_mileage;
            $warrant->end_mileage = $end_mileage;
            $warrant->duration = $duration;
            $warrant->location = $location;
            $warrant->purpose = $purpose;
            $warrant->description = $description;
            $warrant->advance = $advance;
            $warrant->non_costs = $non_costs;
            $warrant->note = $note;
            $warrant->report = $report;
            $warrant->save();

            //if wages array exists insert wages
            if (isset($wages))
            {
                foreach ($wages as $wage)
                {
                    //format wage date, start time and end time
                    $date = date('Y-m-d', strtotime($wage['date']));
                    $start_time = date('Y-m-d H:i:s', strtotime($wage['departure_date'].' '.$wage['departure_time'].':00'));
                    $end_time = date('Y-m-d H:i:s', strtotime($wage['arrival_date'].' '.$wage['arrival_time'].':00'));

                    //insert wage
                    $wage_model = new WarrantWage;
                    $wage_model->warrant_id = $warrant->id;
                    $wage_model->country = $wage['country'];
                    $wage_model->wage_date = $date;
                    $wage_model->wage = $wage['wage'];
                    $wage_model->wage_id = $wage['wage_type'];
                    $wage_model->start_time = $start_time;
                    $wage_model->end_time = $end_time;
                    $wage_model->save();
                }
            }

            //if directions array exists insert directions
            if (isset($directions))
            {
                foreach ($directions as $direction)
                {
                    //format direction date
                    $date = date('Y-m-d', strtotime($direction['date']));

                    //insert direction
                    $direction_model = new Direction;
                    $direction_model->warrant_id = $warrant->id;
                    $direction_model->direction_date = $date;
                    $direction_model->transport_type = $direction['transport_type'];
                    $direction_model->start_location = $direction['start_location'];
                    $direction_model->end_location = $direction['end_location'];
                    $direction_model->distance = $direction['distance'];
                    $direction_model->km_price = $direction['km_price'];
                    $direction_model->save();
                }
            }

            //if costs array exists insert costs
            if (isset($costs))
            {
                foreach ($costs as $cost)
                {
                    //format cost date
                    $date = date('Y-m-d', strtotime($cost['date']));

                    //insert cost
                    $cost_model = new Cost;
                    $cost_model->warrant_id = $warrant->id;
                    $cost_model->cost_date = $date;
                    $cost_model->cost_type = $cost['cost_type'];
                    $cost_model->description = $cost['description'];
                    $cost_model->sum = $cost['sum'];
                    $cost_model->non_costs = $cost['non_costs'];
                    $cost_model->save();
                }
            }

            //commit transaction
            DB::commit();

            //set insert travel warrant flash
            Session::flash('success_message', trans('main.travel_warrant_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get travel warrant details
    public function getTravelWarrantDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $warrant = TravelWarrant::with('creator', 'user', 'vehicle')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if warrant doesn't exist return error status
            if (!$warrant)
            {
                return ['status' => 0];
            }

            //format start and end date
            $warrant->start_date = date('d.m.Y.', strtotime($warrant->start_date));
            $warrant->end_date = date('d.m.Y.', strtotime($warrant->end_date));

            //get wages
            $wages = WarrantWage::with('wageWage', 'wageCountry')
                ->select('id', 'wage_id', DB::raw('DATE_FORMAT(wage_date, "%d.%m.%Y.") AS date'), 'country',
                    DB::raw('DATE_FORMAT(start_time, "%d.%m.%Y.") AS departure_date'),
                    DB::raw('DATE_FORMAT(start_time, "%H:%i") AS departure_time'),
                    DB::raw('DATE_FORMAT(end_time, "%d.%m.%Y.") AS arrival_date'),
                    DB::raw('DATE_FORMAT(end_time, "%H:%i") AS arrival_time'), 'wage',
                    DB::raw('time_to_sec(TIMEDIFF(end_time, start_time)) / 3600 AS hours'))
                ->where('warrant_id', '=', $id)->get();

            //get directions
            $directions = Direction::select('id', DB::raw('DATE_FORMAT(direction_date, "%d.%m.%Y.") AS date'),
                'transport_type', 'start_location', 'end_location', 'distance', 'km_price')
                ->where('warrant_id', '=', $id)->get();

            //get costs
            $costs = Cost::select('id', DB::raw('DATE_FORMAT(cost_date, "%d.%m.%Y.") AS date'), 'cost_type',
                'description', 'sum', 'non_costs')
                ->where('warrant_id', '=', $id)->get();

            //add travel warrant details to warrant array
            $warrant_array['warrant'] = $warrant;
            $warrant_array['wages'] = $wages;
            $warrant_array['directions'] = $directions;
            $warrant_array['costs'] = $costs;

            return ['status' => 1, 'data' => $warrant_array];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //update travel warrant
    public function updateTravelWarrant($id, $creator, $user, $start_date, $end_date, $vehicle, $start_mileage, $end_mileage,
        $duration, $location, $purpose, $description, $advance, $non_costs, $note, $report, $wages, $directions, $costs)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            //format start and end date
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));

            $warrant = TravelWarrant::find($id);
            $warrant->creator_id = $creator;
            $warrant->user_id = $user;
            $warrant->start_date = $start_date;
            $warrant->end_date = $end_date;
            $warrant->vehicle_id = $vehicle;
            $warrant->start_mileage = $start_mileage;
            $warrant->end_mileage = $end_mileage;
            $warrant->duration = $duration;
            $warrant->location = $location;
            $warrant->purpose = $purpose;
            $warrant->description = $description;
            $warrant->advance = $advance;
            $warrant->non_costs = $non_costs;
            $warrant->note = $note;
            $warrant->report = $report;
            $warrant->save();

            //if wages array exists insert wages
            if (isset($wages))
            {
                foreach ($wages as $wage)
                {
                    //format wage date, start time and end time
                    $date = date('Y-m-d', strtotime($wage['date']));
                    $start_time = date('Y-m-d H:i:s', strtotime($wage['departure_date'].' '.$wage['departure_time'].':00'));
                    $end_time = date('Y-m-d H:i:s', strtotime($wage['arrival_date'].' '.$wage['arrival_time'].':00'));

                    if ($id && $wage['id'])
                    {
                        $wage_model = WarrantWage::where('warrant_id', '=', $id)->where('id', '=', $wage['id'])->first();

                        //if wage doesn't exist return error message
                        if (!$wage_model)
                        {
                            return ['status' => 0, 'error' => trans('errors.error')];
                        }

                        //update wage
                        $wage_model->country = $wage['country'];
                        $wage_model->wage_date = $date;
                        $wage_model->wage = $wage['wage'];
                        $wage_model->wage_id = $wage['wage_type'];
                        $wage_model->start_time = $start_time;
                        $wage_model->end_time = $end_time;
                        $wage_model->save();
                    }
                    else
                    {
                        //insert wage
                        $wage_model = new WarrantWage;
                        $wage_model->warrant_id = $warrant->id;
                        $wage_model->country = $wage['country'];
                        $wage_model->wage_date = $date;
                        $wage_model->wage = $wage['wage'];
                        $wage_model->wage_id = $wage['wage_type'];
                        $wage_model->start_time = $start_time;
                        $wage_model->end_time = $end_time;
                        $wage_model->save();
                    }
                }
            }

            //if directions array exists insert directions
            if (isset($directions))
            {
                foreach ($directions as $direction)
                {
                    //format direction date
                    $date = date('Y-m-d', strtotime($direction['date']));

                    if ($id && $direction['id'])
                    {
                        $direction_model = Direction::where('warrant_id', '=', $id)->where('id', '=', $direction['id'])->first();

                        //if direction doesn't exist return error message
                        if (!$direction_model)
                        {
                            return ['status' => 0, 'error' => trans('errors.error')];
                        }

                        //update direction
                        $direction_model->direction_date = $date;
                        $direction_model->transport_type = $direction['transport_type'];
                        $direction_model->start_location = $direction['start_location'];
                        $direction_model->end_location = $direction['end_location'];
                        $direction_model->distance = $direction['distance'];
                        $direction_model->km_price = $direction['km_price'];
                        $direction_model->save();
                    }
                    else
                    {
                        //insert direction
                        $direction_model = new Direction;
                        $direction_model->warrant_id = $warrant->id;
                        $direction_model->direction_date = $date;
                        $direction_model->transport_type = $direction['transport_type'];
                        $direction_model->start_location = $direction['start_location'];
                        $direction_model->end_location = $direction['end_location'];
                        $direction_model->distance = $direction['distance'];
                        $direction_model->km_price = $direction['km_price'];
                        $direction_model->save();
                    }
                }
            }

            //if costs array exists insert costs
            if (isset($costs))
            {
                foreach ($costs as $cost)
                {
                    //format cost date
                    $date = date('Y-m-d', strtotime($cost['date']));

                    if ($id && $cost['id'])
                    {
                        $cost_model = Cost::where('warrant_id', '=', $id)->where('id', '=', $cost['id'])->first();

                        //if cost doesn't exist return error message
                        if (!$cost_model)
                        {
                            return ['status' => 0, 'error' => trans('errors.error')];
                        }

                        //update cost
                        $cost_model->cost_date = $date;
                        $cost_model->cost_type = $cost['cost_type'];
                        $cost_model->description = $cost['description'];
                        $cost_model->sum = $cost['sum'];
                        $cost_model->non_costs = $cost['non_costs'];
                        $cost_model->save();
                    }
                    else
                    {
                        //insert cost
                        $cost_model = new Cost;
                        $cost_model->warrant_id = $warrant->id;
                        $cost_model->cost_date = $date;
                        $cost_model->cost_type = $cost['cost_type'];
                        $cost_model->description = $cost['description'];
                        $cost_model->sum = $cost['sum'];
                        $cost_model->non_costs = $cost['non_costs'];
                        $cost_model->save();
                    }
                }
            }

            //commit transaction
            DB::commit();

            //set update travel warrant flash
            Session::flash('success_message', trans('main.travel_warrant_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete travel warrant
    public function deleteTravelWarrant($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $warrant = TravelWarrant::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if warrant doesn't exist return error status
            if (!$warrant)
            {
                return ['status' => 0];
            }

            //start transaction
            DB::beginTransaction();

            $warrant->delete();

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //copy travel warrant
    public function copyTravelWarrant($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $warrant = TravelWarrant::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if warrant doesn't exist return error status
            if (!$warrant)
            {
                return ['status' => 0];
            }

            //get wages
            $wages = WarrantWage::where('warrant_id', '=', $id)->get();

            //get directions
            $directions = Direction::where('warrant_id', '=', $id)->get();

            //get costs
            $costs = Cost::where('warrant_id', '=', $id)->get();

            //start transaction
            DB::beginTransaction();

            //call getNextTravelWarrantId method to get next travel warrant id
            $response = $this->getNextTravelWarrantId($company_id);

            //if response status = 0 return error message
            if ($response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $warrant_model = new TravelWarrant;
            $warrant_model->company_id = $company_id;
            $warrant_model->creator_id = $warrant->creator_id;
            $warrant_model->user_id = $warrant->user_id;
            $warrant_model->doc_number = $response['doc_number'];
            $warrant_model->warrant_id = $response['warrant_id'];
            $warrant_model->warrant_date = DB::raw('NOW()');
            $warrant_model->start_date = $warrant->start_date;
            $warrant_model->end_date = $warrant->end_date;
            $warrant_model->vehicle_id = $warrant->vehicle_id;
            $warrant_model->start_mileage = $warrant->start_mileage;
            $warrant_model->end_mileage = $warrant->end_mileage;
            $warrant_model->duration = $warrant->duration;
            $warrant_model->location = $warrant->location;
            $warrant_model->purpose = $warrant->purpose;
            $warrant_model->description = $warrant->description;
            $warrant_model->advance = $warrant->advance;
            $warrant_model->non_costs = $warrant->non_costs;
            $warrant_model->note = $warrant->note;
            $warrant_model->report = $warrant->report;
            $warrant_model->save();

            foreach ($wages as $wage)
            {
                //insert wage
                $wage_model = new WarrantWage;
                $wage_model->warrant_id = $warrant_model->id;
                $wage_model->country = $wage->country;
                $wage_model->wage_date = $wage->wage_date;
                $wage_model->wage = $wage->wage;
                $wage_model->wage_id = $wage->wage_id;
                $wage_model->start_time = $wage->start_time;
                $wage_model->end_time = $wage->end_time;
                $wage_model->save();
            }

            foreach ($directions as $direction)
            {
                //insert direction
                $direction_model = new Direction;
                $direction_model->warrant_id = $warrant_model->id;
                $direction_model->direction_date = $direction->direction_date;
                $direction_model->transport_type = $direction->transport_type;
                $direction_model->start_location = $direction->start_location;
                $direction_model->end_location = $direction->end_location;
                $direction_model->distance = $direction->distance;
                $direction_model->km_price = $direction->km_price;
                $direction_model->save();
            }

            foreach ($costs as $cost)
            {
                //insert cost
                $cost_model = new Cost;
                $cost_model->warrant_id = $warrant_model->id;
                $cost_model->cost_date = $cost->cost_date;
                $cost_model->cost_type = $cost->cost_type;
                $cost_model->description = $cost->description;
                $cost_model->sum = $cost->sum;
                $cost_model->non_costs = $cost->non_costs;
                $cost_model->save();
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

    //get next travel warrant id
    private function getNextTravelWarrantId($company_id)
    {
        try
        {
            //get current year
            $year = date('Y');

            //set default doc number
            $doc_number = 1;

            //get max doc number
            $max_doc_number = TravelWarrant::where('company_id', '=', $company_id)
                ->whereRaw('YEAR(warrant_date) = ?', [$year])->max('doc_number');

            if ($max_doc_number)
            {
                //set doc_number
                $doc_number = $max_doc_number + 1;
            }

            //set travel warrant id
            $travel_warrant_id = $doc_number."/".$year;

            return ['status' => 1, 'warrant_id' => $travel_warrant_id, 'doc_number' => $doc_number];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //calculate distance
    public function calculateDistance($start_location, $end_location)
    {
        try
        {
            $url = $start_location.'&destinations='.$end_location;

            $distance = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$url),
                true);

            //check maps api status
            if ($distance['status'] != 'OK')
            {
                return ['status' => 0, 'error' => trans('errors.distance_error')];
            }

            $km = $distance['rows'][0]['elements'][0]['distance']['value'];
            $km = number_format($km / 1000, 0);

            return ['status' => 1, 'data' => $km];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete item
    public function deleteItem($warrant_id, $item_type, $item_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $warrant = TravelWarrant::where('company_id', '=', $company_id)->where('id', '=', $warrant_id)->first();

            //set item types array
            $item_types_array = ['wage' => WarrantWage::where('warrant_id', '=', $warrant_id),
                'direction' => Direction::where('warrant_id', '=', $warrant_id),
                'cost' => Cost::where('warrant_id', '=', $warrant_id)];

            $item = $item_types_array[$item_type]->where('id', '=', $item_id);

            $item = $item->first();

            //if item type is not correct or warrant doesn't exist or item doesn't exist return error status
            if (!array_key_exists($item_type, $item_types_array) || !$warrant || !$item)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $item->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //travel warrant pdf data
    public function TravelWarrantPdfData($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getTravelWarrantDetails method to get travel warrant pdf data
            $data = $this->getTravelWarrantDetails($id);

            //if response status = 0 return error message
            if ($data['status'] == 0)
            {
                return view('errors.500');
            }

            $company = Company::find($company_id);

            $wage_sum = 0;
            $direction_sum = 0;
            $cost_sum = 0;

            foreach ($data['data']['wages'] as $wage)
            {
                $wage_sum += $wage->wage * $wage->wageWage->price;
            }

            foreach ($data['data']['directions'] as $direction)
            {
                $direction_sum += $direction->distance * $direction->km_price;
            }

            foreach ($data['data']['costs'] as $cost)
            {
                $cost_sum += $cost->sum - $cost->non_costs;
            }

            $data['data']['wage_sum'] = $wage_sum;
            $data['data']['direction_sum'] = $direction_sum;
            $data['data']['cost_sum'] = $cost_sum;
            $data['data']['warrant']->report_date = date('d.m.Y.', strtotime('+3 day',
                strtotime($data['data']['warrant']->end_date)));

            return ['status' => 1, 'warrant' => $data['data'], 'company' => $company];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
