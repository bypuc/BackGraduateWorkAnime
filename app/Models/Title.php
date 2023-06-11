<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;
use App\Models\Premier;
use App\Models\TitleStatus;

class Title extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rating',
        'amount-of-rates',
        'image',
        'type',
        'big_image',
        'author',
        'studio',
        'release_date',
        'title_status_id',
    ];

    protected $hidden = [
        'pivot'
    ];

    function genres() {
        return $this->belongsToMany(Genre::class, 'title_genre');
    }

    function comments() {
        return $this->hasMany(Comment::class);
    }

    function premier() {
        return $this->hasOne(Premier::class);
    }

    function status() {
        return $this->belongsTo(TitleStatus::class, 'title_status_id');
    }

    function seasons() {
        return $this->hasMany(Season::class);
    }
}
