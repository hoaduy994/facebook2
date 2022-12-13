<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{

    public function saveImage($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/employees/group', $imageName);
        return url('storage/employees/group', $imageName);
    }

    public function saveImageBG($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/employees/BGgroup', $imageName);
        return url('storage/employees/BGgroup', $imageName);
    }

    public function saveImagePost($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/employees/Post_group', $imageName);
        return url('storage/employees/Post_group', $imageName);
    }

    public function createGroup(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'privacy' => 'in:1,2',
        ],[
            'name.required' => 'Tên nhóm không được bỏ trống.',
        ]);

        $data = [
            'name' => $request->name,
            'privacy' => $request->privacy,
        ];

        $user = auth()->user();
        $group = $user->groups()->create($data);
        
        $member = DB::table('user_groups')->insert([
            'user_id' => $user->id,
            'group_id'=> $group->id,
            'member_id' => $user->id,
            'status'=>'1',
        ]);

        return response()->json([
            'message' => 'Đã tạo nhóm thành công.',
            'group' => $group,
            'member' => $member,
        ]);
    }

    public function editGroup(Request $request, $id)
    {
        $request->validate([
            'name'=> 'required|string',
            'privacy' => 'in:1,2',
            'avatar_group' =>'image|mimes:jpeg,jpg,png,gif|max:10000',
            'background_image_group' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'description' => 'required|string',
        ],[
            'name.required' =>'Tên nhóm không được bỏ trống.',
        ]);

        $group = Group::find($id);
        // dd($group);
        if (!$group) {
            return response()->json([
                'message' => 'group not found',
            ], 404);
        }

        $user = auth()->user();
        // dd($user);
        $checkAuthor = $user->groups->where('id', $id)->first();
        // dd($checkAuthor);
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Not permission to delete group',
            ], 403);
        }
        // dd($request);
        // dd($file);
        DB::beginTransaction();
        
        try {
            $group->name =  $request->name;
            $group->privacy =  $request->privacy;
            if ($request->avatar_group){
            $group->avatar_group = $this->saveImage($request->avatar_group); 
            }
            if ($request->background_image_group){
            $group->background_image_group =  $this->saveImageBG($request->background_image_group);
            }
            $group->description =  $request->description;
            
            $group->save();
            DB::commit();
            return response()->json([
                'message' => 'Cập nhập thông tin nhóm thành công.',
                'group' => $group
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteGroup(Request $request, $id){
        $group = Group::find($id);
        // dd($group);
        if (!$group) {
            return response()->json([
                'message' => 'group not found',
            ], 404);
        }

        $user = auth()->user();
        // dd($user);
        $checkAuthor = $user->groups()->where('id', $id)->first();
        // dd($checkAuthor);
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Not permission to delete group',
            ], 403);
        }

        DB::beginTransaction();
        
        try {
            $group->delete();
            DB::commit();
            return response()->json([
                'message' => 'Đã xóa nhóm thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
