<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = ['wp_post_id','post_title','post_excerpt','post_content','featured_image','categories','tags'];
}
