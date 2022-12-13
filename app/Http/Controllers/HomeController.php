<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResourceCollection;
use App\Http\Resources\StoryResourceCollection;
use App\Models\Post;
use App\Models\Story;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $posts = Post::paginate(5);
        $story = Story::paginate(5);
        return response()->json([
            'post' => (new PostResourceCollection($posts)),
            'story' => (new StoryResourceCollection($story)),
        ]);
    }
}
