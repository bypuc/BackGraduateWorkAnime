<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genres')->delete();

        $genres = [
            [
                'id' => 1,
                'name' => 'Романтика'
            ],
            [
                'id' => 2,
                'name' => 'Приключения'
            ],
            [
                'id' => 3,
                'name' => 'Экшен'
            ],
            [
                'id' => 4,
                'name' => 'Комедия'
            ],
            [
                'id' => 5,
                'name' => 'Драма'
            ],
            [
                'id' => 6,
                'name' => 'Ужасы'
            ],
            [
                'id' => 7,
                'name' => 'Повседневность'
            ],
            [
                'id' => 8,
                'name' => 'Мистика'
            ],
            [
                'id' => 9,
                'name' => 'Детектив'
            ],
            [
                'id' => 10,
                'name' => 'Триллер'
            ],
            [
                'id' => 11,
                'name' => 'Фантастика'
            ],
            [
                'id' => 12,
                'name' => 'Фэнтези'
            ],
        ];

        Genre::insert($genres);
    }
}
