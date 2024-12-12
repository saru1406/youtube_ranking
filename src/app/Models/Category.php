<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_number',
        'category_name',
        'category_physical_name',
    ];

    /**
     * DlYoutubeVideoと紐づけ
     *
     * @return HasMany
     */
    public function dlYoutubeVideos(): HasMany
    {
        return $this->hasMany(DlYoutubeVideo::class, 'search_category_id', 'category_number');
    }
}
