<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\CommentPostRequest;
use App\Post;
use App\Comment;
use App\Like;
use Exception;

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
            $post = Post::with('user')->orderBy('posts.updated_at', 'desc')->paginate(5);

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
    public function store(CreatePostRequest $request)
    {
        //
        $user = auth()->guard('api')->user();

        try{
            if (isset($request->validator) && $request->validator->fails()) {
                $error = $request->validator->messages();

                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $error
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

            $post = Post::with('user')->findOrFail($id);
            $comments = $post->comments()->latest()->get();
            $totalLikes = $post->likes()->count();
            $user = auth()->guard('api')->user();
            $hasLiked = $user ? intval($post->likes()->where('user_email', '=', $user->email)->exists()):0;
            $isAuthor = $user ? intval($post->user && ($post->user->email == $user->email)): 0;

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
    public function update(UpdatePostRequest $request, $id)
    {
        try{
            
            if (isset($request->validator) && $request->validator->fails()) {
                $error = $request->validator->messages();

                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $error
                ], 422);
            }

            $post = Post::findOrFail($id);
            $user = auth()->guard('api')->user();
            if( $post->user && $post->user->email != $user->email){
                throw new Exception('Not Author of The Post');
            }
            
            $post->fill(['title' => $request->title, 'content' => $request->content])->save();

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

            if( $post->user && $post->user->email != $user->email ){
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
            $like = $post->likes()->where('user_email', '=', Auth::user()->email)->first();

            if($like){
                $like->delete();
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

    public function comment(CommentPostRequest $request, $id){
        try{

            if (isset($request->validator) && $request->validator->fails()) {
                $error = $request->validator->messages();

                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $error
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
