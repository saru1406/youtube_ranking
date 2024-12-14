<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DlDailyYoutubeVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_category_id',
        'video_data',
    ];

    protected $casts = [
        'video_data' => 'array',
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
