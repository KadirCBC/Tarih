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
        $val = $this->estimated_valuation;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'coin_id' => $this->coin_id,
            'user_condition' => $this->user_condition?->value ?? $this->user_condition,
            'user_condition_label' => $this->user_condition?->label() ?? null,
            'custom_image_url' => $this->custom_image_url,
            'watermarked_image_url' => $this->watermarked_image_url,
            'display_image_url' => $this->display_image_url,
            'has_mint_error' => (bool) $this->has_mint_error,
            'mint_error_type' => $this->mintErrorType ? [
                'id' => $this->mintErrorType->id,
                'name' => $this->mintErrorType->name,
                'slug' => $this->mintErrorType->slug,
                'severity_impact' => $this->mintErrorType->severity_impact,
                'multiplier_min' => (float) $this->mintErrorType->multiplier_min,
                'multiplier_max' => (float) $this->mintErrorType->multiplier_max,
            ] : null,
            'mint_error_notes' => $this->mint_error_notes,
            'purchase_price_usd' => $this->purchase_price_usd ? (float) $this->purchase_price_usd : null,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'estimated_valuation' => $val ? [
                'min_value_usd' => (float) $val->min_value_usd,
                'max_value_usd' => (float) $val->max_value_usd,
                'price_range_formatted' => $val->formatted_price_range,
            ] : null,
            'coin' => new CoinResource($this->whenLoaded('coin')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

