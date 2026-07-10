<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'description',
        'required_skills',
    ];

    protected $casts = [
        'required_skills' => 'array',
    ];
}
