<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Get the category that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }


    /**
     * 
     * return hint of post body
     */
    public function truncate_body($limit)
    {
        $text = $this->body;
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]) . '...';
        }

        return $text;
    }

    public function formatOutput()
    {
        return [
            'id' => $this->id,
            'author' => $this->user->username,
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->featured_image,
            'body' => $this->body,
            'category' => $this->category->name,
            'created_at' => $this->created_at,
            'hint' => $this->truncate_body(50),
        ];
    }
}
