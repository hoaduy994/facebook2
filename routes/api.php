<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\JWTController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\StoryController;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\Users;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function() { 
    Route::post('/login', [JWTController::class, 'login'], function ($id) {
        return new UserResource(User::findOrFail($id));
    });

    Route::post('/register', [JWTController::class, 'register']); 
    Route::post('/re_register', [JWTController::class, 're_register']);
    Route::post('/email/verify_OTP/{id}',[VerificationController::class,'verify_OTP']);
    Route::post('/email/logout_OTP',[VerificationController::class,'logout_OTP']);

    Route::post('/logout', [JWTController::class, 'logout']);
    Route::post('/refresh', [JWTController::class, 'refresh']);
    Route::get('/profile/{id}', [JWTController::class, 'profile']);
    Route::post('/profile/{id}', [JWTController::class, 'editProfile']);
    Route::post('/send_token', [ForgotPasswordController::class,'sendMail'])->name('send_token');
    Route::post('/reset_password',[ResetPasswordController::class,'reset'])->name('reset_password');
});

Route::group([
    'middleware' => ['auth:api', 'api'],
], function () {
    Route::post('/post', [PostController::class, 'createPost']);
    Route::post('/post/{id}', [PostController::class, 'updatePost']);
    Route::delete('/post/{id}', [PostController::class, 'deletePost']);

    Route::post('/story', [StoryController::class, 'createStory']);
    Route::post('/story/{id}', [StoryController::class, 'updateStory']);
    Route::delete('/story/{id}', [StoryController::class, 'deleteStory']);

    Route::post('/post/{id}', [CommentController::class, 'createComment']);
    Route::post('post/{id1}/{id2}', [CommentController::class, 'replyComment']);
    Route::post('/post/{id1}/{id2}', [CommentController::class, 'updateComment']);
    Route::delete('/post/{id1}/{id2}', [CommentController::class, 'deleteComment']);

    Route::post('/post/{id}', [ReactionController::class, 'likePost']);
    Route::post('/post/{id1}/{id2}', [ReactionController::class, 'likeComment']);

    Route::get('/index', [HomeController::class, 'index']);

    Route::post('/group', [GroupController::class, 'createGroup']);
    Route::post('/group/{id}', [GroupController::class, 'editGroup']);
    Route::delete('/group/{id}', [GroupController::class, 'deleteGroup']);
});
   
