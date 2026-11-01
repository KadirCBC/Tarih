<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollectionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'coin_id' => $this->coin_id,
            'user_condition' => $this->user_condition?->value ?? $this->user_condition,
            'user_condition_label' => $this->user_condition?->label() ?? null,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'coin' => new CoinResource($this->whenLoaded('coin')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

