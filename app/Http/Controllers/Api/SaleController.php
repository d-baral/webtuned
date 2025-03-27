<?php

namespace App\Http\Controllers\Api;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Resources\SaleResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\Sales\EditRequest;
use App\Http\Requests\Api\Sales\CreateRequest;

class SaleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request()->query('query');
        $date = request()->query('date');
        $sort_by = request()->query('sort_by','asc');
        $perPage = request()->query('per_page', 10);

        $salesQuery = Sale::query();

        if ($query) {
            $salesQuery->whereRaw('LOWER(business_name) LIKE LOWER(?)', ["%{$query}%"]);
        }

        if ($date) {
            $timestamp = strtotime($date);

            // Check if the date format contains only the year (YYYY)
            if (preg_match('/^\d{4}$/', $date)) {
                $salesQuery->whereYear('sales_date', $date);
            }
            // Check if the date format contains year and month (YYYY-MM)
            elseif (preg_match('/^\d{4}-\d{2}$/', $date)) {
                $salesQuery->whereYear('sales_date', date('Y', $timestamp))
                    ->whereMonth('sales_date', date('m', $timestamp));
            }
            // Otherwise, assume a full date (YYYY-MM-DD) and filter by exact date
            else {
                $salesQuery->whereDate('sales_date', date('Y-m-d', $timestamp));
            }
        }

        $sales = $salesQuery->orderBy('created_at',$sort_by)->paginate($perPage);

        if ($sales->isEmpty()) {
            return $this->sendError('No Data Found.');
        }

        return $this->sendResponse(SaleResource::collection($sales), 'Sales Data retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $sale = new Sale;

        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        if ($request->hasFile('file_upload')) {
            $filename = $request->file('file_upload')->getClientOriginalName();
            $getfilenamewitoutext = pathinfo($filename, PATHINFO_FILENAME);
            $getfileExtension = $request->file('file_upload')->getClientOriginalExtension();
            $createnewFileName = time() . '_' . str_replace(' ', '_', $getfilenamewitoutext) . '.' . $getfileExtension;
            $file_path = $request->file('file_upload')->storeAs('file', $createnewFileName);
            $request->request->add(['file' => 'storage/'.$file_path]);
        }

        $sales = $sale->create($request->all());
        return $this->sendResponse(new SaleResource($sales), 'Sale Record Created Successful!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sales = Sale::find($id);
        if ($sales) {
            return $this->sendResponse(new SaleResource($sales), 'Sale Record Fetched Successful!');
        } else {
            return $this->sendError('Sale Record Not Found!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $sales = Sale::find($id);
        if ($sales) {
            $sales->update($request->all());
            return $this->sendResponse($sales, 'Sale Record Updated Successful!');
        } else {
            return $this->sendError('Sale Record Not Found!');
        }
        return $this->sendResponse(new SaleResource($sales), 'Sale Record Created Successful!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sales = Sale::find($id);
        if ($sales) {
            $sales->delete();
            return $this->sendResponse(null, 'Sale Record Deleted Successful!');
        } else {
            return $this->sendError('Sale Record Not Found!');
        }
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'business_name' => 'required',
            'services' => 'required',
            'paid_amount' => 'numeric|gt:0',
            'due_amount' => 'nullable|numeric|gt:0',
            'sales_date' => 'required|before:now',
        ]);
    }
}
