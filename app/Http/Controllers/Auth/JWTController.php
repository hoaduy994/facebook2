<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource as ResourcesUserResource;
use App\Mail\UserVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JWTController extends Controller
{
    //  /**
    //  * Create a new AuthController instance.
    //  *
    //  * @return void
    //  */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','re_register']]);
    }

    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'gender' => 'required|string|in:1,2',
            'password' => 'required|string|confirmed|min:6',
            'number_phone' => 'required|string|size:10',
            'birthday' => [
                'required',
                'before:' . Carbon::now()->subYears(18)->format('Y-m-d')
              ],
        ],[
            'name.required' => 'Họ và tên không được bỏ trống.',
            'email.required' => 'Email không được bỏ trống.',
            'email.unique' => 'Tài khoản đã tồn tại.',
            'gender.required' => 'Hãy chọn giới tính.',
            'password.required' => 'Mật khẩu không được bỏ trống.',
            'password.min'=> 'Mật khẩu lớn hơn 6 kí tự.',
            'number_phone.size'=>'SĐT 10 số thôi, gì mà dài thế!.',
            'birthday.before' => 'Phải trên 18 tuổi.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user['confirm'] == 1)
                return response()->json([
                    'message' => 'Email đã tồn tại.',
                ], 401);
            else {
                 return response()->json([
                        'message' => 'Đã có lỗi xảy ra.',
                        'error' => $validator->errors()
                    ], 400);
            }
        }
        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'confirm' => 2,
                'confirmation_code' => rand(100000, 999999),
                'confirmation_code_expired_in' => Carbon::now()->addSecond(60)
            ]
        ));
        try {
            Mail::to($user->email)->send(new UserVerification($user));
            return response()->json([
                'message' => 'Đã đăng ký, xác minh địa chỉ email của bạn để đăng nhập.',
                'user' => $user
            ], 201);
        } catch (\Exception $err) {
            $user->delete();
            return response()->json([
                'message' => 'Không thể gửi xác minh email, vui lòng thử lại.',
            ], 500);
        }
        return response()->json([
            'message' => 'Không thể đang ký.',
        ], 500);
    }

    public function re_register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
        ]);
        // dd($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // dd($request->all());
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user['confirm'] == 1)
                return response()->json([
                    'message' => 'Email đã tồn tại.',
                ], 401);
            else {
                $user->confirmation_code = rand(100000, 999999);
                $user->confirmation_code_expired_in = Carbon::now()->addSecond(60);
                $user->save();
                try {
                    Mail::to($user->email)->send(new UserVerification($user));
                    return response()->json([
                        'message' => 'Mã xác minh đã được gửi lại, vui lòng truy cập email đã đăng ký của bạn để xác nhận.',
                        'user' => $user
                    ], 201);
                } catch (\Exception $err) {
                    $user->delete();
                    return response()->json([
                        'message' => 'Không thể gửi xác minh email, vui lòng thử lại.',
                        'error' => $validator->errors(),
                    ], 500);
                }
            }
        }
        return response()->json([
            'message' => 'Không thể đăng ký lại.',
        ], 500);
    }

    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ],[
            'email.required' => 'Email không được bỏ trống!',
            'password.required' => 'Mật khẩu không được bỏ trống!',
            'password.min' => 'Mật khẩu lớn hơn 6 kí tự!'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Tài khoản hoặc mật khẩu không chính xác!'], 401);
        }
        
        return response()->json([
            'message' => 'Đăng nhập.',
            'user' => auth()->user(),
            'token'=> $this->respondWithToken($token)->original,
        ]);
        
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    /**
     * Refresh token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
     
        ]);
    }

    /**
     * Get user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function profile($id)
    {   
        try {
            return new userResource(User::findOrFail($id));
        }catch (\Exception $e) {
            return response()->json([$e->getMessage()]);
        }
    }

    public function editProfile(Request $request, $id){
        $request->validate([
            'name' => 'string|required',
            'avatar' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'background_img' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'gender' => 'required|string|in:1,2',
            'bio' => 'string|max:255',
            'address' => 'string',
            'birthday' => [
                'required',
                'before:' . Carbon::now()->subYears(18)->format('Y-m-d')
              ],
        ],[
            'name.required' => 'Họ và tên không được bỏ trống.',
            'gender.required' => 'Chọn giới tính.',
            'birthday.required' => 'Nhập ngày sinh.'
        ]);

        $users = User::find($id);
        $checkAuthor = auth()->user();
        if (!$checkAuthor) {
            return response()->json([
                'message' => 'Không được phép cập nhật hồ sơ.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $users->name =  $request->name;
            if ($request->avatar) {
                $users->avatar = $this->saveImage($request->avatar);
            }
            if ($request->background_img){
                $users->background_img = $this->saveImageBG($request->background_img);
            }

            $users->gender = $request->gender;
            $users->bio = $request->bio;
            $users->address = $request->address;
            $users->save();
            
            DB::commit();
            return response()->json([
                'message' => 'Cập nhập thành công.',
                'user' => $users
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveImage($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/avt/', $imageName);
        return url('storage/avt/'.$imageName);
    }
    public function saveImageBG($image){
        $imageName =  uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/BGproile/', $imageName);
        return url('storage/BGproile/'.$imageName);
    }
}
