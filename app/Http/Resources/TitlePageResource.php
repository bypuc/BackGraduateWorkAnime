<?php

namespace App\Http\Resources;

use App\Models\UserTitle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Title;
use App\Models\Genre;
use Laravel\Passport\Token;

class TitlePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $typesOnRussian = [
            'anime' => 'TV-сериал',
            'movie' => 'Полнометражный фильм',
            'ova' => 'OVA',
            'ona' => 'ONA',
        ];

        $alsoCool = Genre::whereIn('id', [1, 2])->get()->map(
            function ($item) {
                return $item->titles;
            }
        );
        $alsoCool->map(function ($item) use ($alsoCool) {
            $alsoCool[0] = $alsoCool[0]->merge($item);
        });
        $alsoCool = $alsoCool[0]->where('id', '<>', $this->id)->unique()->take(6);

        if ($this->comments->count()) $comments = $this->comments->toQuery()->orderBy('created_at', 'desc')->get();
        else $comments = [];

        $userRate = null;
        $isLiked = null;
        if ($request->header('Authorization')) {
            $token = explode(' ', $request->header('Authorization'));
            $token = $token[1];
            $token = explode('.', $token);
            $token = $token[1];
            $token = base64_decode($token);
            $token = json_decode($token, true);
            $token = $token['jti'];
            $user = Token::find($token)->user;

            $rate = UserTitle::where('user_id', $user->id)
                ->where('title_id', $this->id)
                ->where('type', 'rate')
                ->first();
            if ($rate) $userRate = $rate->rate;

            $isLiked = !!UserTitle::where('user_id', $user->id)
                ->where('title_id', $this->id)
                ->where('type', 'like')
                ->first();
        }


        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => asset('/storage/'.$this->big_image),
            'author' => $this->author,
            'studio' => $this->studio,
            'userRate' => $userRate,
            'isLiked' => $isLiked,
            'genres' => $this->genres->implode('name', ', '),
            'release' => $this->release_date,
            'status' => $this->status->name,
            'type' => $typesOnRussian[$this->type],
            'rate' => round($this->rating, 1),
            'amountOfRates' => $this['amount-of-rates'],
            'alsoCool' => TitleCardResource::collection($alsoCool),
            'comments' => CommentResource::collection($comments),
            'seasons' => SeasonResource::collection($this->seasons),
        ];
    }
}

// $this->genres->map(function ($item) {return $item->id;})