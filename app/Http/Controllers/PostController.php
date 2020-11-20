<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Post;
use App\Comment;
use App\Like;
use Exception;
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
        try{
            $post = Post::with('users')->orderBy('posts.updated_at', 'desc')->paginate(5);

            return response()->json([
                'message'   => 'Success',
                'data'      => ['posts' =>  $post],
            ], 200);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }
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
        $user = auth()->guard('api')->user();

        try{

            $validator = Validator::make($request->all(),[
                'title' => ['required'],
                'content' => ['required']
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $validator->errors()
                ], 422);
            }

            Post::create([
                'title'     => $request->title,
                'content'   => $request->content,
                'user_id'    => $request->user()->id
            ]);

            return response()->json([
                'message' =>'Successfully created'
            ]);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }
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
            $isAuthor = 1;
            $post = Post::with('users')->where('posts.id', '=', $id)->firstOrFail();
            $comments = $post->comments()->orderBy('created_at', 'desc')->get();
            $totalLikes = count($post->likes()->get());
            $user = auth()->guard('api')->user();
            $hasLiked = $user ? intval($post->likes()->where('user_email', '=', $user->email)->exists()):0;

            if($post->users()->first()->email != $user->email)
            {
                $isAuthor = 0;
            }
            return response()->json([
                'message'   => 'Success',
                'data'      => [
                            'post'      =>  $post,
                            'comments'  =>  $comments,
                            'hasLiked'  =>  $hasLiked,
                            'totalLikes'=>  $totalLikes,
                            'isAuthor'  =>  $isAuthor
                            ]
            ],200);

        } catch (ModelNotFoundException $ex){
            return response()->json(['message' => 'Post not found.'], 400);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
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
        try{
            
            $validator = Validator::make($request->all(),[
                'title' => ['required'],
                'content' => ['required']
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $validator->errors()
                ], 422);
            }

            $post = Post::findOrFail($id);
            $post->title    = $request->title;
            $post->content  = $request->content;
            $post->save();
            $user = auth()->guard('api')->user();

            if( $post->users()->first()->email != $user->email){
                throw new Exception('Not Author of The Post');
            }

            return response()->json([
                'message' =>'Successfully updated'
            ], 200);

        } catch (ModelNotFoundException $ex){
            return response()->json(['message' => 'Post not found.'], 400);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }
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
        try{
            $post = Post::findOrFail($id);
            $user = auth()->guard('api')->user();
            if( $post->users()->first()->email != $user->email){
                throw new Exception('Not Author of The Post');
            }
            
            $post->delete();

            return response()->json([
                'message' =>'Successfully deleted'
            ], 200);
            
        } catch (ModelNotFoundException $ex){
            return response()->json(['message' => 'Post not found.'], 400);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }

    }

    public function like(Request $request, $id){
        try{
            $post = Post::findOrFail($id);
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

            return response()->json([
                'message' =>'Successfully reacted'
            ], 200);
            
        } catch (ModelNotFoundException $ex){
            return response()->json(['message' => 'Post not found.'], 400);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function comment(Request $request, $id){
        try{

            $validator = Validator::make($request->all(),[
                'content' => ['required']
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $validator->errors()
                ], 422);
            }

            $post = Post::findOrFail($id);

            Comment::create([
                'content'   => $request->content,
                'post_id'   => $id,
                'name'    => $request->user()->name,
                'email'    => $request->user()->email,
            ]);

            return response()->json([
                'message' =>'Successfully commented'
            ], 200);
            
        } catch (ModelNotFoundException $ex){
            return response()->json(['message' => 'Post not found.'], 400);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }
}
