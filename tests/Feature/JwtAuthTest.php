<?php

namespace Dwivedianuj9118\ApiStarter\Tests\Feature;

use Dwivedianuj9118\ApiStarter\Tests\TestCase;

class JwtAuthTest extends TestCase
{
    /** @test */
    public function health_endpoint_works()
    {
        $this->get('/api/v1/health')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);
    }
}
