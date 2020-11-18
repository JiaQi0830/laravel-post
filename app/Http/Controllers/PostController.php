<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        $posts = DB::table('posts')
                ->select('posts.id as id', 'posts.created_at', 'posts.title', 'users.name', 'users.email')
                ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                ->orderBy('posts.created_at', 'desc')
                ->orderBy('posts.updated_at')
                ->paginate(5);

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
        try{
            $post = Post::with('users')->where('posts.id', '=', $id)->firstOrFail();
            $comments = Post::find($id)->comments()->orderBy('created_at', 'desc')->get();
            $totalLikes = count(Post::find($id)->likes()->get());
            $user = auth()->guard('api')->user();
            $hasLiked = $user ? intval(Post::find($id)->likes()->where('user_email', '=', $user->email)->exists()):0;
            
            return response()->json([
                'post'      =>  $post,
                'comments'  =>  $comments,
                'hasLiked'  =>  $hasLiked,
                'totalLikes'=>  $totalLikes
            ]);
        } catch (ModelNotFoundException $ex){
            return response()->json(['message' => 'Post not found.'], 400);
        } catch (Exception $ex){
            return response()->json(['message' => 'Internal server error.'], 500);
        }
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
