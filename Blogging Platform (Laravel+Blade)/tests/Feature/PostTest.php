<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $admin = User::factory()->admin()->create();

        $post = Post::factory()->create(['author_id' => $admin->id]);
        Post::factory()->count(2)->create();

        $this->get('/')
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee(humanize_date($post->posted_at))
            ->assertSee('3')
            ->assertSee('admin');
    }

    public function testSearch()
    {
        Post::factory()->count(3)->create();
        $post = Post::factory()->create(['title' => 'Hello World']);

        $this->get('/?q=Hello')
            ->assertOk()
            // ->assertSee('1 post found')
            ->assertSee($post->title)
            ->assertSee(humanize_date($post->posted_at));
    }

    public function testShow()
    {
        $post = Post::factory()->create();

        $this->actingAsUser()
            ->get("/posts/{$post->slug}")
            ->assertOk()
            ->assertSee($post->content)
            ->assertSee($post->title)
            ->assertSee(humanize_date($post->posted_at));
    }
}
