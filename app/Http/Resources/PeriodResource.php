<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeriodResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'formatted_date_range' => $this->formatted_date_range,
            'description' => $this->description,
            'coins_count' => $this->whenCounted('coins'),
            'rulers_count' => $this->whenCounted('rulers'),
        ];
    }
}

