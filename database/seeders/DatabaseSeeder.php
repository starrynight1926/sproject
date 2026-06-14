<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(Company2026Seeder::class);
        $this->call(ExcelFormulaSeeder::class);
    }
}
