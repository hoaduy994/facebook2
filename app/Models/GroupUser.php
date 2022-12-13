<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;

    public $timestamps = false;
    // protected $guarded = [];
    protected $table = 'user_groups';
    // protected $fillable = [
    //     'id',
    //     'user_id',
    //     'group_id',
    //     'stt',
    // ];

    const NO_MEMBER = 0;
    const IS_MEMBER = 1;
    const REQUEST_MEMBER = 2;

    const REJECT_MEMBER = 0;
    const ACCPET_MEMBER = 1;

    public function members(){
        return $this->belongsTo(User::class);
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function member(){
        return $this->belongsTo(Groups::class);
    }
    
    public function memberRequests(){
        return $this->belongsTo(Users::class);
    }

    public function join(){
        return $this->belongsTo(Group::class);
    }
}
