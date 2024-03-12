<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::factory()
            ->count(fake()->numberBetween(10, 50))
            ->comic()
            ->for(Author::factory(1)->createOne())
            ->create();
    }
}
