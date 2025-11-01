<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogElement extends Model
{
    protected $fillable = ['element_type', 'value', 'blog_id', 'section_title', 'order'];

    /**
     * Get the blog that owns this element.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
