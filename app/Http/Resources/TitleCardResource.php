<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TitleCardResource extends JsonResource
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
            'description' => $this->description,
            'genres' => GenreResource::collection($this->genres->toQuery()->orderBy('color', 'desc')->get()),
            'image' => asset('/storage/'.$this->image),
            'rate' => round($this->rating, 1),
            'amountOfRates' => $this['amount-of-rates'],
            'episodes' => $this->episodes,
            'season' => $this->season,
            'type' => $this->type,
        ];
    }
}
