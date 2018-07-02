<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\PaymentSlip;
use App\PayoutSlip;
use App\PayoutSlipItem;
use App\RegisterReport;
use App\Invoice;
use App\Company;

class RegisterReportRepository extends UserRepository
{
    /*
    |--------------------------------------------------------------------------
    | Payment slips
    |--------------------------------------------------------------------------
    */

    //get payment slips
    public function getPaymentSlips($office_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $slips = PaymentSlip::select('id', 'slip_id', DB::raw('DATE_FORMAT(slip_date, "%d.%m.%Y.") AS date'), 'item',
                'description', 'invoice_id')->where('company_id', '=', $company_id);

            if ($office_id)
            {
                 $slips->where('office_id', '=', $office_id);
            }

            $slips = $slips->orderBy('id', 'desc')->paginate(30);

            return ['status' => 1, 'data' => $slips];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert payment slip
    public function insertPaymentSlip($is_invoice, $payer, $office_id, $location, $item, $description, $sum, $invoice_id,
        $client)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //start transaction
            DB::beginTransaction();

            //call getNextPaymentSlipId method to get next payment slip id
            $response = $this->getNextPaymentSlipId($company_id, $office_id);

            //if response status = 0 return error message
            if ($response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $slip = new PaymentSlip;
            $slip->company_id = $company_id;
            $slip->doc_number = $response['doc_number'];
            $slip->slip_id = $response['slip_id'];
            $slip->slip_date = DB::raw('NOW()');
            $slip->payer = $payer;
            $slip->office_id = $office_id;
            $slip->location = $location;
            $slip->item = $item;
            $slip->description = $description;
            $slip->sum = $sum;
            $slip->invoice_id = $invoice_id;
            $slip->client_id = $client;
            $slip->save();

            //commit transaction
            DB::commit();

            if ($is_invoice == 'F')
            {
                //set insert payment slip flash
                Session::flash('success_message', trans('main.payment_slip_insert'));
            }

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete payment slip
    public function deletePaymentSlip($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $slip = PaymentSlip::where('company_id', '=', $company_id)->where('id', '=', $id)->where('invoice_id', '=', 0)
                ->first();

            //if slip doesn't exist return error status
            if (!$slip)
            {
                return ['status' => 0];
            }

            $slip->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get next payment slip id
    private function getNextPaymentSlipId($company_id, $office_id)
    {
        try
        {
            //get current year
            $year = date('Y');

            //set default doc number
            $doc_number = 1;

            //get max doc number
            $max_doc_number = PaymentSlip::where('company_id', '=', $company_id)->where('office_id', '=', $office_id)
                ->whereRaw('YEAR(slip_date) = ?', [$year])->max('doc_number');

            if ($max_doc_number)
            {
                //set doc_number
                $doc_number = $max_doc_number + 1;
            }

            //set payment slip id
            $payment_slip_id = $year.'-'.sprintf("%06d", $doc_number);

            return ['status' => 1, 'slip_id' => $payment_slip_id, 'doc_number' => $doc_number];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //payment slip pdf data
    public function paymentSlipPdfData($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $payment_slip = PaymentSlip::with('client', 'office')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)
                ->first();

            //if payment slip doesn't exist return error status
            if (!$payment_slip)
            {
                return ['status' => 0];
            }

            $company = Company::find($company_id);

            $office_name = '';
            $payer_name = $payment_slip->payer;

            if ($payment_slip->office)
            {
                $office_name = $payment_slip->office->name;
            }

            if ($payment_slip->client_id)
            {
                $payer_name = $payment_slip->client->name;
            }

            $payment_slip->office_name = $office_name;
            $payment_slip->payer_name = $payer_name;

            return ['status' => 1, 'payment_slip' => $payment_slip, 'company' => $company];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Payout slips
    |--------------------------------------------------------------------------
    */

    //get payout slips
    public function getPayoutSlips($office_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $slips = PayoutSlip::with('employee')
                ->select('id', 'slip_id', DB::raw('DATE_FORMAT(slip_date, "%d.%m.%Y.") AS date'), 'employee_id', 'note')
                ->where('company_id', '=', $company_id);

            if ($office_id)
            {
                $slips->where('office_id', '=', $office_id);
            }

            $slips = $slips->orderBy('id', 'desc')->paginate(30);

            return ['status' => 1, 'data' => $slips];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert payout slip
    public function insertPayoutSlip($employee_id, $office_id, $income, $location, $note, $items, $reversed = false, $sum = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //start transaction
            DB::beginTransaction();

            //call getNextPayoutSlipId method to get next payout slip id
            $response = $this->getNextPayoutSlipId($company_id, $office_id);

            //if response status = 0 return error message
            if ($response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $slip = new PayoutSlip;
            $slip->company_id = $company_id;
            $slip->doc_number = $response['doc_number'];
            $slip->slip_id = $response['slip_id'];
            $slip->slip_date = DB::raw('NOW()');
            $slip->office_id = $office_id;
            $slip->location = $location;
            $slip->employee_id = $employee_id;

            if ($reversed)
            {
                $slip->note = 'stornirani račun';
            }
            else
            {
                $slip->note = $note;
            }

            $slip->income = $income;
            $slip->save();

            if (!$reversed)
            {
                foreach ($items as $item)
                {
                    $item_model = new PayoutSlipItem;
                    $item_model->slip_id = $slip->id;
                    $item_model->item = $item['item'];
                    $item_model->description = $item['description'];
                    $item_model->sum = $item['sum'];
                    $item_model->save();
                }
            }
            else
            {
                $item_model = new PayoutSlipItem;
                $item_model->slip_id = $slip->id;
                $item_model->item = 'stornirani račun';
                $item_model->description = $note;
                $item_model->sum = $sum;
                $item_model->save();
            }

            //commit transaction
            DB::commit();

            if (!$reversed)
            {
                //set insert payout slip flash
                Session::flash('success_message', trans('main.payout_slip_insert'));
            }

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get payout slip details
    public function getPayoutSlipDetails($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $slip = PayoutSlip::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if slip doesn't exist return error status
            if (!$slip)
            {
                return ['status' => 0];
            }

            //get items
            $items = PayoutSlipItem::select('id', 'item', 'description', 'sum')
                ->where('slip_id', '=', $id)->get();

            return ['status' => 1, 'slip' => $slip, 'items' => $items];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //update payout slip
    public function updatePayoutSlip($id, $employee_id, $income, $location, $note, $items)
    {
        try
        {
            //start transaction
            DB::beginTransaction();

            $slip = PayoutSlip::find($id);
            $slip->location = $location;
            $slip->employee_id = $employee_id;
            $slip->note = $note;
            $slip->income = $income;
            $slip->save();

            foreach ($items as $item)
            {
                if ($id && $item['id'])
                {
                    $item_model = PayoutSlipItem::where('slip_id', '=', $id)->where('id', '=', $item['id'])->first();

                    //if item doesn't exist return error message
                    if (!$item_model)
                    {
                        return ['status' => 0, 'error' => trans('errors.error')];
                    }

                    //update item
                    $item_model->item = $item['item'];
                    $item_model->description = $item['description'];
                    $item_model->sum = $item['sum'];
                    $item_model->save();
                }
                else
                {
                    //insert item
                    $item_model = new PayoutSlipItem;
                    $item_model->slip_id = $slip->id;
                    $item_model->item = $item['item'];
                    $item_model->description = $item['description'];
                    $item_model->sum = $item['sum'];
                    $item_model->save();
                }
            }

            //commit transaction
            DB::commit();

            //set update payout slip flash
            Session::flash('success_message', trans('main.payout_slip_update'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //delete payout slip
    public function deletePayoutSlip($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $slip = PayoutSlip::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if slip doesn't exist return error status
            if (!$slip)
            {
                return ['status' => 0];
            }

            $slip->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //delete payout slip item
    public function deletePayoutSlipItem($slip_id, $item_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $slip = PayoutSlip::where('company_id', '=', $company_id)->where('id', '=', $slip_id)->first();

            $item = PayoutSlipItem::where('slip_id', '=', $slip_id)->where('id', '=', $item_id)->first();

            //if slip doesn't exist or item doesn't exist return error status
            if (!$slip || !$item)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            $count_items = PayoutSlipItem::where('slip_id', '=', $slip_id)->count();

            //if slip has only one item return warning message
            if ($count_items == 1)
            {
                return ['status' => 2, 'warning' => trans('errors.delete_item')];
            }

            $item->delete();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get next payout slip id
    private function getNextPayoutSlipId($company_id, $office_id)
    {
        try
        {
            //get current year
            $year = date('Y');

            //set default doc number
            $doc_number = 1;

            //get max doc number
            $max_doc_number = PayoutSlip::where('company_id', '=', $company_id)->where('office_id', '=', $office_id)
                ->whereRaw('YEAR(slip_date) = ?', [$year])->max('doc_number');

            if ($max_doc_number)
            {
                //set doc_number
                $doc_number = $max_doc_number + 1;
            }

            //set payout slip id
            $payout_slip_id = $year.'-'.sprintf("%06d", $doc_number);

            return ['status' => 1, 'slip_id' => $payout_slip_id, 'doc_number' => $doc_number];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //payout slip pdf data
    public function payoutSlipPdfData($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $payout_slip = PayoutSlip::with('office', 'employee')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)
                ->first();

            //if payout slip doesn't exist return error status
            if (!$payout_slip)
            {
                return ['status' => 0];
            }

            //get items
            $items = PayoutSlipItem::select('item', 'description', 'sum')
                ->where('slip_id', '=', $id)->get();

            $company = Company::find($company_id);

            $office_name = '';
            $employee_name = '';

            if ($payout_slip->office)
            {
                $office_name = $payout_slip->office->name;
            }

            if ($payout_slip->employee)
            {
                $employee_name = $payout_slip->employee->first_name.' '.$payout_slip->employee->last_name;
            }

            $payout_slip->office_name = $office_name;
            $payout_slip->employee_name = $employee_name;

            /*
            |--------------------------------------------------------------------------
            |--------------------------------------------------------------------------
            */

            //set items array
            $items_array = [];

            $items_sum = 0;
            $i = 1;

            foreach ($items as $item)
            {
                $items_sum += $item->sum;

                //add item to items array
                $items_array[] = ['rb' => $i, 'item' => $item->item, 'description' => $item->description, 'sum' => $item->sum];

                $i++;
            }

            return ['status' => 1, 'payout_slip' => $payout_slip, 'company' => $company, 'items' => $items_array,
                'items_sum' => $items_sum];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Register reports
    |--------------------------------------------------------------------------
    */

    //get register reports
    public function getRegisterReports($office_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $reports = RegisterReport::select('id', 'report_id', DB::raw('DATE_FORMAT(start_date, "%d.%m.%Y.") AS start_date'),
                DB::raw('DATE_FORMAT(end_date, "%d.%m.%Y.") AS end_date'))
                ->where('company_id', '=', $company_id);

            if ($office_id)
            {
                $reports->where('office_id', '=', $office_id);
            }

            $reports = $reports->orderBy('id', 'desc')->paginate(30);

            //set counter
            $counter = 1;

            foreach ($reports as $report)
            {
                //set default report message variable
                $report->report_message = 'T';

                if ($counter == 1)
                {
                    //count reports with id greater than current id
                    $check_reports = RegisterReport::where('company_id', '=', $company_id)->where('office_id', '=', $office_id)
                        ->where('id', '>', $report->id)->count();

                    //if there are no reports with id greater than current id set report message to 'F'
                    if (!$check_reports)
                    {
                        $report->report_message = 'F';
                    }
                }

                $counter++;
            }

            return ['status' => 1, 'data' => $reports];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //insert register report
    public function insertRegisterReport($start_date, $end_date, $office_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //format start and end date
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));

            //start transaction
            DB::beginTransaction();

            //call getNextRegisterReportId method to get next register report id
			$response = $this->getNextRegisterReportId($company_id, $start_date, $office_id);

            //if response status = 0 return error message
            if ($response['status'] == 0)
            {
                return ['status' => 0, 'error' => trans('errors.error')];
            }

            //set report sum
            $report_sum = 0;

            //get last report sum
            $last_sum = RegisterReport::select('sum')->where('company_id', '=', $company_id)->where('office_id', '=', $office_id)
                ->orderBy('id', 'desc')->first();

            if ($last_sum)
            {
                $report_sum = $last_sum->sum;
            }

            //get payment slips
            $payment_slips = PaymentSlip::select('sum', 'invoice_id')->where('company_id', '=', $company_id)
                ->where('office_id', '=', $office_id)->where('slip_date', '>=', $start_date)
                ->where('slip_date', '<=', $end_date)->get();

            foreach ($payment_slips as $slip)
            {
                //if invoice exists check payment type
                if ($slip->invoice_id)
                {
                    $invoice = Invoice::select('payment_type_id')->where('id', '=', $slip->invoice_id)->first();

                    //if payment type = 'Cache' add slip sum to payment slips sum
                    if ($invoice->payment_type_id == 1)
                    {
                        $report_sum += $slip->sum;
                    }
                }
                else
                {
                    $report_sum += $slip->sum;
                }
            }

            //get payout slips
            $payout_slips = PayoutSlip::select('id')->where('company_id', '=', $company_id)
                ->where('office_id', '=', $office_id)->where('slip_date', '>=', $start_date)
                ->where('slip_date', '<=', $end_date)->get();

            foreach ($payout_slips as $slip)
            {
                //get slip items
                $items = PayoutSlipItem::select('sum')->where('slip_id', '=', $slip->id)->get();

                foreach ($items as $item)
                {
                    $report_sum -= $item->sum;
                }
            }

            $report = new RegisterReport;
            $report->company_id = $company_id;
            $report->doc_number = $response['doc_number'];
            $report->report_id = $response['report_id'];
            $report->office_id = $office_id;
            $report->start_date = $start_date;
            $report->end_date = $end_date;
            $report->sum = $report_sum;
            $report->save();

            //commit transaction
            DB::commit();

            //set insert register report flash
            Session::flash('success_message', trans('main.register_report_insert'));

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0, 'error' => trans('errors.error')];
        }
    }

    //get register report details
    public function getRegisterReportDetails($id, $return_items = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $report = RegisterReport::with('office')
                ->where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if report doesn't exist return error status
            if (!$report)
            {
                return ['status' => 0];
            }

            //set report array
            $report_array = [];

            //set items array
            $items_array = [];

            //set payment slips sum
            $payment_slips_sum = 0;

            //set payout slips sum
            $payout_slips_sum = 0;

            //set previous report sum
            $previous_report_sum = 0;

            //set default payment type
            $payment_type = 'Gotovina';

            //get previous report
            $previous_report = RegisterReport::select('sum')->where('company_id', '=', $company_id)
                ->where('office_id', '=', $report->office_id)->where('id', '<', $id)->orderBy('id', 'desc')->first();

            //if previous report exists set previous sum
            if ($previous_report)
            {
                $previous_report_sum = $previous_report->sum;
            }

            //get payment slips
            $payment_slips = PaymentSlip::with('office', 'client')
                ->where('company_id', '=', $company_id)->where('office_id', '=', $report->office_id)
                ->where('slip_date', '>=', $report->start_date)->where('slip_date', '<=', $report->end_date)->get();

            foreach ($payment_slips as $slip)
            {
                //set client name
                $client_name = $slip->payer;

                //if invoice exists check payment type
                if ($slip->invoice_id)
                {
                    $invoice = Invoice::with('paymentType')
                        ->select('payment_type_id')->where('id', '=', $slip->invoice_id)->first();

                    //if payment type != 'Cache' get payment type name
                    if ($invoice->payment_type_id != 1)
                    {
                        $payment_type = $invoice->paymentType->name;
                    }
                    else
                    {
                        $payment_slips_sum += $slip->sum;
                    }
                }
                else
                {
                    $payment_slips_sum += $slip->sum;
                }

                //if client exists set client name
                if ($slip->client_id)
                {
                    $client_name = $slip->client->name;
                }

                //add slip to items array
                $items_array[] = ['payment_slip' => 'T', 'date' => $slip->slip_date,
                    'list_date'=> date('d.m.Y.', strtotime($slip->slip_date)), 'document_id' => $slip->slip_id,
                    'client' => $client_name, 'description' => $slip->description, 'payment_type' => $payment_type,
                    'sum' => number_format($slip->sum, '2', ',', '.')];
            }

            //get payout slips
            $payout_slips = PayoutSlip::with('office', 'employee')
                ->where('company_id', '=', $company_id)->where('office_id', '=', $report->office_id)
                ->where('slip_date', '>=', $report->start_date)->where('slip_date', '<=', $report->end_date)->get();

            foreach ($payout_slips as $slip)
            {
                //set employee name
                $employee_name = '';

                //if employee exists set employee name
                if ($slip->employee)
                {
                    $employee_name = $slip->employee->first_name.' '.$slip->employee->last_name;
                }

                //get slip items
                $items = PayoutSlipItem::select('description', 'sum')->where('slip_id', '=', $slip->id)->get();

                foreach ($items as $item)
                {
                    $payout_slips_sum += $item->sum;

                    //add slip item to items array
                    $items_array[] = ['payment_slip' => 'F', 'date' => $slip->slip_date,
                        'list_date'=> date('d.m.Y.', strtotime($slip->slip_date)), 'document_id' => $slip->slip_id,
                        'client' => $employee_name, 'description' => $item->description, 'payment_type' => '',
                        'sum' => number_format($item->sum, '2', ',', '.')];
                }
            }

            foreach ($items_array as $key => $single_item)
            {
                $payment_slip_sort[$key] = $single_item['payment_slip'];
                $date_sort[$key] = $single_item['date'];
                $list_date_sort[$key] = $single_item['list_date'];
                $document_id_sort[$key] = $single_item['document_id'];
                $client_sort[$key] = $single_item['client'];
                $description_sort[$key] = $single_item['description'];
                $payment_type_sort[$key] = $single_item['payment_type'];
                $sum_sort[$key] = $single_item['sum'];
            }

            if (count($items_array) > 1)
            {
                //sort items
                array_multisort($date_sort, SORT_ASC, $items_array);
            }

            //add report data to report array
            $report_array['report'] = $report;
            $report_array['start_date'] = date('d.m.Y.', strtotime($report->start_date));
            $report_array['end_date'] = date('d.m.Y.', strtotime($report->end_date));
            $report_array['old_balance'] = number_format($previous_report_sum, 2, ',', '.');
            $report_array['income'] = number_format($payment_slips_sum, 2, ',', '.');
            $report_array['total'] = number_format($previous_report_sum + $payment_slips_sum, 2, ',', '.');
            $report_array['expense'] = number_format($payout_slips_sum, 2, ',', '.');
            $report_array['new_balance'] = number_format($previous_report_sum + $payment_slips_sum -
                $payout_slips_sum, 2, ',', '.');

            //add items array to report array
            $report_array['items'] = $items_array;

            if ($return_items)
            {
                //add payment slips and payout slips to report array
                $report_array['payment_slips'] = $payment_slips;
                $report_array['payout_slips'] = $payout_slips;
            }

            return ['status' => 1, 'data' => $report_array];
        }
        catch (Exception $exp)
        {
            return ['status' => 0];
        }
    }

    //delete register report
    public function deleteRegisterReport($id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            $report = RegisterReport::where('company_id', '=', $company_id)->where('id', '=', $id)->first();

            //if report doesn't exist return error status
            if (!$report)
            {
                return ['status' => 0];
            }

            //start transaction
            DB::beginTransaction();

            //delete all reports with given id and greater
            RegisterReport::where('company_id', '=', $company_id)->where('id', '>=', $id)->delete();

            //commit transaction
            DB::commit();

            return ['status' => 1];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get next register report id
    private function getNextRegisterReportId($company_id, $start_date, $office_id)
    {
        try
        {
            //set year
            $year = date('Y', strtotime($start_date));

            //set default doc number
            $doc_number = 1;

            //get max doc number
            $max_doc_number = RegisterReport::where('company_id', '=', $company_id)->where('office_id', '=', $office_id)
                ->whereRaw('YEAR(start_date) = ?', [$year])->max('doc_number');

            if ($max_doc_number)
            {
                //set doc_number
                $doc_number = $max_doc_number + 1;
            }

            //set report id
            $report_id = sprintf("%06d", $doc_number);

            return ['status' => 1, 'report_id' => $report_id, 'doc_number' => $doc_number];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //get current register sum
    public function getCurrentRegisterSum($office_id)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //set default register sum
            $register_sum = 0;

            //set default end date
            $end_date = null;

            if ($office_id)
            {
                //get previous report
                $previous_report = RegisterReport::select('sum', 'end_date')
                    ->where('company_id', '=', $company_id)->where('office_id', '=', $office_id)->orderBy('id', 'desc')->first();

                //if previous report exists set sum and end date
                if ($previous_report)
                {
                    $register_sum = $previous_report->sum;
                    $end_date = $previous_report->end_date;
                }

                //get payment slips
                $payment_slips = PaymentSlip::select('sum', 'invoice_id')->where('company_id', '=', $company_id)
                    ->where('office_id', '=', $office_id);

                if ($end_date)
                {
                    $payment_slips->where('slip_date', '>', $end_date);
                }

                $payment_slips = $payment_slips->get();

                foreach ($payment_slips as $slip)
                {
                    //if invoice exists check payment type
                    if ($slip->invoice_id)
                    {
                        $invoice = Invoice::select('payment_type_id')->where('id', '=', $slip->invoice_id)->first();

                        //if payment type = 'Cache' add slip sum to register sum
                        if ($invoice->payment_type_id == 1)
                        {
                            $register_sum += $slip->sum;
                        }
                    }
                    else
                    {
                        $register_sum += $slip->sum;
                    }
                }

                //get payout slips
                $payout_slips = PayoutSlip::select('id')->where('company_id', '=', $company_id)
                    ->where('office_id', '=', $office_id);

                if ($end_date)
                {
                    $payout_slips->where('slip_date', '>', $end_date);
                }

                $payout_slips = $payout_slips->get();

                foreach ($payout_slips as $slip)
                {
                    //get slip items
                    $items = PayoutSlipItem::select('sum')->where('slip_id', '=', $slip->id)->get();

                    foreach ($items as $item)
                    {
                        $register_sum -= $item->sum;
                    }
                }
            }

            return ['status' => 1, 'data' => number_format($register_sum, 2, ',', '.')];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }

    //register report pdf data
    public function registerReportPdfData($id, $items = false)
    {
        try
        {
            //call getCompanyId method from UserRepository to get company id
            $company_id = $this->getCompanyId();

            //call getRegisterReportDetails method to get register report pdf data
            $data = $this->getRegisterReportDetails($id, $items);

            //if response status = 0 return error message
            if ($data['status'] == 0)
            {
                return view('errors.500');
            }

            $company = Company::find($company_id);

            if ($items)
            {
                foreach ($data['data']['payment_slips'] as $payment_slip)
                {
                    $office_name = '';
                    $payer_name = $payment_slip->payer;

                    if ($payment_slip->office)
                    {
                        $office_name = $payment_slip->office->name;
                    }

                    if ($payment_slip->client_id)
                    {
                        $payer_name = $payment_slip->client->name;
                    }

                    $payment_slip->office_name = $office_name;
                    $payment_slip->payer_name = $payer_name;
                }

                foreach ($data['data']['payout_slips'] as $payout_slip)
                {
                    //get items
                    $slip_items = PayoutSlipItem::select('item', 'description', 'sum')
                        ->where('slip_id', '=', $payout_slip->id)->get();

                    $office_name = '';
                    $employee_name = '';

                    if ($payout_slip->office)
                    {
                        $office_name = $payout_slip->office->name;
                    }

                    if ($payout_slip->employee)
                    {
                        $employee_name = $payout_slip->employee->first_name.' '.$payout_slip->employee->last_name;
                    }

                    $payout_slip->office_name = $office_name;
                    $payout_slip->employee_name = $employee_name;

                    /*
                    |--------------------------------------------------------------------------
                    |--------------------------------------------------------------------------
                    */

                    //set items array
                    $items_array = [];

                    $items_sum = 0;
                    $i = 1;

                    foreach ($slip_items as $item)
                    {
                        $items_sum += $item->sum;

                        //add item to items array
                        $items_array[] = ['rb' => $i, 'item' => $item->item, 'description' => $item->description,
                            'sum' => $item->sum];

                        $i++;
                    }

                    $payout_slip->items = $items_array;
                    $payout_slip->items_sum = $items_sum;
                }
            }

            return ['status' => 1, 'report' => $data['data'], 'company' => $company, 'show_items' => $items];
        }
        catch (Exception $e)
        {
            return ['status' => 0];
        }
    }
}
