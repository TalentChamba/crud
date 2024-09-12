<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function guests_can_view_published_posts()
    {
        $post = Post::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);
        $response = $this->get(route('posts.index'));
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    /** @test */
    public function guests_cannot_view_unpublished_posts()
    {
        $post = Post::factory()->create(['is_published' => false]);
        $response = $this->get(route('posts.show', $post));
        $response->assertStatus(404);
    }

    /** @test */
    public function guests_cannot_create_posts()
    {
        $response = $this->post(route('posts.store'), []);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_create_posts()
    {
        $postData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'is_published' => true,
            'published_at' => now(),
        ];

        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), $postData);

        $response->assertRedirect(route('posts.show', 1));
        $this->assertDatabaseHas('posts', ['title' => $postData['title']]);
    }

    /** @test */
    public function users_can_add_tags_to_posts()
    {
        $tag = Tag::factory()->create();
        $postData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'is_published' => true,
            'published_at' => now(),
            'tags' => [$tag->id],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), $postData);

        $this->assertDatabaseHas('post_tag', ['tag_id' => $tag->id, 'post_id' => 1]);
    }

    /** @test */
    public function users_can_update_their_own_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $updatedData = ['title' => 'Updated Title', 'content' => 'Updated content', 'is_published' => true];

        $response = $this->actingAs($this->user)
            ->put(route('posts.update', $post), $updatedData);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Title']);
    }

    /** @test */
    public function users_cannot_update_others_posts()
    {
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);
        $updatedData = ['title' => 'Updated Title', 'content' => 'Updated content', 'is_published' => true];

        $response = $this->actingAs($this->user)
            ->put(route('posts.update', $post), $updatedData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_update_any_post()
    {
        $post = Post::factory()->create();
        $updatedData = ['title' => 'Admin Updated', 'content' => 'Updated by admin', 'is_published' => true];

        $response = $this->actingAs($this->admin)
            ->put(route('posts.update', $post), $updatedData);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Admin Updated']);
    }

    /** @test */
    public function users_can_delete_their_own_posts()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function users_cannot_delete_others_posts()
    {
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('posts.destroy', $post));

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_delete_any_post()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
