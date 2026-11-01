<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValuationResource extends JsonResource
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
            'condition_grade' => $this->condition_grade?->value ?? $this->condition_grade,
            'condition_label' => $this->condition_grade?->label() ?? null,
            'min_value_usd' => (float) $this->min_value_usd,
            'max_value_usd' => (float) $this->max_value_usd,
            'price_range_formatted' => $this->formatted_price_range,
            'last_updated_at' => $this->last_updated_at?->format('Y-m-d'),
        ];
    }
}

