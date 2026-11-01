<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinResource extends JsonResource
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
            'title' => $this->translated_title,
            'slug' => $this->slug,
            'catalog_reference' => $this->catalog_reference,
            'denomination' => $this->denomination,
            'metal' => $this->metal?->value ?? $this->metal,
            'metal_label' => $this->metal?->label() ?? null,
            'weight_g' => $this->weight_g ? (float) $this->weight_g : null,
            'diameter_mm' => $this->diameter_mm ? (float) $this->diameter_mm : null,
            'rarity_grade' => $this->rarity_grade?->value ?? $this->rarity_grade,
            'rarity_label' => $this->rarity_grade?->label() ?? null,
            'view_count' => (int) $this->view_count,
            'period' => new PeriodResource($this->whenLoaded('period')),
            'ruler' => new RulerResource($this->whenLoaded('ruler')),
            'mint' => new MintResource($this->whenLoaded('mint')),
            'primary_image' => new CoinImageResource($this->whenLoaded('primaryImage')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
