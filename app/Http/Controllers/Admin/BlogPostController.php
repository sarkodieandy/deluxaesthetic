<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\SaveBlogPostRequest;
use App\Models\BlogPost;
use App\Support\GalleryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = BlogPost::query()->with('author:id,name')
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($inner) => $inner
                ->where('title', 'like', '%'.$request->string('q')->trim().'%')
                ->orWhere('excerpt', 'like', '%'.$request->string('q')->trim().'%')))
            ->when(in_array($request->input('status'), ['draft', 'published'], true),
                fn ($query) => $query->where('status', $request->input('status')))
            ->latest('updated_at')->paginate(15)->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog.create');
    }

    public function store(SaveBlogPostRequest $request): RedirectResponse
    {
        $post = BlogPost::create($this->payload($request, null) + ['author_id' => $request->user()->id]);

        return redirect()->route('admin.blog.edit', $post)->with('status', 'Blog post created successfully.');
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    public function update(SaveBlogPostRequest $request, BlogPost $blog): RedirectResponse
    {
        $blog->update($this->payload($request, $blog));

        return redirect()->route('admin.blog.edit', $blog)->with('status', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $this->deleteLocalImage($blog->cover_image_path);
        $blog->delete();

        return redirect()->route('admin.blog.index')->with('status', 'Blog post deleted.');
    }

    private function payload(SaveBlogPostRequest $request, ?BlogPost $post): array
    {
        $data = $request->validated();
        $imagePath = $post?->cover_image_path;

        if ($request->hasFile('cover_image')) {
            $this->deleteLocalImage($imagePath);
            $imagePath = $this->storeImage($request->file('cover_image'));
        } elseif ($url = GalleryMedia::normalizeUrl($data['cover_image_url'] ?? null)) {
            if ($url !== $imagePath) {
                $this->deleteLocalImage($imagePath);
                $imagePath = $url;
            }
        }

        $publishedAt = $data['status'] === 'published'
            ? ($data['published_at'] ?? $post?->published_at ?? now())
            : null;

        return [
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title'], $post),
            'category' => $data['category'] ?: null,
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'cover_image_path' => $imagePath,
            'cover_image_alt' => ($data['cover_image_alt'] ?? null) ?: $data['title'],
            'status' => $data['status'],
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $publishedAt,
            'seo_title' => ($data['seo_title'] ?? null) ?: null,
            'seo_description' => ($data['seo_description'] ?? null) ?: null,
        ];
    }

    private function storeImage(UploadedFile $image): string
    {
        return $image->store('blog', 'public');
    }

    private function deleteLocalImage(?string $path): void
    {
        if ($path && ! GalleryMedia::isRemoteUrl($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function uniqueSlug(string $title, ?BlogPost $post): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $suffix = 2;

        while (BlogPost::withTrashed()->where('slug', $slug)
            ->when($post, fn ($query) => $query->whereKeyNot($post->getKey()))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
