<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enums\ItemEnum;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 200),
        ];
    }

    public function book(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => ItemEnum::BOOK->value,
            'details' => ['genre' => fake()->word()],
        ]);
    }

    public function comic(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => ItemEnum::COMIC->value,
            'details' => ['series' => fake()->word()],
        ]);
    }

    public function shortStoryCollection(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => ItemEnum::SHORT_STORY_COLLECTION->value,
            'details' => ['theme' => fake()->word()],
        ]);
    }
}
