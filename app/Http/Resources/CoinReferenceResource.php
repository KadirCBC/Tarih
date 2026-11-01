<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinReferenceResource extends JsonResource
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
            'catalog_system' => $this->catalog_system,
            'reference_number' => $this->reference_number,
            'full_reference' => "{$this->catalog_system} {$this->reference_number}",
            'notes' => $this->notes,
        ];
    }
}

