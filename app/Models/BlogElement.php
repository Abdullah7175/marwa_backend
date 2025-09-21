<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogElement extends Model
{
    protected $fillable = ['element_type', 'value', 'blog_id'];
}
