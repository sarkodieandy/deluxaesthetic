<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AcademyShowcaseItem extends Model
{
    public const TYPES = [
        'training_step' => 'Training format step',
        'student_story' => 'Past student story',
        'skill_review' => 'Student skill review',
        'training_country' => 'Training country',
        'student_video' => 'Student video',
        'certification' => 'Certification',
        'experience' => 'Training experience',
        'career_benefit' => 'Career support benefit',
    ];

    protected $fillable = [
        'type', 'title', 'subtitle', 'body', 'image_path', 'video_url',
        'sort_order', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) return null;
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) return $this->image_path;
        return Storage::disk('public')->exists($this->image_path) ? asset('storage/'.$this->image_path) : null;
    }

    public function videoEmbedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $parts = parse_url($this->video_url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $host = preg_replace('/^www\./', '', $host);
        $path = trim((string) ($parts['path'] ?? ''), '/');

        $youtubeId = null;
        if ($host === 'youtu.be') {
            $youtubeId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'youtube-nocookie.com'], true)) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            $segments = array_values(array_filter(explode('/', $path)));
            $youtubeId = $query['v'] ?? null;

            if (! $youtubeId && in_array($segments[0] ?? null, ['embed', 'shorts', 'live'], true)) {
                $youtubeId = $segments[1] ?? null;
            }
        }

        if (is_string($youtubeId) && preg_match('/^[A-Za-z0-9_-]{6,20}$/', $youtubeId)) {
            return 'https://www.youtube-nocookie.com/embed/'.$youtubeId;
        }

        if (in_array($host, ['vimeo.com', 'player.vimeo.com'], true)) {
            foreach (array_reverse(array_filter(explode('/', $path))) as $segment) {
                if (ctype_digit($segment)) {
                    return 'https://player.vimeo.com/video/'.$segment;
                }
            }
        }

        return null;
    }
}
