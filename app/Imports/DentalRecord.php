<?php

namespace App\Imports;

use App\Models\Dental;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DentalRecord implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 6; // Skip the first 5 rows
    }

    public function model(array $row)
    {
        $createdAt = Date::excelToDateTimeObject($row[8])->format('Y-m-d H:i:s');

        return new Dental([
            'patient_id' => $row[0],
            'blood_pressure' => $row[1],
            'body_temperature' => $row[2],
            'height' => $row[3],
            'weight' => $row[4],
            'chief_complaints' => $row[5],
            'number_of_days' => $row[6],
            'emergency_purposes' => $row[7],
            'created_at' => $row[8],
        ]);
    }
}