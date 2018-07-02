<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustomValidator\Validator as CustomValidator;
use App\Repositories\TaxGroupRepository;

class TaxGroupController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new TaxGroupRepository;
    }

    //get tax groups
    public function getTaxGroups()
    {
        //call getTaxGroups method from TaxGroupRepository to get tax groups
        $groups = $this->repo->getTaxGroups();

        //if response status = '0' show error page
        if ($groups['status'] == 0)
        {
            return view('errors.500');
        }

        return view('app.taxGroups.list', ['groups' => $groups['data']]);
    }

    //add tax group
    public function addTaxGroup()
    {
        return view('app.taxGroups.addGroup');
    }

    //insert tax group
    public function insertTaxGroup(Request $request)
    {
        $name = $request->name;
        $note = $request->note;

        //validate form inputs
        $validator = CustomValidator::taxGroups();

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call insertTaxGroup method from TaxGroupRepository to insert tax group
        $response = $this->repo->insertTaxGroup($name, $note);

        return response()->json($response);
    }

    //edit tax group
    public function editTaxGroup($id)
    {
        //call getTaxGroupDetails method from TaxGroupRepository to get tax group details
        $group = $this->repo->getTaxGroupDetails($id);

        //if response status = '0' return error message
        if ($group['status'] == 0)
        {
            return redirect()->route('GetTaxGroups')->with('error_message', trans('errors.error'));
        }

        return view('app.taxGroups.editGroup', ['group' => $group['group'], 'taxes' => $group['taxes']]);
    }

    //update tax group
    public function updateTaxGroup(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $note = $request->note;

        //call getCompanyId method from UserRepository to get company id
        $company_id = $this->repo->getCompanyId();

        //validate form inputs
        $validator = CustomValidator::taxGroups($company_id, $id);

        //if form input is not correct return error message
        if (!$validator['status'])
        {
            return response()->json($validator);
        }

        //call updateTaxGroup method from TaxGroupRepository to update tax group
        $response = $this->repo->updateTaxGroup($id, $name, $note);

        return response()->json($response);
    }

    //delete tax group
    public function deleteTaxGroup($id)
    {
        //call deleteTaxGroup method from TaxGroupRepository to delete tax group
        $response = $this->repo->deleteTaxGroup($id);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('GetTaxGroups')->with('error_message', trans('errors.error'));
        }

        return redirect()->route('GetTaxGroups')->with('success_message', trans('main.tax_group_delete'));
    }
}
