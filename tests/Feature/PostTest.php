<?php

namespace Tests\Feature;


use Tests\TestCase;

class PostTest extends TestCase
{


    /** @test */
    public function a_post_can_be_stored()
    {
        $this->withoutExceptionHandling();

        $data = [
            'title'=>'Some title',
            'description'=>'Description',
            'image'=>'123'
        ];
        $response = $this->post('/posts', $data);

        $response->assertOk();
    }
}
