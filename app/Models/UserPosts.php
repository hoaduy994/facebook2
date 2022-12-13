<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPosts extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'user_posts';

    public function userPost(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function post(){
        return $this->belongsTo(Post::class,'post_id', 'id');
    }
}
