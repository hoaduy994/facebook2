<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function likePost($post_id)
    {
        $user = auth()->user();
        
        $reactionPost = Reaction::firstOrCreate(
            ['user_id' => $user->id, 'post_id' => $post_id],
            ['user_id' => $user->id, 'post_id' => $post_id, 'is_like' => 0],
        );

        if ($reactionPost->is_like > 0)
        {
            $reactionPost->delete();
        } else {
            $reactionPost->increment('is_like', 1);
        }

        $posts = Post::with('user','comments.user','comments.replies.user','like.user')->find($post_id);

        return response()->json([
            'post' => $posts
        ]);
    }

    public function likeComment($post_id, $comment_id)
    {
        $user = auth()->user();
        
        $reactionPost = Reaction::firstOrCreate(
            ['user_id' => $user->id, 'post_id' => $post_id,'comment_id' => $comment_id],
            ['user_id' => $user->id, 'post_id' => $post_id,'comment_id' => $comment_id, 'is_like' => 0],
        );

        if ($reactionPost->is_like > 0)
        {
            $reactionPost->delete();
        } else {
            $reactionPost->increment('is_like', 1);
        }

        $posts = Post::with('user','comments.user','comments.replies.user','like.user')->find($post_id);

        return response()->json([
            'post' => $posts
        ]);
    }
}
