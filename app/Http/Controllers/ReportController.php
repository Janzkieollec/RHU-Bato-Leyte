<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\DiagnosisAnalytics;
use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\Log;
use App\Models\Population;
use App\Models\Reports;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Facades\Auth;


class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        return view('content.report.generate_report');
    }

    public function generateMidwifeReport(Request $request)
    {
        return view('content.report.midwife_report');
    }
    public function generateFHSISReport(Request $request)
    {
        $path = public_path('assets/img/favicon/DOHevchd-header-1.png');
        if (!file_exists($path)) {
            abort(404, 'Image not found');
        }
    
        $type = $request->input('type');
        $reportType = $request->input('reportType');
        $year = $request->input('year');
        $userRole = auth()->user()->role; // Assuming the user is authenticated and has a 'role' field
    
        // Validate reportType
        if (!in_array($reportType, ['monthly', 'quarterly', 'annual'])) {
            return response()->json(['status' => 400, 'message' => 'Invalid report type selected.']);
        }
    
        // Determine the diagnosis_type based on the user role
        $diagnosisType = in_array($userRole, ['Nurse', 'Midwife']) ? 1 : 2; // Diagnosis type 1 for Nurse or Midwife, 2 for the Staff
    
        // Filter data based on the report type and diagnosis type
        if ($reportType === 'monthly') {
            $data = DiagnosisAnalytics::with(['patient.gender', 'diagnosis'])
                ->whereHas('diagnosis', function ($query) use ($diagnosisType) {
                    $query->where('diagnosis_type', $diagnosisType); // Filter by diagnosis type based on role
                })
                ->whereYear('created_at', $year)
                ->when($type, function ($query, $type) {
                    $query->whereMonth('created_at', $type);
                })
                ->get()
                ->map(function ($item) {
                    $item->gender_id = $item->patient ? $item->patient->gender_id : null;
                    return $item;
                });
    
            $monthName = Carbon::createFromFormat('m', $type)->format('F');
            $reportTitle = "FHSIS for {$monthName} {$year}";
    
        } elseif ($reportType === 'quarterly') {
            $quarterMonths = [];
            switch ($type) {
                case '1': $quarterMonths = [1, 2, 3]; $quarterName = 'Quarter 1 (Jan-Mar)'; break;
                case '2': $quarterMonths = [4, 5, 6]; $quarterName = 'Quarter 2 (Apr-Jun)'; break;
                case '3': $quarterMonths = [7, 8, 9]; $quarterName = 'Quarter 3 (Jul-Sep)'; break;
                case '4': $quarterMonths = [10, 11, 12]; $quarterName = 'Quarter 4 (Oct-Dec)'; break;
                default: return response()->json(['status' => 400, 'message' => 'Invalid quarter selected.']);
            }
    
            $data = DiagnosisAnalytics::with(['patient.gender', 'diagnosis'])
                ->whereHas('diagnosis', function ($query) use ($diagnosisType) {
                    $query->where('diagnosis_type', $diagnosisType);
                })
                ->whereYear('created_at', $year)
                ->whereIn(\DB::raw('MONTH(created_at)'), $quarterMonths)
                ->get()
                ->map(function ($item) {
                    $item->gender_id = $item->patient ? $item->patient->gender_id : null;
                    return $item;
                });
    
            $reportTitle = "FHSIS Quarterly for {$quarterName} {$year}";
    
        } else {
            $data = DiagnosisAnalytics::with(['patient.gender', 'diagnosis'])
                ->whereHas('diagnosis', function ($query) use ($diagnosisType) {
                    $query->where('diagnosis_type', $diagnosisType);
                })
                ->whereYear('created_at', $type)
                ->get()
                ->map(function ($item) {
                    $item->gender_id = $item->patient ? $item->patient->gender_id : null;
                    return $item;
                });
    
            $reportTitle = "FHSIS for Year {$type}";
        }
    
        // Get the latest population data
        $latestPopulation = Population::orderBy('created_at', 'desc')->first();
        $totalPopulation = $latestPopulation ? $latestPopulation->total_population : 0;
    
        // Process age groups and genders for the diagnosis
        $diagnosisAgeGroups = [];
        foreach ($data as $item) {
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
    
        // Get the latest population data
        $latestPopulation = Population::orderBy('created_at', 'desc')->first();
        $totalPopulation = $latestPopulation ? $latestPopulation->total_population : 0;
    
        // Process age groups and genders for the diagnosis
        $diagnosisAgeGroups = [];
        foreach ($data as $item) {
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
    
        // Generate a unique report ID
        $reportId = 'RPT-' . strtoupper(uniqid()) . '-' . Carbon::now()->format('YmdHis');
    
        // Define the file path for both PDF and XLSX in the same directory
        $directoryPath = storage_path("app/reports/{$reportId}/");
    
        // Create directory if it doesn't exist
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
    
        // Generate the HTML view for the PDF (same as existing logic)
        $html = view('content.report.fhsis_report', [
            'image' => $path,
            'diagnosisAgeGroups' => $diagnosisAgeGroups,
            'data' => $data,
            'selectedType' => $type,
            'totalPopulation' => $totalPopulation,
            'quarterName' => $quarterName ?? '',
            'reportType' => $reportType,
            'year' => $year
        ])->render();
    
        // Set up PDF generation using Dompdf (same as existing logic)
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Define the file path and save the PDF
        $pdfFilePath = $directoryPath . "FHSIS_Report_{$reportId}.pdf";
        file_put_contents($pdfFilePath, $dompdf->output());
    
    
        $spreadsheetPath = storage_path('app/Morbidity.xlsx');
    
        // Load the existing Excel file
        $spreadsheet = IOFactory::load($spreadsheetPath);
    
        // Get the active sheet (or create a new one if necessary)
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set the value for O1 (or another cell if needed)
        $reportLabel = match ($reportType) {
            'annual' => $year, // Year
            'quarterly' => "Q{$type}", // Quarter
            'monthly' => "M{$type}", // Month and Year
        };

        $typeReport =  match ($reportType) {
            'annual' => $year, // Year
            'quarterly' => $quarterName . " {$year}", // Quarter
            'monthly' => Carbon::createFromFormat('m', $type)->format('F') . " {$year}", // For monthly, format the month and year
        };

        // Set the alignment to center horizontally and vertically
        $sheet->getStyle('O1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)   // Center horizontally
            ->setVertical(Alignment::VERTICAL_CENTER);       // Center vertically

        // Set the font size to 30
        $sheet->getStyle('O1')->getFont()
            ->setSize(30)   // Set font size to 30
            ->setBold(true); // Set font to bold

         // Set the value for cell O1
         $sheet->setCellValue('O1', $reportLabel);
         $sheet->setCellValue('G1', $typeReport);
         $sheet->setCellValue('G6', $totalPopulation);
 

    
        // Start writing data directly, skipping headers
        $row = 11; // Start from row 11
        foreach ($diagnosisAgeGroups as $diagnosis => $ageGroup) {
            $sheet->setCellValue("A{$row}", $diagnosis);
            $sheet->setCellValue("B{$row}", $data->firstWhere('diagnosis.diagnosis_name', $diagnosis)->diagnosis->diagnosis_code ?? '');
            $sheet->setCellValue("C{$row}", $ageGroup['0-9']['M']);
            $sheet->setCellValue("D{$row}", $ageGroup['0-9']['F']);
            $sheet->setCellValue("E{$row}", $ageGroup['10-19']['M']);
            $sheet->setCellValue("F{$row}", $ageGroup['10-19']['F']);
            $sheet->setCellValue("G{$row}", $ageGroup['20-59']['M']);
            $sheet->setCellValue("H{$row}", $ageGroup['20-59']['F']);
            $sheet->setCellValue("I{$row}", $ageGroup['60+']['M']);
            $sheet->setCellValue("J{$row}", $ageGroup['60+']['F']);
    
            $totalM = $ageGroup['0-9']['M'] + $ageGroup['10-19']['M'] + $ageGroup['20-59']['M'] + $ageGroup['60+']['M'];
            $totalF = $ageGroup['0-9']['F'] + $ageGroup['10-19']['F'] + $ageGroup['20-59']['F'] + $ageGroup['60+']['F'];
            $totalBoth = $totalM + $totalF;
    
            $sheet->setCellValue("K{$row}", $totalM);
            $sheet->setCellValue("M{$row}", $totalF);
            $sheet->setCellValue("Q{$row}", $totalBoth);
    
            $row++;
        }
    
        // Define the file path and save the XLSX in the same directory as the PDF
        $xlsxFilePath = $directoryPath . "FHSIS_Report_{$reportId}.xlsx";
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($xlsxFilePath);
    
        $role = Auth::user()->role; 
        
        // Save report details in the database
        $report = Reports::create([
            'report_id' => $reportId,
            'role' => $role,
            'type' => $reportTitle,
            'pdf_file_path' => $pdfFilePath,
            'csv_file_path' => $xlsxFilePath,
        ]);

        $role = Auth::user()->role;

        Log::create([
            'role' => $role,
            'action' => 'Added',
            'description' => "You have successfully generated reports for {$reportType}.",
        ]);
    
        // Return response with success status
        return response()->json(['status' => 200, 'message' => 'Report generated and saved successfully.']);
    }

    public function fetchReport(Request $request)
    {
        $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
    
             // Get the logged-in user's role
        $user = Auth::user();
        $rolename = null;

        // Set diagnosis type based on role
        if ($user->role === 'Nurse') {
            $rolename = 'Nurse'; // Consultation
        } elseif ($user->role === 'Staff') {
            $rolename = 'Staff'; // Dental
        } elseif ($user->role === 'Midwife') {
            $rolename = 'Midwife'; // Midwife
        }
        
        // Build query to fetch Reports with related data (if any)
        $query = Reports::select(
            'reports.id',
            'reports.report_id',
            'reports.type',
            'reports.created_at'
        )
        ->where('reports.role', $rolename)
        ->orderBy('reports.created_at', 'desc');
    
        $reports = $query->paginate($pageSize);
    
        // Process reports data (for middle name and encryption)
        foreach ($reports as $report) {
            $report->encrypted_id = Crypt::encrypt($report->id); // Encrypt the report ID
            $report->formattedDate = Carbon::parse($report->created_at)->format('F j, Y'); 
        }
    
        // Return JSON response for pagination and reports
        return response()->json([
            'reports' => $reports
        ]);
    }
   
    public function downloadReport($encryptedId, $fileType)
    {
        // Decrypt the ID
        $id = Crypt::decrypt($encryptedId);

        // Retrieve the report from the database
        $report = Reports::findOrFail($id);

        // Check if the report status is 'Pending'
        if ($report->status === 'Pending') {
            return response()->json(['status' => 400, 'message' => 'Report is not ready for download.']);
        }

        // Update the status to 'Completed'
        $report->update([
            'status' => 'Completed',
        ]);

        // Define the base directory path based on report_id
        $directoryPath = storage_path("app/reports/{$report->report_id}/");

        // Determine the file path based on the requested file type (pdf or csv)
        if ($fileType === 'pdf') {
            $filePath = $directoryPath . "FHSIS_Report_{$report->report_id}.pdf";
            $fileName = "FHSIS_Report_{$report->report_id}.pdf";
        } elseif ($fileType === 'csv') {
            $filePath = $directoryPath . "FHSIS_Report_{$report->report_id}.xlsx";  // Assuming you have the CSV in a similar structure
            $fileName = "FHSIS_Report_{$report->report_id}.xlsx";
        } else {
            return abort(404, 'Invalid file type');
        }

        // Check if the file exists
        if (!file_exists($filePath)) {
            return abort(404, 'File not found');
        }

        $role = Auth::user()->role;

        Log::create([
            'role' => $role,
            'action' => 'Download',
            'description' => "You have successfully downloaded reports {$fileName}.",
        ]);
        
        // Return the file for download
        return response()->download($filePath, $fileName); 
    }

}