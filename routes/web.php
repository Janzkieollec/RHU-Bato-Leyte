<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\PatientsDashboard;
use App\Http\Controllers\dashboard\NurseStaffDashboard;
use App\Http\Controllers\dashboard\DoctorDashboard;
use App\Http\Controllers\dashboard\DentistDashboard;
use App\Http\Controllers\dashboard\MidwifeDashboard;
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
use App\Models\UserProfile;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PatientQueueController;
use App\Http\Controllers\pages\AccountSettingsAccount;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SMSController;


Route::get('/', function () {
    // Retrieve future announcements
    $announcements = Announcement::select('title', 'content', 'location', 'date')
        ->where('date', '>=', Carbon::today()) // Filter out past announcements
        ->orderBy('date', 'asc')
        ->get();

    // Format the date for each announcement
    foreach ($announcements as $announcement) {
        $announcement->date = Carbon::parse($announcement->date)->format('F j, Y');
    }

    // Retrieve the first Admin user with their profile
    $user = User::where('role', 'Admin')
        ->with('profile') // Assuming the relationship is defined in the User model
        ->first();

    return view('content.landing', compact('announcements', 'user'));
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
    
    Route::get('/logs', [LogsController::class, 'index']);
    Route::get('/fetchLogs', [LogsController::class, 'fetchLogs']);
    Route::get('/profile', [AccountSettingsAccount::class, 'index']);
    Route::post('/update-account', [AccountSettingsAccount::class, 'updateAccount'])->name('update-account');
    Route::post('/update-profile', [AccountSettingsAccount::class, 'updateProfile'])->name('update-profile');
    Route::post('/change-password', [AccountSettingsAccount::class, 'changePassword'])->name('change-password');
    Route::post('/update-setting', [AccountSettingsAccount::class, 'updateSetting'])->name('update-setting');

    Route::post('/update-nurse', [AccountSettingsAccount::class, 'updateNurse'])->name('update-nurse');
    
    Route::post('/send-cloudsms', [SMSController::class, 'sendInfobipSMS'])->name('send-cloudsms');

    Route::post('/api/set-max-patients', [PatientQueueController::class, 'setMaxPatients'])->name('set-max-patients');

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
        Route::get('/admin-dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');

        Route::get('/patients', [PatientController::class, 'index'])->name('patients-view');
        Route::get('/patient/add-patient', [PatientController::class, 'getAddPatients'])->name('patients-view');
        Route::get('/patient/edit-patient/{id}', [PatientController::class, 'getUpdatePatients'])->name('patients-view');
        Route::post('/add-patient', [PatientController::class, 'addPatient'])->name('add-patient');
        Route::put('/update-patient/{id}', [PatientController::class, 'updatePatient'])->name('update-patient');
        Route::post('/add-patient-user/{id}', [PatientController::class, 'addPatientsAccount'])->name('add-patient-user');
        Route::get('/fetchPatient', [PatientController::class, 'fetchPatients'])->name('fetch-patient');
        Route::get('/fetch-patient-details/{id}', [PatientController::class, 'getPatients']);
        Route::get('/search-patients', [PatientController::class, 'searchPatient'])->name('search-patients');

        Route::get('/get-family-number/{last_name}/{middle_name?}', [PatientController::class, 'getFamilyNumber']);
        
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
        Route::get('/top_10_diagnosis', [DoctorDashboard::class, 'getDiagnosisAnalytics']);

        Route::get('/doctor-patients', [DoctorController::class, 'getConsultationPatientsQueue'])->name('doctor-view');
        Route::get('/patients-consultation-diagnosis', [DoctorController::class, 'getDiagnosisPatients'])->name('diagnosis-view');

        Route::get('/fetchDiagnosis', [DoctorController::class, 'getDiagnosisPatientsRecord'])->name('fetchDiagnosis');
        Route::get('/fetchQueueing', [DoctorController::class, 'consultationQueueing'])->name('fetchQueueing');

        Route::get('/view-consultation-diagnosis-records/{id}', [DoctorController::class, 'getDiagnosisPatientsRecords'])->name('diagnosis-view');
        Route::get('/fetchConsultationDiagnosisRecords/{id}', [DoctorController::class, 'getDiagnosisRecords']);

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
        Route::get('/top_10-diagnosis', [DentistDashboard::class, 'getDiagnosisAnalytics']);

        Route::get('/dentist-patients', [DentistController::class, 'getDentalPatientsQueue'])->name('dentist-view');

        Route::get('/patients-dental-diagnosis', [DentistController::class, 'getDiagnosisPatients'])->name('dental-view');
        Route::get('/fetchQueue', [DentistController::class, 'dentalQueueing'])->name('fetchQueue');

        Route::get('/fetchDentalDiagnosisRecords/{id}', [DentistController::class, 'getDiagnosisRecords']);

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

    // //Role Nurse, Staff and Midwife dashboard
    Route::middleware('role:Nurse')->group(function () { 
        //Nurse 
        Route::get('/nurse-dashboard', [NurseStaffDashboard::class, 'nurse'])->name('nurse-dashboard');
        Route::get('/dashboard/chief-complaints-data', [NurseStaffDashboard::class, 'getChiefConsultation'])->name('dashboard.chiefComplaintsData');
        Route::get('/chief-complaint', [NurseStaffDashboard::class, 'getChiefComplaintConsultation'])->name('dashboard.chief_complaint');
        Route::get('/get/consultation/count', [NurseStaffDashboard::class, 'getConsultationCount'])->name('consultation-count');
        Route::get('/yearly-consultations', [NurseStaffDashboard::class, 'getYearlyConsultationData']);
        Route::get('/yearly-patients', [NurseStaffDashboard::class, 'getYearlyPatientData']);
        Route::get('/yearly-dental', [NurseStaffDashboard::class, 'getYearlyDentalData']);
        Route::get('/top-10-diagnosis-data', [NurseStaffDashboard::class, 'getDiagnosisAnalytics']);

        Route::get('/nurse-dashboard/diagnosis-data', [NurseStaffDashboard::class, 'getDiagnosisData']);
        Route::get('/dashboard/ages-distribution', [NurseStaffDashboard::class, 'getAgeDistributionData'])->name('dashboard/age-distribution');
        Route::get('/dashboard/patients-gender-data', [NurseStaffDashboard::class, 'getGenderDistributionData']);

        Route::get('/map-bato-barangays', [MapController::class, 'getBatoBarangays']);
        Route::get('/get-highest-diagnosis-data', [NurseStaffDashboard::class, 'getHighestCases']);       
    });

    //Role Midwife dashboard
    Route::middleware('role:Midwife')->group(function () { 
        //Midwife
        Route::get('/midwife-dashboard', [MidwifeDashboard::class, 'index'])->name('midwife-dashboard');
        Route::get('/get/planning-implant/count', [MidwifeDashboard::class, 'getPlanningImplantCount'])->name('planning-implant-count');
        Route::get('/yearly_patients', [MidwifeDashboard::class, 'getYearlyPatientData']);
       
    });

    Route::middleware('role:Staff')->group(function () { 
         //Staff
         Route::get('/staff-dashboard', [NurseStaffDashboard::class, 'staff'])->name('staff-dashboard');
         Route::get('/dashboard-chief-complaints-data', [NurseStaffDashboard::class, 'getChiefDental'])->name('dashboard.chiefComplaintsData');
         Route::get('/chief_complaint', [NurseStaffDashboard::class, 'getChiefComplaintsDental'])->name('dashboard.chief_complaint');
         Route::get('/get/dental/count', [NurseStaffDashboard::class, 'getDentalCount'])->name('dental-count');
         Route::get('/yearly-consultation', [NurseStaffDashboard::class, 'getYearlyConsultationData']);
         Route::get('/yearly-patient', [NurseStaffDashboard::class, 'getYearlyPatientData']);
         Route::get('/yearly-dentals', [NurseStaffDashboard::class, 'getYearlyDentalData']);
         Route::get('/top-10-diagnosis', [NurseStaffDashboard::class, 'getDiagnosisAnalytics']);
 
         
         Route::get('/staff-dashboard/diagnosis-data', [NurseStaffDashboard::class, 'getDiagnosisData']);
         Route::get('/dashboard/ages/distribution', [NurseStaffDashboard::class, 'getAgeDistributionData'])->name('dashboard/age-distribution');
         Route::get('/dashboard/patients/gender-data', [NurseStaffDashboard::class, 'getGenderDistributionData']);
 
         Route::get('/bato-barangay', [MapController::class, 'getBatoBarangays']);
         Route::get('/highest-diagnosis-data', [NurseStaffDashboard::class, 'getHighestCases']);
    });
    
    //Role Nurse, Midwife and Staff
    Route::middleware('role:Nurse,Staff,Midwife')->group(function () {    
        //Consultation Controller
        Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultation-view');
        Route::get('fetchPatients', [ConsultationController::class, 'fetchPatientsConsultation'])->name('fetchPatients');
        Route::get('add-consultation/{id}', [ConsultationController::class, 'getPatientConsultation'])->name('consultation-view'); //consultation-view to active the Consultation in verticalMenueJSON
        Route::post('/addConsultation/{id}', [ConsultationController::class, 'addConsulation'])->name('addConsultation');
        Route::get('/view-consultation/{id}', [ConsultationController::class, 'getConsultationRecords'])->name('consultation-view');
        // Route::get('/consultations/filter', [ConsultationController::class, 'getConsultationRecords']);
        Route::get('/fetchConsultation/{id}', [ConsultationController::class, 'viewConsultationRecords']);

        //Dental Controller 
        Route::get('/dentals', [DentalController::class, 'index'])->name('dental-view');
        Route::get('fetch-patients', [DentalController::class, 'fetchPatientsDental'])->name('fetch-patients');
        Route::get('add-dental/{id}', [DentalController::class, 'getPatientDental'])->name('dental-view'); 
        Route::post('/addDental/{id}', [DentalController::class, 'addDental'])->name('addDental');
        Route::get('/view-dental/{id}', [DentalController::class, 'getDentalRecords'])->name('dental-view');
        Route::get('/fetchDental/{id}', [DentalController::class, 'viewDentalRecords']);

    
        //Family Planning Controller
        Route::get('/family-planning', [FamilyController::class, 'index'])->name('planning-view');
        Route::get('/add-family-planning', [FamilyController::class, 'getAddPlanning'])->name('planning-view');
        Route::get('/fetchPlanning', [FamilyController::class, 'fetchPlanning'])->name('fetchPlanning');
        Route::post('/add-planning', [FamilyController::class, 'addPlanning'])->name('add-planning');
        Route::get('/add-new-planning/{id}', [FamilyController::class, 'addNewPlanning'])->name('planning-view');
        Route::post('/addNewPlanning/{id}', [FamilyController::class, 'addNewPlannings'])->name('addNewPlanning');
        Route::get('/view-planning-records/{id}', [FamilyController::class, 'getPlanningRecords'])->name('planning-view');
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
        Route::get('/generate-midwife-reports', [ReportController::class, 'generateMidwifeReport'])->name('report-view');
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
Route::get('/city-municipality/{provinceId}', [PatientController::class, 'fetchCityMunicipality']);
Route::get('/barangays', [PatientController::class, 'fetchBarangays']);