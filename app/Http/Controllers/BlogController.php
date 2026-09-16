<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\AppSetting;
use App\Models\BlogPost;
use App\Support\PageHtml;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $app = AppSetting::first() ?? (object) [
            'title' => config('app.name', 'LinkProfilde'),
            'description' => 'Dijital profil, kısa link, QR kod ve analitik rehberleri.',
            'logo' => 'assets/icons/link-drop.png',
        ];

        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('blog.index', compact('app', 'posts'));
    }

    public function show(string $slug)
    {
        $app = AppSetting::first() ?? (object) [
            'title' => config('app.name', 'LinkProfilde'),
            'description' => 'Dijital profil, kısa link, QR kod ve analitik rehberleri.',
            'logo' => 'assets/icons/link-drop.png',
        ];

        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $safeContent = PageHtml::sanitize($post->content);
        $related = BlogPost::published()
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('app', 'post', 'safeContent', 'related'));
    }

    public function adminIndex()
    {
        $posts = BlogPost::query()->orderByDesc('created_at')->paginate(30);

        return Inertia::render('Admin/Blog/Show', compact('posts'));
    }

    public function adminCreate()
    {
        return Inertia::render('Admin/Blog/Edit', ['post' => null]);
    }

    public function adminStore(Request $request)
    {
        $data = $this->validatedPost($request);

        try {
            BlogPost::create($data);

            return redirect()->route('admin.blog.index')->with('success', 'Blog yazısı oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with('error', AppHelper::publicExceptionMessage($th, 'Blog yazısı oluşturulamadı.'));
        }
    }

    public function adminEdit(int $id)
    {
        $post = BlogPost::findOrFail($id);

        return Inertia::render('Admin/Blog/Edit', compact('post'));
    }

    public function adminUpdate(Request $request, int $id)
    {
        $post = BlogPost::findOrFail($id);
        $data = $this->validatedPost($request, $post->id);

        try {
            $post->update($data);

            return redirect()->route('admin.blog.index')->with('success', 'Blog yazısı güncellendi.');
        } catch (\Throwable $th) {
            return back()->with('error', AppHelper::publicExceptionMessage($th, 'Blog yazısı güncellenemedi.'));
        }
    }

    public function adminDelete(int $id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();

        return back()->with('success', 'Blog yazısı silindi.');
    }

    private function validatedPost(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:190',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_posts', 'slug')->ignore($ignoreId),
            ],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:300000'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'focus_keyword' => ['nullable', 'string', 'max:160'],
            'author_name' => ['nullable', 'string', 'max:120'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['title']);
        if ($slug === '') {
            throw ValidationException::withMessages(['slug' => 'Geçerli bir URL yolu oluşturulamadı.']);
        }

        if (BlogPost::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            throw ValidationException::withMessages(['slug' => 'Bu blog URL yolu zaten kullanılıyor.']);
        }

        $content = PageHtml::sanitize($data['content']);
        if ($content === '') {
            throw ValidationException::withMessages(['content' => 'Yazı içeriği boş veya geçersiz.']);
        }

        $published = (bool) ($data['is_published'] ?? false);

        return [
            'title' => trim($data['title']),
            'slug' => strtolower($slug),
            'excerpt' => trim($data['excerpt']),
            'content' => $content,
            'meta_title' => trim((string) ($data['meta_title'] ?? '')) ?: null,
            'meta_description' => trim((string) ($data['meta_description'] ?? '')) ?: null,
            'focus_keyword' => trim((string) ($data['focus_keyword'] ?? '')) ?: null,
            'author_name' => trim((string) ($data['author_name'] ?? '')) ?: 'LinkProfilde Editör Ekibi',
            'is_published' => $published,
            'published_at' => $published ? ($data['published_at'] ?? now()) : ($data['published_at'] ?? null),
        ];
    }
}
