<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A User has many posts
     *
     * @return void
     * @test
     */
    public function task_1_user_has_many_posts()
    {
        $user = User::factory()->create();

        $post = Post::factory(5)->create([
            'author_id' => $user->id,
        ]);

        $this->assertEquals(5,$user->posts->count());
    }

    /**
     * A post may have an author
     *
     * @return void
     * @test
     */
    public function task_2_a_blog_may_have_an_author()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'author_id' => $user->id,
        ]);

        $this->assertEquals($user->id ,$post->author->id);
    }

    /** @test */
    public function task_3_show_posts_by_author(){

        $user = User::factory()->create();

        $posts = Post::factory(5)->create([
            'author_id' => $user->id,
        ]);

        $response = $this->call('GET',route('user.posts',$user->id));

        $response->assertStatus(200);
        $response->assertSee($posts->first()->title);
    }

    /** @test */
    public function task_4_assert_not_too_many_queries(){

        $user = User::factory()->create();

        $posts = Post::factory(10)->create([
            'author_id' => $user->id,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $response = $this->call('GET',route('index'));
        $response->assertStatus(200);

        $this->assertCount(3,DB::getQueryLog());
    }
}
