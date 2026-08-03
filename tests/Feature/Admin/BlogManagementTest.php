<?php

namespace Tests\Feature\Admin;

use App\Models\BlogPost;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->admin->assignRole('Super Administrator');
    }

    public function test_admin_can_create_publish_update_and_delete_article(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('admin.blog.store'), [
            'title' => 'How to care for your skin after treatment',
            'category' => 'Aftercare',
            'excerpt' => 'Simple clinical aftercare guidance for healthy, comfortable skin.',
            'body' => str_repeat('Follow the treatment plan provided by your practitioner. ', 3),
            'cover_image' => UploadedFile::fake()->image('aftercare.jpg', 1200, 800),
            'status' => 'published',
            'is_featured' => '1',
            'seo_title' => 'Aesthetic treatment aftercare in Accra',
            'seo_description' => 'Clinical aftercare guidance from De Luxe Aesthetic Clinic in Accra.',
        ])->assertRedirect();

        $post = BlogPost::firstOrFail();
        $this->assertSame($this->admin->id, $post->author_id);
        $this->assertTrue($post->isPublic());
        Storage::disk('public')->assertExists($post->cover_image_path);

        $this->get(route('web.blog.index'))->assertOk()->assertSee($post->title);
        $this->get(route('web.blog.show', $post))->assertOk()->assertSee($post->body);

        $this->actingAs($this->admin)->put(route('admin.blog.update', $post), [
            'title' => 'Updated skin aftercare guide',
            'category' => 'Skin health',
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'status' => 'draft',
        ])->assertRedirect();

        $post->refresh();
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
        $this->get(route('web.blog.show', $post))->assertNotFound();
        $this->actingAs($this->admin)->get(route('web.blog.show', ['post' => $post, 'preview' => 1]))->assertOk();

        $imagePath = $post->cover_image_path;
        $this->actingAs($this->admin)->delete(route('admin.blog.destroy', $post))->assertRedirect(route('admin.blog.index'));
        $this->assertSoftDeleted($post);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_scheduled_and_draft_articles_are_not_public(): void
    {
        BlogPost::create([
            'author_id' => $this->admin->id,
            'title' => 'Future article', 'slug' => 'future-article', 'excerpt' => 'Coming soon.',
            'body' => str_repeat('Useful clinical guidance. ', 4), 'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('web.blog.index'))->assertOk()->assertDontSee('Future article');
        $this->get('/blog/future-article')->assertNotFound();
    }

    public function test_non_blog_staff_cannot_manage_articles(): void
    {
        $student = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $student->assignRole('Student');

        $this->actingAs($student)->get('/admin/blog')->assertForbidden();
    }
}
