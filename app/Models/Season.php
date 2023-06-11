<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'title_id',
    ];

    function title() {
        return $this->belongsTo(Title::class, 'title_id');
    }

    function episodes() {
        return $this->hasMany(Episode::class);
    }
}
