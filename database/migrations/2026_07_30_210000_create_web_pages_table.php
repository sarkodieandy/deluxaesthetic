<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('route_name')->unique();
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('hero_eyebrow')->nullable();
            $table->text('hero_title')->nullable();
            $table->text('hero_body')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->json('sections')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $now = now();
        DB::table('web_pages')->insert(collect([
            ['Home', 'home', 'web.home'],
            ['About', 'about', 'web.about'],
            ['Treatments', 'treatments', 'web.treatments.index'],
            ['Practitioners', 'practitioners', 'web.practitioners.index'],
            ['Academy', 'academy', 'web.academy.index'],
            ['Courses', 'courses', 'web.courses.index'],
            ['Store', 'store', 'web.store.index'],
            ['Gallery', 'gallery', 'web.gallery'],
            ['Blog', 'blog', 'web.blog.index'],
            ['Contact', 'contact', 'web.contact'],
        ])->map(fn ($page) => [
            'name' => $page[0],
            'slug' => $page[1],
            'route_name' => $page[2],
            'is_published' => true,
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('web_pages');
    }
};
