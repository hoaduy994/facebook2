<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function saveImageComment($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/comment/', $imageName);
        return url('storage/comment/'.$imageName);
    }

    public function createComment(Request $request,$id)
    {
        $comment = new Comment;

        $comment->content = $request->content;

        if ($request->image){
             $comment->image = $this->saveImageComment($request->image);
        }
       
        $comment->user()->associate(auth()->user());

        $post = Post::find($id);

        $post->comments()->save($comment);

        $posts = Post::with('user','comments.replies','comments.user')->find($id);

        return response()->json([
            'post' => $posts
        ]);
    }

    public function replyComment(Request $request,$post_id,$comment_id)
    {
        $reply = new Comment;
        
        $comment = Comment::find($comment_id);

        $post = Post::find($post_id);
        
        $reply->content = $request->content;

        if ($request->image){
            $reply->image = $this->saveImageComment($request->image);
        }

        $reply->user()->associate(auth()->user());

        $reply->parent_id = $comment_id;
        
        $post->comments()->save($reply);

        $posts = Post::with('user','comments.user','comments.replies.user')->find($post_id);

        return response()->json([
            'post' => $posts
        ]);;

    }

    public function updateComment(Request $request,$post_id,$comment_id)
    {
        $post = Post::find($post_id);

        $comment = Comment::find($comment_id);


        $checkAuthor = auth()->user()->id->where('id', $comment_id)->first();
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Not permission to update comment',
            ], 403);
        }

        $post = Post::with('user','comments','comments.replies.user')->find($post_id);

        DB::beginTransaction();
        try {
            $comment->content =  $request->content;
            if ($request->image){
                $comment->image = $this->saveImageComment($request->image);
            }
            $comment->save();
            DB::commit();
            return response()->json([
                'message' => 'Updated comment successfully',
                'post' => $post
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteComment($post_id, $comment_id)
    {
        $post = Post::find($post_id);

        $comment = Comment::find($comment_id);
        
        $checkAuthor = auth()->user()->comment()->where('id', $comment_id)->first();
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Not permission to delete comment',
            ], 403);
        }

        $post = Post::with('user','comments','comments.replies')->find($post_id);

        DB::beginTransaction();
        try {
            
            $comment->delete();
            DB::commit();
            return response()->json([
                'message' => 'Delete comment successfully',
                'post' => $post
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
