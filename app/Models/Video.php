<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'perfume_id',
        'title',
        'video_url',
        'thumbnail_url',
        'duration',
        'views_count',
        'description',
        'placement',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'views_count' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function perfume(): BelongsTo
    {
        return $this->belongsTo(Perfume::class, 'perfume_id');
    }

    public function getThumbnailSrcAttribute(): string
    {
        if ($this->thumbnail_url) {
            return Str::startsWith($this->thumbnail_url, ['http://', 'https://'])
                ? $this->thumbnail_url
                : asset(ltrim($this->thumbnail_url, '/'));
        }

        if ($this->perfume && $this->perfume->image_src) {
            return $this->perfume->image_src;
        }

        return asset('images/perfume-default.jpg');
    }

    public function getFormattedViewsAttribute(): string
    {
        $views = (int) $this->views_count;
        if ($views >= 1000000) {
            return round($views / 1000000, 1) . 'M';
        }
        if ($views >= 1000) {
            return round($views / 1000, 1) . 'K';
        }
        return (string) $views;
    }

    /**
     * Chuyển đổi link YouTube (watch, youtu.be, shorts) sang Embed URL chuẩn
     */
    public function getEmbedUrlAttribute(): string
    {
        $url = trim((string) $this->video_url);

        // YouTube Shorts: https://www.youtube.com/shorts/VIDEO_ID
        if (preg_match('/(?:youtube\.com\/shorts\/|youtu\.be\/shorts\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0';
        }

        // YouTube watch: https://www.youtube.com/watch?v=VIDEO_ID
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0';
        }

        // TikTok: https://www.tiktok.com/@username/video/7123456789012345678
        if (preg_match('/tiktok\.com\/@[^\/]+\/video\/(\d+)/i', $url, $m)) {
            return 'https://www.tiktok.com/player/v1/' . $m[1];
        }

        // Đã là link embed sẵn
        if (Str::contains($url, ['youtube.com/embed/', 'tiktok.com/embed/', 'tiktok.com/player/'])) {
            return Str::contains($url, '?') ? $url . '&autoplay=1' : $url . '?autoplay=1';
        }

        return $url;
    }

    public function getIsYoutubeAttribute(): bool
    {
        return Str::contains($this->video_url, ['youtube.com', 'youtu.be']);
    }

    public function getIsTiktokAttribute(): bool
    {
        return Str::contains($this->video_url, 'tiktok.com');
    }

    public function getIsDirectVideoAttribute(): bool
    {
        return preg_match('/\.(mp4|webm|ogg)$/i', $this->video_url) === 1;
    }
}
