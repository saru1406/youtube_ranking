<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DlYoutubeVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_category_id',
        'video_data',
    ];

    protected $casts = [
        'video_data' => 'array',
    ];
}
