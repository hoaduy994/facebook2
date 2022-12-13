<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }
    
    public function createPost(Request $request) {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|',
            'privacy' => 'required|string|in:1,2,3',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);
      
        if ($request->image){
            $data = [
                'content' => $request->content,
                'privacy' => $request->privacy,
                'image' => $this->saveImagePost($request->image),
            ];
        } else {
            $data = [
                'content' => $request->content,
                'privacy' => $request->privacy,
                'image' => NULL,
            ];
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json($validator->errors(), 400);
        }
        $post = $user->post()->create($data);

        return (new PostResource($post))->response();
    }

    public function updatePost(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'privacy' => 'in:1,2,3',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);


        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $checkAuthor = auth()->user()->post()->where('id', $id)->first();
        if (!$checkAuthor) {
            return response()->json($validator->errors(), 400);
        }
    
        DB::beginTransaction();
        try {
            $post->content =  $request->content;
            $post->privacy =  $request->privacy;
            if ($request->image) {
                $post->image = $this->saveImagePost($request->image);
            } else {
                $post->image = null;
            }
            $post->save();
            DB::commit();
            return (new PostResource($post))->response();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function deletePost(Request $request, $id){
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $checkAuthor = auth()->user()->post()->where('id', $id)->first();
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Not permission to delete post',
            ], 403);
        }

        DB::beginTransaction();
        
        try {
            $post->delete();
            DB::commit();
            return response()->json([
                'message' => 'Bạn đã xóa bài viết.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveImagePost($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/post/', $imageName);
        return url('storage/post/'.$imageName);
    }
}
