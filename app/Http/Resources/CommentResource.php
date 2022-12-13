<?php

namespace App\Http\Resources;

use App\Models\Reaction;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'user' =>$this->user,
            'id'=>$this->id,
            'parent_id'=>$this->parent_id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'replies'=> CommentResource::collection($this->replies),
            'likes' => LikeResource::collection($this->like),
        ];
        
    }
}
