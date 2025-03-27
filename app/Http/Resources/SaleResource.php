<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'services' => $this->services,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'sales_date' => $this->sales_date,
            'remarks' => $this->remarks,
            'file' => $this->file?asset($this->file):null,
        ];
    }
}
