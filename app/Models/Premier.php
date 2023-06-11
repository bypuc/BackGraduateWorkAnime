<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Title;

class Premier extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'image',
    ];

    function title() {
        return $this->belongsTo(Title::class);
    }
}
