<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory; 

    protected $casts = [
        'content' => 'array',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
