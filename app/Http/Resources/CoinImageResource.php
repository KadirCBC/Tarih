<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinImageResource extends JsonResource
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
            'image_type' => $this->image_type?->value ?? $this->image_type,
            'image_type_label' => $this->image_type?->label() ?? null,
            'image_url' => $this->url,
            'is_primary' => (bool) $this->is_primary,
        ];
    }
}

