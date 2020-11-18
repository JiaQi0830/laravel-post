<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\Comment;
use App\Like;
use DB;
use Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        // $posts = Post::orderBy('created_at', 'desc')
        //         ->paginate(5);
        $posts = DB::table('posts')
                ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                ->orderBy('posts.created_at', 'desc')
                ->paginate(5);
        // dd($posts);

        return response()->json([
            'posts' =>  $posts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        Post::create([
            'title'     => $request->title,
            'content'   => $request->content,
            'user_id'    => $request->user()->id
        ]);

        return response()->json([
            'message' =>'Successfully created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = DB::table('posts')
        ->leftJoin('users', 'users.id', '=', 'posts.user_id')
        ->where('posts.id', $id)
        ->first();


        $comments = Post::find($id)->comments()->orderBy('created_at', 'desc')->get();
        $totalLikes = count(Post::find($id)->likes()->get());
        $user = auth()->guard('api')->user();

        if($user){
            $likes = Post::find($id)->likes()->where('user_email', '=', $user->email)->get();
            if(count($likes)>0){
                $hasLiked = 1;
            }else{
                $hasLiked = 0;
            }
        }else{
            $hasLiked = 0;
        }

        return response()->json([
            'post'      =>  $post,
            'comments'  =>  $comments,
            'hasLiked'  =>  $hasLiked,
            'totalLikes'=>  $totalLikes
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $post = Post::find($id);
        $post->title    = $request->title;
        $post->content  = $request->content;
        $post->save();

        return response()->json([
            'message' =>'Successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function like(Request $request, $id){
        $post = Post::find($id);
        $likes = $post->likes()->where('user_email', '=', Auth::user()->email)->get();
        if(count($likes)>0){
            $post->likes()->where('user_email', '=', Auth::user()->email)->delete();
        }else{
            Like::create([
                'post_id'   => $id,
                'user'    => $request->user()->name,
                'user_email'    => $request->user()->email,
            ]);
        }
    }

    public function comment(Request $request, $id){

        Comment::create([
            'content'   => $request->content,
            'post_id'   => $id,
            'name'    => $request->user()->name,
            'email'    => $request->user()->email,
        ]);

        return response()->json([
            'message' =>'Successfully commented'
        ]);
    }
}
