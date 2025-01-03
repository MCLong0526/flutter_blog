<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get all comments of a post
    public function index($postId)
    {
        $post = Post::find($postId);

        if (! $post) {
            return response([
                'message' => 'Post not found',
            ], 404);
        }

        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get(),
        ], 200);
    }

    //store acomment
    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if (! $post) {
            return response([
                'message' => 'Post not found',
            ], 404);
        }

        //validate fields
        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'user_id' => auth()->user()->id,
            'post_id' => $id,
        ]);

        return response([
            'message' => 'Comment created',
        ], 200);
    }

    //update a comment
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response([
                'message' => 'Comment not found',
            ], 404);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied',
            ], 403);
        }

        //validate fields
        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $attrs['comment'],
        ]);

        return response([
            'message' => 'Comment updated',
        ], 200);
    }

    //delete a comment
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (! $comment) {
            return response([
                'message' => 'Comment not found',
            ], 404);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied',
            ], 403);
        }

        $comment->delete();

        return response([
            'message' => 'Comment deleted',
        ], 200);
    }
}
