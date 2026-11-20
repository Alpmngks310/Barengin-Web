<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    public function test_api_health_endpoint_returns_json(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Barengin API is running.');
    }
}
