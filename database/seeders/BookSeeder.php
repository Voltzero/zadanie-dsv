<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::factory()
            ->count(fake()->numberBetween(1, 20))
            ->book()
            ->for(Author::factory(1)->createOne())
            ->create();
    }
}
