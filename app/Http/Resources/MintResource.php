<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MintResource extends JsonResource
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
            'location' => $this->location,
            'ancient_name' => $this->ancient_name ?: $this->name,
            'modern_name' => $this->modern_name ?: $this->location,
            'active_period' => $this->active_period ?: 'Antik Çağ',
            'historical_significance' => $this->historical_significance,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'has_coordinates' => $this->hasCoordinates(),
            'coins_count' => $this->whenCounted('coins'),
        ];
    }
}

