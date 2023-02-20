<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Holiday::insert($this->getExamples());
    }

    public function getExamples()
    {
        return [
            [
                'day' => 3, 
                'month' => 2,
            ],
            [
                'day' => 31, 
                'month' => 3,
            ],
            [
                'day' => 21, 
                'month' => 7,
            ],
            [
                'day' => 1, 
                'month' => 12,
            ],
            [
                'day' => 20, 
                'month' => 12,
            ],
        ];
    }
}
