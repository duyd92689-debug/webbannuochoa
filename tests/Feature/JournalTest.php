<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_visitors_can_read_published_articles_but_not_drafts(): void
    {
        $article = Article::where('slug', 'hieu-ba-tang-huong')->firstOrFail();
        $this->get(route('store.journal'))->assertOk()->assertSee($article->title);
        $this->get(route('store.article', $article->slug))->assertOk()->assertSee('Hương đầu');
        $article->update(['is_published' => false]);
        $this->get(route('store.article', $article->slug))->assertNotFound();
    }

    public function test_admin_can_publish_and_edit_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Chọn hương mùa hè', 'excerpt' => 'Cách tìm mùi hương dễ chịu cho ngày nóng.',
            'body' => str_repeat('Một đoạn hướng dẫn chọn mùi tươi mát cho mùa hè. ', 3),
            'is_published' => 1,
        ])->assertRedirect(route('admin.articles.index'));
        $article = Article::where('slug', 'chon-huong-mua-he')->firstOrFail();
        $this->get(route('store.article', $article->slug))->assertOk();
        $this->actingAs($admin)->put(route('admin.articles.update', $article), [
            'title' => 'Chọn hương ngày hè', 'excerpt' => $article->excerpt,
            'body' => $article->body, 'is_published' => 1,
        ])->assertRedirect(route('admin.articles.index'));
        $this->assertSame('chon-huong-ngay-he', $article->fresh()->slug);
    }
}
