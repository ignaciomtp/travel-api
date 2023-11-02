<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Travel;

class TravelsListTest extends TestCase
{

    use RefreshDatabase; // Con cada test laravel ejecutará migrate:fresh 


    public function test_travels_list_returns_paginated_data_correctly(): void
    {

        Travel::factory(16)->create(['is_public' => true]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonPath('meta.last_page', 2);
    }


    public function test_travels_list_shows_only_public_records(): void
    {

        Travel::factory(1)->create(['is_public' => true]);
        Travel::factory(1)->create(['is_public' => false]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        
    }
}
