<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionRecordResource extends JsonResource
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
            'auction_house' => $this->auction_house,
            'sale_name_or_number' => $this->sale_name_or_number,
            'lot_number' => $this->lot_number,
            'sale_date' => $this->sale_date?->format('Y-m-d'),
            'hammer_price_usd' => (float) $this->hammer_price_usd,
            'realized_price_usd' => (float) $this->realized_price_usd,
            'formatted_price' => $this->formatted_price,
            'condition_grade' => $this->condition_grade?->value ?? $this->condition_grade,
            'source_url' => $this->source_url,
        ];
    }
}

