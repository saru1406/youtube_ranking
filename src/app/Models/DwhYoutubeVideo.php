<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DwhYoutubeVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'title',
        'description',
        'channel_id',
        'channel_name',
        'view_count',
        'like_count',
        'comment_count',
        'url',
        'published_at',
    ];
}
