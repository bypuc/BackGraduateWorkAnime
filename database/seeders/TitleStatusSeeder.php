<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TitleStatus;
use Illuminate\Support\Facades\DB;

class TitleStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('title_statuses')->delete();

        $statuses = [
            [
                'id' => 1,
                'name' => 'Онгоинг'
            ],
            [
                'id' => 2,
                'name' => 'Выпущено'
            ],
            [
                'id' => 3,
                'name' => 'Анонс'
            ],
        ];

        TitleStatus::insert($statuses);
    }
}
