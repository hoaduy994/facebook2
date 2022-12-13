<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    
    public function toArray($request)
    {
        $like = $this->like->filter(function($item) {
            return $item->comment_id == null;
        });
        
        return [
            'user'=>$this->user,
            'post_id' => $this->id,
            'content'=>$this->content,
            'image' => $this->image,
            'privacy' => $this->privacy,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'comments' => CommentResource::collection($this->comments),
            'likes' => LikeResource::collection($like),
        ];
    }
}
