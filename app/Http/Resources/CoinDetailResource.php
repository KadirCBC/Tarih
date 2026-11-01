<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinDetailResource extends JsonResource
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
            'title' => $this->title,
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

            // Ön Yüz (Obverse) Epigrafisi ve Tasviri
            'obverse' => [
                'desc' => $this->obverse_desc,
                'legend' => $this->obverse_legend,
                'legend_translation' => $this->obverse_legend_trans,
            ],

            // Arka Yüz (Reverse) Epigrafisi ve Tasviri
            'reverse' => [
                'desc' => $this->reverse_desc,
                'legend' => $this->reverse_legend,
                'legend_translation' => $this->reverse_legend_trans,
            ],

            'historical_notes' => $this->historical_notes,
            'legal_notice' => 'Bu veri 2863 sayılı kanun kapsamında akademik/eğitim amaçlı nümizmatik arşiv kaydıdır. Alım-satım yapılmaz.',

            // İlişkiler
            'period' => new PeriodResource($this->whenLoaded('period')),
            'ruler' => new RulerResource($this->whenLoaded('ruler')),
            'mint' => new MintResource($this->whenLoaded('mint')),
            'images' => CoinImageResource::collection($this->whenLoaded('images')),
            'primary_image' => new CoinImageResource($this->whenLoaded('primaryImage')),
            'valuations' => ValuationResource::collection($this->whenLoaded('valuations')),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

