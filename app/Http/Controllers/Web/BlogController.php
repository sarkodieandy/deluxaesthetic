<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = BlogPost::query()->published()
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
            ->orderByDesc('is_featured')->latest('published_at')->paginate(9)->withQueryString();
        $categories = BlogPost::query()->published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('web.blog.index', compact('posts', 'categories'));
    }

    public function show(Request $request, BlogPost $post): View
    {
        $canPreview = $request->boolean('preview') && $request->user()?->can('blog.manage');
        abort_unless($post->isPublic() || $canPreview, 404);

        $related = BlogPost::query()->published()->whereKeyNot($post->id)
            ->when($post->category, fn ($query) => $query->where('category', $post->category))
            ->latest('published_at')->limit(3)->get();

        return view('web.blog.show', compact('post', 'related', 'canPreview'));
    }
}
