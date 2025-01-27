<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\PatientsDashboard;
use App\Http\Controllers\dashboard\NurseStaffDashboard;
use App\Http\Controllers\dashboard\DoctorDashboard;
use App\Http\Controllers\dashboard\DentistDashboard;
use App\Http\Controllers\doctor\DoctorController;
use App\Http\Controllers\dentist\DentistController;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\admin\patients\PatientController;
use App\Http\Controllers\patient\PatientConsultationRecords;
use App\Http\Controllers\admin\treatments\TreatmentController;
use App\Http\Controllers\nurse_and_staff\ConsultationController;
use App\Http\Controllers\nurse_and_staff\DentalController;
use App\Http\Controllers\nurse_and_staff\FamilyController;
use App\Http\Controllers\nurse_and_staff\ImportPatients;
use App\Http\Controllers\nurse_and_staff\AnnouncementController;
use App\Http\Controllers\nurse_and_staff\ImplantController;
use App\Http\Controllers\users\UserController;
use App\Http\Controllers\users\PopulationController;
use App\Models\Announcement;
use Carbon\Carbon;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Artisan;

Route::get('/create-log-migration', function () {
    // Step 1: Create the migration file for the logs table
    Artisan::call('make:migration create_logs_table --create=logs');
});

Route::get('/clear-all-cache', function () {
    // Clear the config cache
    Artisan::call('config:clear');

    // Clear the application cache
    Artisan::call('cache:clear');

    // Flash success messages to the session
    session()->flash('cacheCleared', 'All caches cleared successfully!');

    return redirect()->back();
});



Route::get('/', function () {
    // Retrieve future announcements, paginated by 3 per page
    $announcements = Announcement::select('title', 'content', 'location', 'date')
        ->where('date', '>=', Carbon::today()) // Filter out past announcements
        ->orderBy('date', 'asc')
        ->paginate(3);
    // Format the date for each announcement
    foreach ($announcements as $announcement) {
        $announcement->date = Carbon::parse($announcement->date)->format('F j, Y');
    }
    
    return view('content.welcome', compact('announcements'));
});


Route::get('/login', [LoginBasic::class, 'index'])->name('login');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/login-post', [LoginBasic::class, 'login'])->name('login-post');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::post('/auth/register-post', [RegisterBasic::class, 'store'])->name('register-post');
Route::post('/auth/logout', [LoginBasic::class, 'logout'])->name('logout');

//Google Auth
Route::post('/auth/sso', [LoginBasic::class, 'sso']);

Route::middleware('auth')->group(function () {
    
    // Routes that should be accessible by Admin only
    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin-dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');
       Route::get('/users', [UserController::class, 'index'])->name('user-view');
       Route::post('/store-user', [UserController::class, 'store'])->name('store-user');
       Route::get('/fetchUser', [UserController::class, 'fetchUser'])->name('fetch-user');
       Route::get('/edit-user/{id}', [UserController::class, 'getUser'])->name('get-user');
       Route::put('/update-user/{id}', [UserController::class, 'updateUser'])->name('update-user');
       Route::delete('/delete-user/{id}', [UserController::class, 'destroy'])->name('delete-user');
    });


    Route::middleware('role:Admin,Nurse,Staff,Midwife')->group(function () {
        //Patients
        Route::get('/patients', [PatientController::class, 'index'])->name('patients-view');
        Route::get('/patient/add-patient', [PatientController::class, 'getAddPatients'])->name('patients-view');
        Route::get('/patient/edit-patient/{id}', [PatientController::class, 'getUpdatePatients'])->name('/patient/edit-patient');
        Route::post('/add-patient', [PatientController::class, 'addPatient'])->name('add-patient');
        Route::put('/update-patient/{id}', [PatientController::class, 'updatePatient'])->name('update-patient');
        Route::post('/add-patient-user/{id}', [PatientController::class, 'addPatientsAccount'])->name('add-patient-user');
        Route::get('/fetchPatient', [PatientController::class, 'fetchPatients'])->name('fetch-patient');
        Route::get('/fetch-patient-details/{id}', [PatientController::class, 'getPatients']);
        Route::get('/search-patients', [PatientController::class, 'searchPatient'])->name('search-patients');


        Route::get('/population', [PopulationController::class, 'index'])->name('population-view');
        Route::get('/fetchPopulation', [PopulationController::class, 'fetchPopulation'])->name('fetchPopulation');
        Route::post('/store-population', [PopulationController::class, 'storePopulation'])->name('store-population');

    });

    Route::middleware('role:Patient')->group(function() {
        //Patient Dashboard
        Route::get('/patient-dashboard', [PatientsDashboard::class, 'index'])->name('patient-dashboard');
        
        Route::get('/consultation-records', [PatientConsultationRecords::class, 'index'])->name('consultation-records');
        Route::get('/fetchPatientsConsultation', [PatientConsultationRecords::class, 'viewConsultation'])->name('fetchPatientsConsultation');
        
        Route::get('/dental-records', [PatientConsultationRecords::class, 'dental'])->name('dental-records');
        Route::get('/fetchPatientsDental', [PatientConsultationRecords::class, 'viewDental'])->name('fetchPatientsDental');
        
        Route::get('/consultation-diagnosis-records', [PatientConsultationRecords::class, 'getConsultationDiagnosis'])->name('consultation-diagnosis-view');
        Route::get('/fetchPatientsConsultationDiagnosis', [PatientConsultationRecords::class, 'viewConsultationDiagnosis'])->name('fetchPatientsDiagnosis');

        Route::get('/dental-diagnosis-records', [PatientConsultationRecords::class, 'getDentalDiagnosis'])->name('dental-diagnosis-view');
        Route::get('/fetchPatientsDentalDiagnosis', [PatientConsultationRecords::class, 'viewDentalDiagnosis'])->name('fetchPatientsDiagnosis');

        Route::get('/dashboard/chief/complaints-data', [PatientsDashboard::class, 'getChiefConsultation'])->name('dashboard.chiefComplaintsData');
        Route::get('/chiefComplaint', [PatientsDashboard::class, 'getChiefComplaintConsultation'])->name('dashboard.chief_complaint');
      
        Route::get('/dashboard/chiefComplaints-data', [PatientsDashboard::class, 'getChiefDental'])->name('dashboard.chiefComplaintsData');
        Route::get('/chiefComplaints', [PatientsDashboard::class, 'getChiefComplaintsDental'])->name('dashboard.chief_complaint');

        Route::get('/dashboardDiagnosis-data', [PatientsDashboard::class, 'getDiagnosisData'])->name('dashboard.getDiagnosisData');
        Route::get('/get-diagnosis', [PatientsDashboard::class, 'getDiagnosis'])->name('dashboard.getDiagnosis');

        Route::get('/dashboard_Diagnosis-data', [PatientsDashboard::class, 'get_DiagnosisData'])->name('dashboard.getDiagnosisData');
        Route::get('/get_diagnosis', [PatientsDashboard::class, 'get_Diagnosis'])->name('dashboard.getDiagnosis');


    });

     //Doctor dashboard
     Route::middleware('role:Doctor')->group(function () {  
        Route::get('/doctor-dashboard', [DoctorDashboard::class, 'index'])->name('doctor-dashboard');
        Route::get('/diagnosis', [DoctorDashboard::class, 'getDiagnosis'])->name('diagnosis');
        Route::get('/dashboard/diagnosis-data', [DoctorDashboard::class, 'getDiagnosisData']);
        Route::get('/dashboard/age-distribution', [DoctorDashboard::class, 'getAgeDistributionData'])->name('dashboard/age-distribution');
        Route::get('/dashboard/patient-gender-data', [DoctorDashboard::class, 'getGenderDistributionData']);

        Route::get('/doctor-patients', [DoctorController::class, 'getConsultationPatientsQueue'])->name('doctor-view');
        Route::get('/patients-consultation-diagnosis', [DoctorController::class, 'getDiagnosisPatients'])->name('diagnosis-view');

        Route::get('/fetchDiagnosis', [DoctorController::class, 'getDiagnosisPatientsRecord'])->name('fetchDiagnosis');
        Route::get('/fetchQueueing', [DoctorController::class, 'consultationQueueing'])->name('fetchQueueing');

        Route::get('/view-consultation-diagnosis-records/{id}', [DoctorController::class, 'getDiagnosisPatientsRecords'])->name('diagnosis-view');

        Route::get('/add-consultation-diagnosis/{id}', [DoctorController::class, 'getConsultationDiagnosis'])->name('doctor-view');
        Route::post('/addConsultationDiagnosis/{id}', [DoctorController::class, 'addDiagnosis'])->name('addConsultationDiagnosis');

        Route::get('/get-diagnosis-count', [DoctorDashboard::class, 'getDiagnosisCount'])->name('diagnosis-count');

        Route::get('/map/bato-barangays', [MapController::class, 'getBatoBarangays']);
        Route::get('/get_highest-diagnosis-data', [DoctorDashboard::class, 'getHighestCases']);

        Route::get('/getPrescribedMedicines/{patient_id}', [DoctorController::class, 'getPrescribedMedicines']);
        Route::get('/printPrescription/{patient_id}', [DoctorController::class, 'printPrescription']);

        Route::get('chiefs-complaints-data', [DoctorDashboard::class, 'getChiefConsultation']);
        Route::get('chiefscomplaints', [DoctorDashboard::class, 'getChiefComplaintConsultation']);

    });

     //Dentist dashboard
     Route::middleware('role:Dentist')->group(function () {  
        Route::get('/dentist-dashboard', [DentistDashboard::class, 'index'])->name('dentist-dashboard');
        Route::get('/dental-diagnosis', [DoctorDashboard::class, 'getDiagnosis'])->name('diagnosis');
        Route::get('/dashboard-diagnosis-data', [DentistDashboard::class, 'getDiagnosisData']);
        Route::get('/dashboard-age-distribution', [DentistDashboard::class, 'getAgeDistributionData'])->name('dashboard/age-distribution');
        Route::get('/dashboard-patient-gender-data', [DentistDashboard::class, 'getGenderDistributionData']);

        Route::get('/dentist-patients', [DentistController::class, 'getDentalPatientsQueue'])->name('dentist-view');

        Route::get('/patients-dental-diagnosis', [DentistController::class, 'getDiagnosisPatients'])->name('dental-view');
        Route::get('/fetchQueue', [DentistController::class, 'dentalQueueing'])->name('fetchQueue');


        Route::get('/fetchDentalDiagnosis', [DentistController::class, 'getDiagnosisPatientsRecord'])->name('fetchDentalDiagnosis');
        Route::get('/view-dental-diagnosis-records/{id}', [DentistController::class, 'getDiagnosisPatientsRecords'])->name('dental-view');

        Route::get('/add-dental-diagnosis/{id}', [DentistController::class, 'getDiagnosis'])->name('dentist-view');
        Route::post('/addDentalDiagnosis/{id}', [DentistController::class, 'addDiagnosis'])->name('addDentalDiagnosis');

        Route::get('/get/diagnosis/count', [DentistDashboard::class, 'getDiagnosisCount'])->name('diagnosis-count');

        Route::get('/map_bato_barangays', [MapController::class, 'getBatoBarangays']);
        Route::get('/get_highest_diagnosis_data', [DentistDashboard::class, 'getHighestCases']);

        Route::get('/getPrescribedMedicine/{patient_id}', [DentistController::class, 'getPrescribedMedicines']);
        Route::get('/printPrescriptions/{patient_id}', [DentistController::class, 'printPrescription']);
        
        Route::get('/chiefs/complaints-data', [DentistDashboard::class, 'getChiefDental']);
        Route::get('/chiefs_complaints', [DentistDashboard::class, 'getChiefComplaintsDental'])->name('dashboard.chief_complaint');

    });

    //Role Nurse dashboard
    Route::middleware('role:Nurse,Midwife')->group(function () {  
        Route::get('/dashboard', [NurseStaffDashboard::class, 'nurse'])->name('dashboard');
        Route::get('/dashboard/chief-complaints-data', [NurseStaffDashboard::class, 'getChiefConsultation'])->name('dashboard.chiefComplaintsData');
        Route::get('/chief-complaint', [NurseStaffDashboard::class, 'getChiefComplaintConsultation'])->name('dashboard.chief_complaint');
        Route::get('/get/consultation/count', [NurseStaffDashboard::class, 'getConsultationCount'])->name('consultation-count');
    
        Route::get('/nurse-dashboard/diagnosis-data', [NurseStaffDashboard::class, 'getDiagnosisData']);
        Route::get('/dashboard/ages-distribution', [NurseStaffDashboard::class, 'getAgeDistributionData'])->name('dashboard/age-distribution');
        Route::get('/dashboard/patients-gender-data', [NurseStaffDashboard::class, 'getGenderDistributionData']);

        Route::get('/map-bato-barangays', [MapController::class, 'getBatoBarangays']);
        Route::get('/get-highest-diagnosis-data', [NurseStaffDashboard::class, 'getHighestCases']);
    });
    
    //Role staff dashboard
    Route::middleware('role:Staff')->group(function () {  
        Route::get('/staff-dashboard', [NurseStaffDashboard::class, 'staff'])->name('staff-dashboard');
        Route::get('/dashboard-chief-complaints-data', [NurseStaffDashboard::class, 'getChiefDental'])->name('dashboard.chiefComplaintsData');
        Route::get('/chief_complaint', [NurseStaffDashboard::class, 'getChiefComplaintsDental'])->name('dashboard.chief_complaint');
        Route::get('/get/dental/count', [NurseStaffDashboard::class, 'getDentalCount'])->name('dental-count');

        Route::get('/staff-dashboard/diagnosis-data', [NurseStaffDashboard::class, 'getDiagnosisData']);
        Route::get('/dashboard/ages/distribution', [NurseStaffDashboard::class, 'getAgeDistributionData'])->name('dashboard/age-distribution');
        Route::get('/dashboard/patients/gender-data', [NurseStaffDashboard::class, 'getGenderDistributionData']);

        Route::get('/bato-barangay', [MapController::class, 'getBatoBarangays']);
        Route::get('/highest-diagnosis-data', [NurseStaffDashboard::class, 'getHighestCases']);
    });
    //Role Nurse and Staff
    Route::middleware('role:Nurse,Staff,Midwife')->group(function () {    
        //Consultation Controller
        Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultation-view');
        Route::get('fetchPatients', [ConsultationController::class, 'fetchPatientsConsultation'])->name('fetchPatients');
        Route::get('add-consultation/{id}', [ConsultationController::class, 'getPatientConsultation'])->name('consultation-view'); //consultation-view to active the Consultation in verticalMenueJSON
        Route::post('/addConsultation/{id}', [ConsultationController::class, 'addConsulation'])->name('addConsultation');
        Route::get('/view-consultation/{id}', [ConsultationController::class, 'getConsultationRecords'])->name('consultation-view');
        Route::get('/consultations/filter', [ConsultationController::class, 'getConsultationRecords']);

        //Dental Controller 
        Route::get('/dentals', [DentalController::class, 'index'])->name('dental-view');
        Route::get('fetch-patients', [DentalController::class, 'fetchPatientsDental'])->name('fetch-patients');
        Route::get('add-dental/{id}', [DentalController::class, 'getPatientDental'])->name('dental-view'); 
        Route::post('/addDental/{id}', [DentalController::class, 'addDental'])->name('addDental');
        Route::get('/view-dental/{id}', [DentalController::class, 'getDentalRecords'])->name('dental-view');
    
        //Family Planning Controller
        Route::get('/family-planning', [FamilyController::class, 'index'])->name('planning-view');
        Route::get('/add-family-planning', [FamilyController::class, 'getAddPlanning'])->name('planning-view');
        Route::get('/fetchPlanning', [FamilyController::class, 'fetchPlanning'])->name('fetchPlanning');
        Route::post('/add-planning', [FamilyController::class, 'addPlanning'])->name('add-planning');
        Route::get('/add-new-planning/{id}', [FamilyController::class, 'addNewPlanning'])->name('planning-view');
        Route::post('/addNewPlanning/{id}', [FamilyController::class, 'addNewPlannings'])->name('addNewPlanning');
        Route::get('/view-planning-records/{id}', [FamilyController::class, 'getPlanningRecords'])->name('planning-view');
        Route::get('/search-planning', [FamilyController::class, 'searchPatient'])->name('search-planning');
        Route::delete('/delete-planning/{id}', [FamilyController::class, 'destroy'])->name('delete-planning');

        Route::get('/implant', [ImplantController::class, 'index'])->name('implant-view');
        Route::get('/add-implant', [ImplantController::class, 'getAddImplant'])->name('implant-view');
        Route::post('/addImplant', [ImplantController::class, 'addImplant'])->name('addImplant');
        Route::get('/fetchImplant', [ImplantController::class, 'fetchImplant'])->name('fetchImplant');
        Route::get('/add-new-implant/{id}', [ImplantController::class, 'getNewAddImplant'])->name('implant-view');
        Route::post('/addNewImplant/{id}', [ImplantController::class, 'addNewImplant'])->name('addNewImplant');
        Route::get('/view-implant-records/{id}', [ImplantController::class, 'getImplant'])->name('implant-view');
        Route::delete('/delete-implant/{id}', [ImplantController::class, 'destroy'])->name('delete-implant');


        Route::get('/import-patients', [ImportPatients::class, 'index'])->name('import-patients');
        Route::post('/importpatients', [ImportPatients::class, 'import'])->name('importpatients');
        Route::get('/fetchImport', [ImportPatients::class, 'fetchImport'])->name('fetchImport');

        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcement-view');
        Route::get('/fetchAnnouncement', [AnnouncementController::class, 'fetchAnnouncement'])->name('fetchAnnouncement');
        Route::post('/addAnnouncement', [AnnouncementController::class, 'addAnnouncement'])->name('addAnnouncement');

        Route::post('/generate-report', [ReportController::class, 'generateFHSISReport'])->name('generate.report');
        Route::get('/generate-reports', [ReportController::class, 'generateReport'])->name('report-view');
        Route::get('/fetch-reports', [ReportController::class, 'fetchReport'])->name('report-view');


        Route::get('/report/download/{encryptedId}/{fileType}', [ReportController::class, 'downloadReport'])->name('report.download');

    });

    // pages
    Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
    Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');
});


 // Fetch Diagnosis
 Route::get('/fetch-diagnosis', [DoctorController::class, 'getDiagnosis']);
 Route::get('/fetch-medicines', [DoctorController::class, 'fetchMedicine']);
 
// Dashboard Patient Count
Route::get('/get-patient-count', [Analytics::class, 'getPatientsCount'])->name('patient-count');

  // Fetch Genders
  Route::get('/fetch-genders', [PatientController::class, 'fetchGenders']);

  // Fetch Address
  Route::get('/regions', [PatientController::class, 'fetchRegions']);
  Route::get('/provinces/{region}', [PatientController::class, 'fetchProvinces']);
  Route::get('/city-municipality', [PatientController::class, 'fetchCityMunicipality']);
  Route::get('/barangays', [PatientController::class, 'fetchBarangays']);

  // Fetch Blood Types
  Route::get('/fetch-blood-types', [PatientController::class, 'fetchBloodTypes']);

  // Fetch Civil Statuses
  Route::get('/fetch-civil-statuses', [PatientController::class, 'fetchCivilStatuses']);

  // Fetch Educational Attainments
  Route::get('/fetch-educational-attainments', [PatientController::class, 'educationalAttainments']);

  // Fetch Employment Status
  Route::get('/fetch-employment-statuses', [PatientController::class, 'employmentStatuses']);

  // Fetch Family Member
  Route::get('/fetch-family-members', [PatientController::class, 'familyMembers']);

  // Fetch PhilHealth Status Type
  Route::get('/fetch-status-types', [PatientController::class, 'philhealthStatusTypes']);

  // Fetch PhilHealth Categories
  Route::get('/fetch-categories', [PatientController::class, 'philhealthCategories']);

  // Fetch Mode of Transaction
  Route::get('/fetch-mode-of-transaction', [TreatmentController::class, 'fetchModeOfTransaction']);

  // Fetch Attending Provider
  Route::get('/fetch-attending-provider', [TreatmentController::class, 'fetchAttendingProvider']);

  // Fetch Nature of Visit
  Route::get('/fetch-nature-of-visit', [TreatmentController::class, 'fetchNatureOfVisit']);

  // Fetch Type of Consultation
  Route::get('/fetch-consultation-type', [TreatmentController::class, 'fetchTypeOfConsultation']);

  // Fetch HealthCare Provider
  Route::get('/fetch-healthcare-provider', [TreatmentController::class, 'fetchHealthcareProvider']);

  // Fetch Referred From
  Route::get('/fetch-referred-from', [TreatmentController::class, 'fetchRefferredFrom']);

  // Fetch Referred To
  Route::get('/fetch-referred-to', [TreatmentController::class, 'fetchRefferredTo']);

  // Fetch Referred By
  Route::get('/fetch-referred-by', [TreatmentController::class, 'fetchRefferredBy']);