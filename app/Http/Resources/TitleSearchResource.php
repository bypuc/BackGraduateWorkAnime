<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TitleSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $genres = $this->genres->map(function ($item) {
            return $item->name;
        });

        $genres = implode(', ', $genres->toArray());

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => asset('/storage/'.$this->image),
            'rate' => $this->rating,
            'genres' => $genres,
        ];
    }
}
