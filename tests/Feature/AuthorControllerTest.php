<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authors_empty_database_response_success(): void
    {
        $response = $this->getJson('/authors');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [],
            ]);
    }

    public function test_authors_returning_one_element_success(): void
    {
        $authorData = Author::factory()->makeOne()->toArray();
        Author::factory()->createOne($authorData);

        $response = $this->getJson('/authors');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [$authorData],
            ]);
    }

    public function test_authors_list_assert_types_success(): void
    {
        $amount = fake()->numberBetween(2, 20);
        Author::factory()->count($amount)->create();

        $response = $this->getJson('/authors');

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('data', 'array')
                ->has('data', $amount, fn(AssertableJson $data) => $data
                    ->whereType('first_name', 'string')
                    ->whereType('last_name', 'string')
                    ->whereType('place_of_birth', 'string')
                )
            );
    }
}
