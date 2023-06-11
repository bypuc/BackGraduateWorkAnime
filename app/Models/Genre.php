<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Title;

class Genre extends Model
{
    use HasFactory;

    public const list = [
        'ROMANTIC' => 1,
        'ADVENTURE' => 2,
        'ACTION' => 3,
        'COMEDY' => 4,
        'DRAMA' => 5,
        'HORROR' => 6,
        'ROUTINE' => 7,
        'MYSTIC' => 8,
        'DETECTIVE' => 9,
        'TRILLER' => 10,
        'FANTASTIC' => 11,
        'FANTASY' => 12,
    ];

    protected $fillable = [
        'name',
        'description',
        'color',
        'image',
    ];

    protected $hidden = [
    ];

    function titles() {
        return $this->belongsToMany(Title::class, 'title_genre');
    }
}
