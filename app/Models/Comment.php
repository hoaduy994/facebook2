<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'comments';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable() 
    { 
        return $this->morphTo(); 
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function like() {
        return $this->hasMany(Reaction::class);
    }
}
