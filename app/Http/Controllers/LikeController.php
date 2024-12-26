<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    // like or unlike a post
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);

        if (! $post) {
            return response([
                'message' => 'Post not found',
            ], 404);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        if (! $like) {
            Like::create([
                'user_id' => auth()->user()->id,
                'post_id' => $id,
            ]);

            return response([
                'message' => 'Post liked',
            ], 200);
        }

        $like->delete();

        return response([
            'message' => 'Post unliked',
        ], 200);
    }
}
