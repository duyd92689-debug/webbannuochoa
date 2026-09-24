<?php

namespace Tests\Feature;

use App\Models\Perfume;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVideoTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_videos_list(): void
    {
        $admin = $this->createAdmin();
        $video = Video::create([
            'title' => 'Review Miss Dior',
            'video_url' => 'https://www.youtube.com/watch?v=kYv9bH8F688',
            'placement' => 'home',
            'views_count' => 5000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.videos.index'));
        $response->assertStatus(200);
        $response->assertSee('Review Miss Dior');
    }

    public function test_admin_can_create_video(): void
    {
        $admin = $this->createAdmin();
        $perfume = Perfume::create([
            'name' => 'Chanel Bleu',
            'slug' => 'chanel-bleu',
            'brand' => 'Chanel',
            'gender' => 'nam',
            'volume_ml' => 100,
            'price' => 3200000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.videos.store'), [
            'title' => 'Bleu de Chanel 8 Giờ',
            'video_url' => 'https://www.youtube.com/watch?v=oG-nnDlnDDg',
            'perfume_id' => $perfume->id,
            'placement' => 'all',
            'duration' => '0:50',
            'views_count' => 18200,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.videos.index'));
        $this->assertDatabaseHas('videos', [
            'title' => 'Bleu de Chanel 8 Giờ',
            'perfume_id' => $perfume->id,
        ]);
    }

    public function test_admin_can_toggle_and_delete_video(): void
    {
        $admin = $this->createAdmin();
        $video = Video::create([
            'title' => 'Test Video Toggle',
            'video_url' => 'https://www.youtube.com/watch?v=test',
            'placement' => 'all',
            'is_active' => true,
        ]);

        // Toggle
        $this->actingAs($admin)->post(route('admin.videos.toggle', $video));
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'is_active' => false,
        ]);

        // Delete
        $this->actingAs($admin)->delete(route('admin.videos.destroy', $video));
        $this->assertDatabaseMissing('videos', [
            'id' => $video->id,
        ]);
    }
}
