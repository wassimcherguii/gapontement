<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostTranslation extends Model
{
    protected $fillable = [
        'blog_post_id',
        'locale',
        'title',
        'excerpt',
        'body',
    ];

    /**
     * @return BelongsTo<BlogPost, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
