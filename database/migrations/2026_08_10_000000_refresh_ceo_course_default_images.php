<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Replace only application-owned defaults. Any image uploaded or otherwise
     * selected by an administrator remains the source of truth.
     */
    public function up(): void
    {
        $this->replaceDefault(
            'advance-aesthetics-class',
            'assets/web/images/academy/academy-injectables.webp',
            'assets/web/images/academy/academy-advance-fillers.webp',
        );
        $this->replaceDefault(
            'master-class-one',
            'assets/web/images/hero/hero-body-care.webp',
            'assets/web/images/academy/academy-body-contouring.webp',
        );
        $this->replaceDefault(
            'master-class-two',
            'assets/web/images/academy/academy-training.webp',
            'assets/web/images/academy/academy-master-face.webp',
        );
    }

    public function down(): void
    {
        $this->restoreDefault(
            'advance-aesthetics-class',
            'assets/web/images/academy/academy-advance-fillers.webp',
            'assets/web/images/academy/academy-injectables.webp',
        );
        $this->restoreDefault(
            'master-class-one',
            'assets/web/images/academy/academy-body-contouring.webp',
            'assets/web/images/hero/hero-body-care.webp',
        );
        $this->restoreDefault(
            'master-class-two',
            'assets/web/images/academy/academy-master-face.webp',
            'assets/web/images/academy/academy-training.webp',
        );
    }

    private function replaceDefault(string $slug, string $oldDefault, string $newDefault): void
    {
        DB::table('courses')
            ->where('slug', $slug)
            ->where(fn ($query) => $query
                ->whereNull('image_path')
                ->orWhere('image_path', $oldDefault))
            ->update([
                'image_path' => $newDefault,
                'updated_at' => now(),
            ]);
    }

    private function restoreDefault(string $slug, string $currentDefault, string $oldDefault): void
    {
        DB::table('courses')
            ->where('slug', $slug)
            ->where('image_path', $currentDefault)
            ->update([
                'image_path' => $oldDefault,
                'updated_at' => now(),
            ]);
    }
};
