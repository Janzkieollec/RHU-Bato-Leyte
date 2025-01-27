<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FHSIS Report</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .container {
        width: 100%;
        max-width: 800px;
        margin: auto;
    }

    .header,
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .section-title {
        text-align: center;
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .header td,
    .table td,
    .table th {
        border: 1px solid black;
        padding: 5px;
    }

    .text-header-left {
        text-align: center;
    }

    .text-header-right {
        text-align: center;
    }

    .section-title td {
        text-align: center;
        border: 1px solid black;
        padding: 5px;
    }

    .data {
        text-align: center;
    }

    .header-info td {
        text-align: left;
        padding: 5px;
    }

    .title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
    }

    .section-title td {
        background-color: #f2f2f2;
        font-weight: bold;
        padding: 8px;
    }

    .bold {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <table class="header">
            <tr>
                <td rowspan="7" class="text-header-left" style="width: 80px;">
                    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents($image)) }}" alt="DOH Logo"
                        width="80">
                </td>
                <td colspan="2" class="title">FHSIS REPORT</td>
                <td rowspan="7" class="text-header-right" style="font-size: 30px; font-weight: bold;">
                    <!-- Check if report type is quarterly or annual -->
                    @if ($reportType == 'annual')
                    <span>{{ $selectedType }}</span> <!-- Display Year -->
                    @elseif ($reportType == 'quarterly')
                    <span>Q{{ $selectedType }}</span> <!-- Display Quarter -->
                    @else
                    <span>M{{ $selectedType }}</span> <!-- Display Month -->
                    @endif
                </td>
            </tr>
            <tr>
                <td class="header-info bold">FHSIS REPORT for:</td>
                <td>
                    @if ($reportType == 'annual')
                    <span>{{ $selectedType }}</span>
                    @elseif ($reportType == 'quarterly')
                    <span>{{ $quarterName }} {{ $year }}</span>
                    @else
                    <span>{{ date("F", mktime(0, 0, 0, $selectedType, 10)) }} {{ $year }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="header-info bold">Name of Health Facility:</td>
                <td><span>Bato</span></td>
            </tr>
            <tr>
                <td class="header-info bold">Name of Barangay:</td>
                <td><span>All Brgys.</span></td>
            </tr>
            <tr>
                <td class="header-info bold">Name of Municipality/City:</td>
                <td><span>Bato</span></td>
            </tr>
            <tr>
                <td class="header-info bold">Projected Population of the Year:</td>
                <td><span>{{ $totalPopulation }}</span></td>
            </tr>
            <tr>
                <td class="header-info bold">Name of Province:</td>
                <td><span>Leyte</span></td>
            </tr>
            <tr>
                <td colspan="4" class="section-title"><em>For submission to the next administrative level<em></td>
            </tr>
        </table>

        <!-- Section Title -->
        <table class="section-title">
            <tr>
                <td>Section A.1. Morbidity Report</td>
            </tr>
        </table>

        <!-- Table for morbidity report -->
        <table class="table">
            <tr>
                <th rowspan="2">Disease</th>
                <th rowspan="2">ICD-10 Code</th>
                <th colspan="2">0-9 yrs.</th>
                <th colspan="2">10-19 yrs</th>
                <th colspan="2">20-59 yrs</th>
                <th colspan="2">60 yrs. Above</th>
                <th colspan="2">Total</th>
                <th rowspan="2">TOTAL Both Sex</th>
            </tr>
            <tr>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
            </tr>

            <!-- Loop over the diagnosis age groups -->
            @foreach($diagnosisAgeGroups as $diagnosis => $ageGroup)
            <tr class="data">
                <td>{{ $diagnosis }}</td>
                <td>{{ $data->firstWhere('diagnosis.diagnosis_name', $diagnosis)->diagnosis->diagnosis_code ?? '' }}
                </td>
                <td>{{ $ageGroup['0-9']['M'] }}</td>
                <td>{{ $ageGroup['0-9']['F'] }}</td>
                <td>{{ $ageGroup['10-19']['M'] }}</td>
                <td>{{ $ageGroup['10-19']['F'] }}</td>
                <td>{{ $ageGroup['20-59']['M'] }}</td>
                <td>{{ $ageGroup['20-59']['F'] }}</td>
                <td>{{ $ageGroup['60+']['M'] }}</td>
                <td>{{ $ageGroup['60+']['F'] }}</td>
                <td>{{ $ageGroup['0-9']['M'] + $ageGroup['10-19']['M'] + $ageGroup['20-59']['M'] +  $ageGroup['60+']['M'] }}
                </td>
                <td>{{ $ageGroup['0-9']['F'] + $ageGroup['10-19']['F'] + $ageGroup['20-59']['F'] +  $ageGroup['60+']['F'] }}
                </td>
                <td>{{ $ageGroup['0-9']['M'] + $ageGroup['0-9']['F'] + $ageGroup['10-19']['M'] + $ageGroup['10-19']['F'] + $ageGroup['20-59']['M'] + $ageGroup['20-59']['F'] + $ageGroup['60+']['M'] + $ageGroup['60+']['F'] }}
                </td>
            </tr>
            @endforeach
        </table>

    </div>
</body>

</html>