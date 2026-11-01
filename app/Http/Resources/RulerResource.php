<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RulerResource extends JsonResource
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
            'period_id' => $this->period_id,
            'name' => $this->name,
            'title' => $this->title,
            'slug' => $this->slug,
            'reign_start' => $this->reign_start,
            'reign_end' => $this->reign_end,
            'formatted_reign' => $this->formatted_reign,
            'biography' => $this->biography,
            'period' => new PeriodResource($this->whenLoaded('period')),
            'coins_count' => $this->whenCounted('coins'),
        ];
    }
}

