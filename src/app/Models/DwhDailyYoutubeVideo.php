<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DwhDailyYoutubeVideo extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'search_category_id',
        'video_id',
        'title',
        'description',
        'channel_id',
        'channel_name',
        'view_count',
        'like_count',
        'comment_count',
        'category_id',
        'url',
        'duration',
        'published_at',
    ];

    /**
     * カテゴリと紐づけ
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'search_category_id', 'category_number');
    }
}