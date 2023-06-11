<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Title;

class TitleStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
    ];

    function titles() {
        return $this->hasMany(Title::class);
    }
}
