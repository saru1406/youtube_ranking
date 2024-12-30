<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthYoutubeVideo extends Model
{
    protected $fillable = [
        'search_category_id',
        'target_year',
        'target_month',
        'ranking',
        'month_view_count',
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
