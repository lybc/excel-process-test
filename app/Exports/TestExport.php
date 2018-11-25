<?php

namespace App\Exports;

use Faker\Factory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class TestExport implements FromArray
{
    /**
     * @return array
     */
    public function array(): array
    {
        return generate_test_data();
    }
}
