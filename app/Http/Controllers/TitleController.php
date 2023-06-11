<?php

namespace App\Http\Controllers;

use App\Http\Resources\GenreResource;
use App\Http\Resources\TitlePageResource;
use App\Http\Resources\TitleSearchResource;
use App\Models\Comment;
use App\Models\Title;
use App\Models\Season;
use App\Models\Episode;
use App\Http\Resources\PremierResource;
use App\Http\Resources\TitleCardResource;
use App\Models\UserTitle;
use Auth;
use Illuminate\Http\Request;
use App\Models\Premier;
use App\Models\Genre;
use Laravel\Passport\Token;

class TitleController extends Controller
{
    private const TITLES_PER_CATALOG_PAGE = 6;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Title::with(['genres:id,name', 'premier'])->get();
    }

    public function store(Request $request)
    {
        $validated = \Validator::make($request->all(),
            [
                'name' => 'required|string',
                'release_date' => 'required|string',
                'author' => 'required|string',
                'studio' => 'required|string',
                'description' => 'required|string',
                'genres' => 'required|string',
                'image' => 'required|file',
                'big_image' => 'required|file',
                'type' => 'required|string',
                'premier_description' => 'string',
                'status' => 'integer',
                'premier_image' => 'file',
            ]
        );

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $name = $request->name;
        $releaseDate = $request->release_date;
        $author = $request->author;
        $studio = $request->studio;
        $description = $request->description;
        $genres = explode(',' ,$request->genres);
        $status = $request->status;
        $image = $request
            ->image
            ->storeAs(
                'images/titles', 
                date('d-m-y_H-i').'-card.'.$request->image->extension(),
                'public'
            );
        $bigImage = $request
            ->big_image
            ->storeAs(
                'images/titles', 
                date('d-m-y_H-i').'-title.'.$request->image->extension(),
                'public'
            );
        $rate = $request->rate;
        $type = $request->type;
        $premierDescription = $request->premier_description;
        if ($request->premier_description) {
            $premierImage = $request
                ->premier_image
                ->storeAs(
                    'images/premiers', 
                    date('d-m-y_H-i').'-card.'.$request->premier_image->extension(),
                    'public'
                );
        }

        $title = Title::create([
            'name' => $name,
            'description' => $description,
            'genres' => $genres,
            'image' => $image,
            'rating' => $rate,
            'type' => $type,
            'author' => $author,
            'big_image' => $bigImage,
            'studio' => $studio,
            'release_date' => $releaseDate,
            'title_status_id' => $status,
        ]);

        if ($premierDescription) {
            $premier = new Premier;
            $premier->description = $premierDescription;
            $premier->image = $premierImage;
            $premier->title_id = $title->id;
            $premier->save();

            $title->premier_id = $premier->id;
            $title->save();
        }

        if ($genres) {
            $title->genres()->attach(Genre::find([$genres]));
        }
    }

    private $TITLES_IN_BLOCK = 6;
    public function getMainPageTitles() 
    {
        $premier = Title::with(['genres:id,name', 'premier'])
            ->where('premier_id', '<>', null)
            ->orderBy('updated_at')
            ->first();

        $rateBlock = Title::orderBy('rating', 'desc')->take($this->TITLES_IN_BLOCK)->get();

        $romanticBlock = Genre::find(Genre::list['ROMANTIC'])->titles()->take($this->TITLES_IN_BLOCK)->get();

        $comedyBlock = Genre::find(Genre::list['COMEDY'])->titles()->take($this->TITLES_IN_BLOCK)->get();

        $genres = Genre::where('description', '<>', '')->take($this->TITLES_IN_BLOCK)->get();

        return [
            'premier' => PremierResource::make($premier),
            'rate' => TitleCardResource::collection($rateBlock),
            'genres' => GenreResource::collection($genres),
            'romantic' => TitleCardResource::collection($romanticBlock),
            'comedy' => TitleCardResource::collection($comedyBlock),
        ];
    }

    public function getCatalogTitles(Request $request) {

        if ($request->query('genres')) {
            $genres = explode(',', $request->query('genres'));
            $titles = Genre::with(['titles'])
                ->whereIn('id', $genres)
                ->get()
                ->map(function ($item) {
                    return $item->titles;
                });
            foreach($titles as $item) {
                $titles[0] = $titles[0]->merge($item)->unique();
            }
            $titles = $titles[0];
        } else {
            $titles = Title::all();
        }

        if ($request->query('types')) {
            $types = explode(',', $request->query('types'));
            $titles =$titles->whereIn('type', $types);
        }

        if ($request->query('statuses')) {
            $statuses = explode(',', $request->query('statuses'));
            $titles =$titles->whereIn('title_status_id', $statuses);
        }

        return TitleCardResource::collection($titles->toQuery()->paginate(self::TITLES_PER_CATALOG_PAGE));
    }

    public function getTopTitles(Request $request) {
        $titles = Title::orderBy('rating', 'desc');
        return TitleCardResource::collection($titles->paginate(self::TITLES_PER_CATALOG_PAGE));
    }

    public function getTitle($id) {
        return TitlePageResource::make(Title::find($id));
    }

    public function postComment(Request $request, $id) {
        $validated = \Validator::make($request->all(),
            [
                'text' => 'required|string',
            ]
        );

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        Comment::create([
            'text' => $request->text,
            'user_id' => \Auth::user()->id,
            'title_id' => $id,
        ]);

        return TitlePageResource::make(Title::find($id));
    }

    public function addSeasonToTitle(Request $request, $id) {
        $validated = \Validator::make($request->all(),
            [
                'season' => 'required|integer',
                'episodes' => 'required|array',
                'episodes.number' => 'integer',
                'episodes.link' => 'string',
            ]
        );

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $season = Season::create([
            'number' => $request->season,
            'title_id' => $id
        ]);

        array_map(function ($item) use ($season) {
            Episode::create([
                'number' => $item['number'],
                'link' => $item['link'],
                'season_id' => $season->id,
            ]);
        }, $request->episodes);

        return TitlePageResource::make(Title::find($id));
    }

    public function addImageToGenre(Request $request) {
        $validated = \Validator::make($request->all(),
            [
                'id' => 'required|integer|exists:genres',
                'image' => 'required|file',
            ]
        );

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $genre = Genre::find($request->id);

        $image = $request
            ->image
            ->storeAs(
                'images/genres', 
                date('d-m-y_H-i').'-genre.'.$request->image->extension(),
                'public'
            );
        
        $genre->image = $image;
        $genre->save();
    }

    // $this->getUserByToken($request->header('Authorization'));
    private function getUserByToken($token) {
        $token = explode(' ', $token);
        $token = $token[1];
        $token = explode('.', $token);
        $token = $token[1];
        $token = base64_decode($token);
        $token = json_decode($token, true);
        $token = $token['jti'];
        return Token::find($token)->user;
    }

    public function rateTitle(Request $request, $id) {
        $validated = \Validator::make(['rate' => $request->rate, 'id' => $id],
            [
                'id' => 'required|exists:titles',
                'rate' => 'required|integer',
            ],
        );

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $title = Title::find($id);
        $user = Auth::user();

        $rate = UserTitle::firstOrCreate([
            'title_id' => $title->id,
            'user_id' => $user->id,
            'type' => 'rate',
        ]);

        $amountOfRates = $title['amount-of-rates'];
        $rating = $title->rating * $amountOfRates;
        if ($rate->rate) $rating -= $rate->rate;
        else $amountOfRates += 1; 
        $rating = ($rating + $request->rate) / $amountOfRates;
        $title->rating = $rating;
        $title['amount-of-rates'] = $amountOfRates;
        $title->save();

        $rate->rate = $request->rate;
        $rate->save();

        return TitlePageResource::make($title);
    }

    public function likeTitle(Request $request, $id) {
        $title = Title::find($id);
        $user = Auth::user();

        $link = UserTitle::where('title_id', $title->id)
            ->where('user_id', $user->id)
            ->where('type', 'like')
            ->first();

        if ($link) {
            $link->delete();
        } else {
            UserTitle::create([
                'title_id' => $title->id,
                'user_id' => $user->id,
                'type' => 'like'
            ]);
        }

        return TitlePageResource::make($title);
    }

    public function getProfileTitles(Request $request) {
        $page = $request->query('page');
        $titles = Auth::user()
            ->likedTitles()
            ->orderBy('created_at', 'desc')
            ->where('type', 'like')
            ->get()
            ->map(function ($item) {
                return $item->title;
            })
            ->toQuery();
        return TitleCardResource::collection(
            $titles->paginate(self::TITLES_PER_CATALOG_PAGE)
        );
    }

    public function findTitles(Request $request) {
        $search = $request->query('search');
        if ($search) {
            $titles = Title::where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%')
                ->take(5)
                ->get();

            return TitleSearchResource::collection($titles);
        }
        return ['data' => []];
    }
}
