<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title_id',
        'type',
        'rate',
        'season',
        'episode',
    ];

    function title() {
        return $this->belongsTo(Title::class, 'title_id');
    }

    function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
