<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'groups';

    public function groupsCreated1() {
        return $this->belongsToMany(User::class, 'user_groups', 'group_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    
    public function postGroup(){
        return $this->hasMany(Post::class,'group_id');
    }

    // public function memberRequests (){
    //     return $this->hasMany(GroupUser::class, 'user_id')->where('stt', GroupUser::REQUEST_MEMBER);
    // }
    
    public function member(){
        return $this->hasMany(GroupUser::class, 'user_id')->where('stt', GroupUser::IS_MEMBER);
    }

    public function members(){
        return $this->hasMany(GroupUser::class, 'group_id')->where('stt', GroupUser::IS_MEMBER);
    }

    public function join() {
        return $this->hasMany(GroupUser::class, 'user_id');
    }
}
