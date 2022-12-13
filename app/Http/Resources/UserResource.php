<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'background_img' => $this->background_img,
            'number_phone' => $this->number_phone,
            'gender' => $this->gender,
            'bio'=> $this->bio,
            'address' => $this->address,
            'birthday' => $this->birthday,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'posts' => PostResource::collection($this->post),
            // 'post' => PostResource::make($this->post),
            // 'data'=>$this->when(Auth::user(), function () {
            //     return PostResource::collection($this->post);
            // }),
     
        ];
    }
}
