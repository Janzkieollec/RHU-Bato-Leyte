<?php

namespace App\Exports;

use App\Models\DiagnosisAnalytics;
use App\Models\Population;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FHSISReportExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;
    protected $totalPopulation;
    protected $reportTitle;
    protected $year;
    protected $selectedType;
    protected $quarterName;
    protected $reportType;

    public function __construct($data, $totalPopulation, $reportTitle, $year, $selectedType, $quarterName, $reportType)
    {
        $this->data = $data;
        $this->totalPopulation = $totalPopulation;
        $this->reportTitle = $reportTitle;
        $this->year = $year;
        $this->selectedType = $selectedType;
        $this->quarterName = $quarterName;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        // Structure the data in an array to match the Excel format
        $diagnosisAgeGroups = [];
        foreach ($this->data as $item) {
            if ($item->age !== null) {
                $diagnosisName = $item->diagnosis->diagnosis_name;
                if (!isset($diagnosisAgeGroups[$diagnosisName])) {
                    $diagnosisAgeGroups[$diagnosisName] = [
                        '0-9' => ['M' => 0, 'F' => 0],
                        '10-19' => ['M' => 0, 'F' => 0],
                        '20-59' => ['M' => 0, 'F' => 0],
                        '60+' => ['M' => 0, 'F' => 0],
                    ];
                }

                $gender = $item->gender_id == 1 ? 'M' : 'F';
                $ageGroup = match (true) {
                    $item->age <= 9 => '0-9',
                    $item->age <= 19 => '10-19',
                    $item->age <= 59 => '20-59',
                    default => '60+',
                };
                $diagnosisAgeGroups[$diagnosisName][$ageGroup][$gender]++;
            }
        }

        // Format data for export
        $exportData = [];
        foreach ($diagnosisAgeGroups as $diagnosis => $ageGroup) {
            $exportData[] = [
                'Disease' => $diagnosis,
                'ICD-10 Code' => $this->data->firstWhere('diagnosis.diagnosis_name', $diagnosis)->diagnosis->diagnosis_code ?? '',
                '0-9 M' => $ageGroup['0-9']['M'],
                '0-9 F' => $ageGroup['0-9']['F'],
                '10-19 M' => $ageGroup['10-19']['M'],
                '10-19 F' => $ageGroup['10-19']['F'],
                '20-59 M' => $ageGroup['20-59']['M'],
                '20-59 F' => $ageGroup['20-59']['F'],
                '60+ M' => $ageGroup['60+']['M'],
                '60+ F' => $ageGroup['60+']['F'],
                'Total M' => $ageGroup['0-9']['M'] + $ageGroup['10-19']['M'] + $ageGroup['20-59']['M'] +  $ageGroup['60+']['M'],
                'Total F' => $ageGroup['0-9']['F'] + $ageGroup['10-19']['F'] + $ageGroup['20-59']['F'] +  $ageGroup['60+']['F'],
                'Total Both Sex' => $ageGroup['0-9']['M'] + $ageGroup['0-9']['F'] + $ageGroup['10-19']['M'] + $ageGroup['10-19']['F'] + $ageGroup['20-59']['M'] + $ageGroup['20-59']['F'] + $ageGroup['60+']['M'] + $ageGroup['60+']['F']
            ];
        }

        return collect($exportData);
    }

    public function headings(): array
    {
        return [
            'Disease',
            'ICD-10 Code',
            '0-9 M',
            '0-9 F',
            '10-19 M',
            '10-19 F',
            '20-59 M',
            '20-59 F',
            '60+ M',
            '60+ F',
            'Total M',
            'Total F',
            'Total Both Sex'
        ];
    }

    public function title(): string
    {
        return "FHSIS Report {$this->reportType} - {$this->selectedType} {$this->year}";
    }
}