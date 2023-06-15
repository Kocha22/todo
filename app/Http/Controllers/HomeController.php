<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Auth;

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
        $user_id=Auth::user()->id;
        $user = User::where('id', $user_id)->first();  
        
        $data= Post::where('user_id', $user_id)->get();  
        $tagsArray = [];

        foreach ($data as $post) {
            $tags = $post->tags;
            foreach ($tags as $tag) {
                $tagsArray[$tag->name] = $tag;
            }
        }

        return view('home', ['user'=>$user, 'tags' => $tagsArray]);
    }
}
