<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\Comment;
use App\Like;
use Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::all();
        return response()->json([
            'posts' =>  $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'author'    => $request->user()->name        
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
        $post = Post::find($id);
        $comments = $post->comments()->orderBy('created_at', 'desc')->get();
        $totalLikes = count($post->likes()->get());
        $user = auth()->guard('api')->user();

        if($user){
            $likes = $post->likes()->where('user_email', '=', $user->email)->get();
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        
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
        log::info("enter?");
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
