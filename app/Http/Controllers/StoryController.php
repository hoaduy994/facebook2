<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoryResource;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }
    
    public function createStory(Request $request) {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|',
            'privacy' => 'required|string|in:1,2,3',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($request->image) {
            $data = [
                'content' => $request->content,
                'privacy' => $request->privacy,
                'image' => $this->saveImageStory($request->image),
            ];
        } else {
            $data = [
                'content' => $request->content,
                'privacy' => $request->privacy,
            ];
        }
        
        $user = auth()->user();
        if (!$user) {
            return response()->json($validator->errors(), 400);
        }
        $story = $user->story()->create($data);

        return (new StoryResource($story))->response();
    }

    public function updateStory(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'privacy' => 'required|in:1,2,3',
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        $story = Story::find($id);
        if (!$story) {
            return response()->json([
                'message' => 'Story not found',
            ], 404);
        }

        $checkAuthor = auth()->user()->story()->where('id', $id)->first();
        if (!$checkAuthor) {
            return response()->json($validator->errors(), 400);
        }
    
        DB::beginTransaction();
        try {
            $story->content =  $request->content;
            $story->privacy =  $request->privacy;
            if ($request->image) {
                    $story->image = $this->saveImageStory($request->image);
            } else {
               $story->image = null;
            }
            $story->save();
            DB::commit();
            return (new StoryResource($story))->response();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteStory($id){
        $story = Story::find($id);
        if (!$story) {
            return response()->json([
                'message' => 'Story not found',
            ], 404);
        }

        $checkAuthor = auth()->user()->story()->where('id', $id)->first();
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Not permission to delete story.',
            ], 403);
        }

        DB::beginTransaction();
        
        try {
            $story->delete();
            DB::commit();
            return response()->json([
                'message' => 'success.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveImageStory($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/story/', $imageName);
        return url('storage/story/'.$imageName);
    }
}
