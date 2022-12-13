<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetPassword;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ResetPasswordRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|max:6',
            'password' => 'required|confirmed|min:6',
        ],[
            'password.required' => 'Mật khẩu không được bỏ trống.',
            'password.min'=> 'Mật khẩu lớn hơn 6 kí tự.',
            'token.required' => 'Mã xác nhận không được bỏ trống.',
            'token.max'=> 'Mã xác nhận tối đa 6 kí tự.',
        ]);
        // dd($request);
        $checktoken = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();
        // dd($checktoken);
        if(!$checktoken){
            return response()->json([
                'message' => 'Mã xác nhận không đúng.',
            ]);
        }else {
            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password)
            ]);  
            
            DB::table('password_resets')->where([
            'email' => $request->email,
        ])->delete();

        }

        return response()->json([
            'message' => 'Bạn đã thay đổi mật khẩu thành công.',
        ]);
    }
}
