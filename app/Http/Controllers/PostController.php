<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function storePosts(Request $request) {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'number' => 'required|numeric',
            'text' => 'required',
        ]);

        $post = new Post;
        $post->title = $validatedData['title'];
        $post->number = $validatedData['number'];
        $post->text = $validatedData['text'];
        $post->user_id = Auth::id();
        $post->save();

        return response()->json(['message' => 'Post created successfully'], 200);
    }

    public function getPosts() {
        $posts = Post::where('user_id', Auth::id())->latest()->get();

        return response()->json($posts, 200);
    }

    public function getPost(int $id) {

        $post = Post::findOrFail($id);

        return response()->json($post, 200);
    }

    public function updatePost(Request $request, $id) {
        $post = Post::findOrFail($id);

        $post->title = $request->input('title');
        $post->number = $request->input('number');
        $post->text = $request->input('text');

        $post->save();

        return response()->json($post, 200);
    }

    public function destroy(int $id) {
        $post = Post::findOrFail($id);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
