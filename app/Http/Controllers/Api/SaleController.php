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
        if (request()->query('query')) {
            $business_name = request()->query('query');
            $sales = Sale::where('business_name', 'like', "%$business_name%")->paginate(request()->query('per_page') ?? 10);
            if ($sales) {
                return $this->sendResponse(SaleResource::collection($sales), 'Sales Data retrieved successful.');
            } else {
                return $this->sendError('No Data Found for ' . request()->query('query'));
            }
        }

        $sales = Sale::paginate(request()->query('per_page') ?? 10);
        return $this->sendResponse(SaleResource::collection($sales), 'Sales Data retrieved successful.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $sales = Sale::create($request->all());
        return $this->sendResponse($sales, 'Sale Record Created Successful!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sales = Sale::find($id);
        if ($sales) {
            return $this->sendResponse($sales, 'Sale Record Fetched Successful!');
        } else {
            return $this->sendError('Sale Record Not Found!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd($request);
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
        return $this->sendResponse($sales, 'Sale Record Created Successful!');
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
