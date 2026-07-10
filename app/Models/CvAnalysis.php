<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cv_path',
        'skills',
        'careers',
        'improvements',
        'raw_text',
    ];

    protected $casts = [
        'skills' => 'array',
        'careers' => 'array',
        'improvements' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
