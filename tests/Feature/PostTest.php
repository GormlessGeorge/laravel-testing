<?php

namespace Tests\Feature;


use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function a_post_can_be_stored()
    {
        $this->withoutExceptionHandling();


        $file = File::create('my_image.jpg');


        $data = [
            'title' => 'Some title',
            'description' => 'Description',
            'image' => $file
        ];


        $response = $this->post('/posts', $data);

        $response->assertOk();

        $this->assertDatabaseCount('posts', 1);

        $post = Post::first();

        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals('images/' . $file->hashName(), $post->image_url);

        Storage::disk('local')->assertExists($post->image_url);
    }

    /** @test */
    public function attribute_title_is_required_for_storing_post()
    {
//        $this->withoutExceptionHandling();

        $data = [
            'title' => '',
            'description' => 'Description',
            'image' => ''
        ];

        $response = $this->post('/posts', $data);

        $response->assertRedirect();
        $response->assertInvalid('title');
    }

    /** @test */
    public function attribute_image_is_file_for_storing_post()
    {
//        $this->withoutExceptionHandling();

        $data = [
            'title' => '',
            'description' => 'Description',
            'image' => 'wadaf'
        ];

        $response = $this->post('/posts', $data);

        $response->assertRedirect();
        $response->assertInvalid('image');
    }


    /** @test */
    public function a_post_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create();
        $file = File::create('image.jpg');
        $data = [
            'title' => 'Title Edited',
            'description' => 'Description edited',
            'image' => $file
        ];

        $response = $this->patch('/posts/' . $post->id, $data);
        $response->assertOk();
        $updatedPost = Post::first();
        $this->assertEquals($data['title'], $updatedPost->title);
        $this->assertEquals($data['description'], $updatedPost->description);
        $this->assertEquals('images/' . $file->hashName(), $updatedPost->image_url);

        $this->assertEquals($post->id, $updatedPost->id);
    }

    /** @test */

    public function response_for_route_posts_index_is_view_post_index_with_posts()
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory(10)->create();

        $res = $this->get('/posts');

        $res->assertViewIs('posts.index');

        $res->assertSeeText('View page');

        $titles = $posts->pluck('title')->toArray();
        $res->assertSeeText($titles);
    }

    /** @test */
    public function response_for_route_posts_show_is_view_post_show_with_single_post()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create();

        $res = $this->get('/posts/'. $post->id);

        $res->assertViewIs('posts.show');
        $res->assertSeeText('Show page');
        $res->assertSeeText($post->title);
        $res->assertSeeText($post->description);



    }
}
