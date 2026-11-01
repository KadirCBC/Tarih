<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinVariantResource extends JsonResource
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
            'variant_code' => $this->variant_code,
            'obverse_diff' => $this->obverse_diff,
            'reverse_diff' => $this->reverse_diff,
            'rarity_shift' => $this->rarity_shift,
        ];
    }
}

