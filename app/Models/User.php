<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'avatar',
    //     'background_img',
    //     'gender',
    //     'bio',
    //     'address',
    //     'number_phone',
    //     'confirm',
    //     'confirmation_code',
    //     'confirmation_code_expired_in'
    // ];
    protected $guarded = [];
    protected $table = 'users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'confirm',
        'email_verified_at',
        'confirmation_code',
        'confirmation_code_expired_in',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function post() {
        return $this->hasMany(Post::class);
    }

    public function story() {
        return $this->hasMany(Story::class);
    }

    public function comment() {
        return $this->hasMany(Comment::class);
    }

    public function like() {
        return $this->hasMany(Reaction::class);
    }

    public function groups() {
        return $this->hasMany(Group::class);
    }
    
    public function members (){
        return $this->hasMany(GroupUser::class, 'user_id')->where('stt', GroupUser::IS_MEMBER);
    }

    
    public function memberRequests (){
        return $this->hasMany(GroupUser::class, 'user_id')->where('stt', GroupUser::REQUEST_MEMBER);
    }  
}
