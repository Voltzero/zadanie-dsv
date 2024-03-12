<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Enums\ItemEnum;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_empty_database_response_success(): void
    {
        $response = $this->getJson('/items');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [],
            ]);
    }

    public function test_items_returning_one_element_success(): void
    {
        $author = Author::factory()->createOne();
        $item = Item::factory()->for($author)->book()->createOne();

        $response = $this->getJson('/items');

        $expected = $item->toArray();
        $expected['price'] = number_format($expected['price'], 2);
        unset($expected['author_id']);
        $expected = [...$expected, 'author' => $author->fullName()];

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [$expected],
            ]);
    }

    public function test_items_list_assert_types_success(): void
    {
        $amount = fake()->numberBetween(2, 20);
        $authors = Author::factory()->count($amount)->create();

        $itemsAmount = 0;
        foreach ($authors as $index => $author) {
            $factory = Item::factory()->count(fake()->numberBetween(1, 10))->for($author);
            $created = match ($index % 3) {
                1 => $factory->comic()->create(),
                2 => $factory->shortStoryCollection()->create(),
                default => $factory->book()->create(),
            };
            $itemsAmount += $created->count();
        }

        $response = $this->getJson('/items');

        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('data', 'array')
                ->has('data', $itemsAmount, fn(AssertableJson $data) => $data
                    ->whereType('id', 'integer')
                    ->whereType('title', 'string')
                    ->whereType('type', 'string')
                    ->whereType('price', 'string')
                    ->whereType('author', 'string')
                    ->whereType('details', 'array')
                    ->whereType('created_at', 'string')
                    ->whereType('updated_at', 'string')
                    ->has('details', 1)
                )
            );
    }

    public function test_items_description_success(): void
    {
        /** @var Author $author */
        $author = Author::factory()->createOne();
        $item = Item::factory()->for($author)->book()->createOne();

        $response = $this->getJson("/items/$item->id/description");

        $genre = $item->details['genre'];
        $price = number_format($item->price, 2);
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'description' => "title: $item->title, type: $item->type, price: $price, author: $author->first_name $author->last_name, $author->place_of_birth, genre: $genre"
            ]);
    }

    #[DataProvider('itemDataSuccessProvider')]
    public function test_items_creation_success(array $data): void
    {
        $authorId = Author::factory()->createOne()->id;
        $data['author_id'] = $authorId;

        $response = $this->postJson('/items', $data);
        $response
            ->assertStatus(201);
    }

    public static function itemDataSuccessProvider(): array
    {
        return [
            'create book' => [[
                'title' => fake()->words(3, true),
                'price' => number_format(fake()->randomFloat(2, 10, 200), 2),
                'type' => ItemEnum::BOOK->value,
                'details' => ['genre' => fake()->word()],
            ]],
            'create comic' => [[
                'title' => fake()->words(3, true),
                'price' => number_format(fake()->randomFloat(2, 10, 200), 2),
                'type' => ItemEnum::COMIC->value,
                'details' => ['series' => fake()->word()],
            ]],
            'create short story collection' => [[
                'title' => fake()->words(3, true),
                'price' => number_format(fake()->randomFloat(2, 10, 200), 2),
                'type' => ItemEnum::SHORT_STORY_COLLECTION->value,
                'details' => ['theme' => fake()->word()],
            ]],
        ];
    }

    #[DataProvider('itemDataFailProvider')]
    public function test_items_creation_failed(array $data, array $messages): void
    {
        $authorId = Author::factory()->createOne()->id;

        // zmieniam author_id na poprawne tylko wtedy gdy potrzebne
        if (isset($data['author_id']) && is_bool($data['author_id'])) {
            $data['author_id'] = $authorId;
        }

        $response = $this->postJson('/items', $data);
        $response
            ->assertStatus(422)
            ->assertExactJson($messages);
    }

    public static function itemDataFailProvider(): array
    {
        return [
            'empty request' => [
                [],
                [
                    'message' => 'The title field is required. (and 4 more errors)',
                    'errors' => [
                        "title" => [
                            "The title field is required."
                        ],
                        "price" => [
                            "The price field is required."
                        ],
                        "type" => [
                            "The type field is required."
                        ],
                        "author_id" => [
                            "The author id field is required."
                        ],
                        "details" => [
                            "The details field is required."
                        ]
                    ]
                ]
            ],
            'title only' => [
                [
                    'title' => fake()->words(3, true),
                ],
                [
                    'message' => 'The price field is required. (and 3 more errors)',
                    'errors' => [
                        "price" => [
                            "The price field is required."
                        ],
                        "type" => [
                            "The type field is required."
                        ],
                        "author_id" => [
                            "The author id field is required."
                        ],
                        "details" => [
                            "The details field is required."
                        ]
                    ]
                ]
            ],
            'wrong data types' => [
                [
                    'title' => fake()->numberBetween(1, 10),
                    'price' => fake()->numberBetween(1, 10),
                    'type' => 'wrong',
                    'author_id' => fake()->word(),
                    'details' => fake()->numberBetween(1, 10),
                ],
                [
                    'message' => 'The title field must be a string. (and 5 more errors)',
                    'errors' => [
                        "title" => [
                            "The title field must be a string."
                        ],
                        "price" => [
                            "The price field must have 2 decimal places."
                        ],
                        "type" => [
                            "The selected type is invalid."
                        ],
                        "author_id" => [
                            "The selected author id is invalid."
                        ],
                        "details" => [
                            "The details field must be an array.",
                            "The details is not correct for given type."
                        ]
                    ]
                ]
            ],
            'wrong type' => [
                [
                    'type' => 'wrong',
                ],
                [
                    'message' => 'The title field is required. (and 4 more errors)',
                    'errors' => [
                        "title" => [
                            "The title field is required."
                        ],
                        "price" => [
                            "The price field is required."
                        ],
                        "type" => [
                            "The selected type is invalid."
                        ],
                        "author_id" => [
                            "The author id field is required."
                        ],
                        "details" => [
                            "The details field is required."
                        ]
                    ]
                ]
            ],
            'wrong book details' => [
                [
                    'title' => fake()->words(3, true),
                    'price' => number_format(fake()->randomFloat(2, 10, 200), 2),
                    'type' => ItemEnum::BOOK->value,
                    'details' => ['series' => fake()->word()],
                    'author_id' => true,
                ],
                [
                    'message' => 'The genre field is required.',
                    'errors' => [
                        "details" => [
                            "The genre field is required."
                        ]
                    ]
                ]
            ],
            'wrong comic details' => [
                [
                    'title' => fake()->words(3, true),
                    'price' => number_format(fake()->randomFloat(2, 10, 200), 2),
                    'type' => ItemEnum::COMIC->value,
                    'details' => ['theme' => fake()->word()],
                    'author_id' => true,
                ],
                [
                    'message' => 'The series field is required.',
                    'errors' => [
                        "details" => [
                            "The series field is required."
                        ]
                    ]
                ]
            ],
            'wrong short story collection details' => [
                [
                    'title' => fake()->words(3, true),
                    'price' => number_format(fake()->randomFloat(2, 10, 200), 2),
                    'type' => ItemEnum::SHORT_STORY_COLLECTION->value,
                    'details' => ['series' => fake()->word()],
                    'author_id' => true,
                ],
                [
                    'message' => 'The theme field is required.',
                    'errors' => [
                        "details" => [
                            "The theme field is required."
                        ]
                    ]
                ]
            ],
        ];
    }
}
