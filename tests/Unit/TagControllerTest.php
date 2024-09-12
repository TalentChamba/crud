<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function guests_can_view_tags_index()
    {
        $tag = Tag::factory()->create();
        $response = $this->get(route('tags.index'));
        $response->assertStatus(200);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function guests_cannot_create_tags()
    {
        $response = $this->post(route('tags.store'), ['name' => 'New Tag']);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_create_tags()
    {
        $response = $this->actingAs($this->user)
            ->post(route('tags.store'), ['name' => 'New Tag']);

        $response->assertRedirect(route('tags.index'));
        $this->assertDatabaseHas('tags', ['name' => 'New Tag']);
    }

    /** @test */
    public function tag_names_must_be_unique()
    {
        Tag::factory()->create(['name' => 'Existing Tag']);

        $response = $this->actingAs($this->user)
            ->post(route('tags.store'), ['name' => 'Existing Tag']);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function guests_can_view_tag_details()
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create(['is_published' => true]);
        $tag->posts()->attach($post);

        $response = $this->get(route('tags.show', $tag));
        $response->assertStatus(200);
        $response->assertSee($tag->name);
        $response->assertSee($post->title);
    }

    /** @test */
    public function guests_cannot_edit_tags()
    {
        $tag = Tag::factory()->create();
        $response = $this->get(route('tags.edit', $tag));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_edit_tags()
    {
        $tag = Tag::factory()->create();
        $response = $this->actingAs($this->user)
            ->put(route('tags.update', $tag), ['name' => 'Updated Tag']);

        $response->assertRedirect(route('tags.index'));
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Updated Tag']);
    }

    /** @test */
    public function guests_cannot_delete_tags()
    {
        $tag = Tag::factory()->create();
        $response = $this->delete(route('tags.destroy', $tag));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_delete_tags()
    {
        $tag = Tag::factory()->create();
        $response = $this->actingAs($this->user)
            ->delete(route('tags.destroy', $tag));

        $response->assertRedirect(route('tags.index'));
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
