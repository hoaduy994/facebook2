<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class VerificationController extends Controller
{
    
    public function verify_OTP(Request $request,$id)
    {
        $request->validate([
            'OTP_token' => 'required|size:6',
        ],[
            'OTP_token.required' => 'Hãy nhập mã xác nhận.',
            'OTP_token.size' => 'Mã xác nhận có 6 ký tự.'
        ]);
    
        $user = User::findOrFail($id);
        
        if (!$user) {
            return response()->json(["status" => 400, "message" => "Người dùng không tồn tại."], 400);
        }
        if (Carbon::now()->gt($user->confirmation_code_expired_in)) {
            return response()->json(["status" => 400, "message" => "OTP của bạn đã hết hạn"], 400);
        } else {
            if (($request->OTP_token) != $user->confirmation_code) {
                return response()->json(["status" => 400, "message" => "OTP của bạn không hợp lệ"], 400);
            }
            $user->confirm = true;
            $user->save();
            return response()->json(["status" => 200, "message" => "Đã xác minh thành công."], 200);
        }
    }
    public function logout_OTP(Request $request,$id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json(["status" => 400, "message" => "Người dùng không tồn tại."], 400);
        }
        if ($user->confirm == false) {
            $result = $user->delete();
            if ($result)
                return response()->json(["status" => 200, "message" => "Đã hủy bỏ."], 200);
            else {
                return response()->json(["status" => 400, "message" => "Đã có lỗi xảy ra."], 400);
            }
        }
        return response()->json(["status" => 400, "message" => "Unauthorized"], 400);
    }
}
