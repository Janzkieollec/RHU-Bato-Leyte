<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PatientsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        
        // Generate 100 sample patient records
        for ($i = 0; $i < 50; $i++) {

            // Generate random timestamps
            $createdAt = $faker->dateTimeBetween('-5 year', 'now');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');

            DB::table('patients')->insert([
                'patient_id' => $faker->unique()->randomNumber(8),
                'family_number' => $faker->numberBetween(1000, 9999),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'middle_name' => $faker->firstName, // Assuming middle name can be a first name
                'suffix_name' => $faker->optional()->randomElement(['Jr.', 'Sr.', 'II', 'III']),
                'birth_date' => $faker->date(),
                'age' => $faker->numberBetween(1, 100),
                'gender_id' => $faker->randomElement([1, 2]), // Assuming 1=Male, 2=Female
                'created_at' => $createdAt,
                'updated_at' =>  $updatedAt,
            ]);
        }

         // Define the barangay IDs
         $barangayIds = [
            '083707001', '083707002', '083707003', '083707004', 
            '083707005', '083707006', '083707008', '083707009', 
            '083707010', '083707011', '083707012', '083707013', 
            '083707014', '083707015', '083707016', '083707017', 
            '083707018', '083707019', '083707020', '083707021', 
            '083707022', '083707023', '083707026', '083707027', 
            '083707028', '083707029', '083707030', '083707031', 
            '083707032', '083707033', '083707034', '083707035'
        ];

        $barangayNames = [
            'Alegria', 'Alejos', 'Amagos', 'Anahawan', 'Bago', 
            'Bagong Bayan District (Pob.)', 'Buli', 'Cebuana', 
            'Daan Lungsod', 'Dawahon', 'Himamaa', 'Dolho', 
            'Domagocdoc', 'Guerrero District (Pob.)', 'Iniguihan District (Pob.)', 
            'Katipunan', 'Liberty (Binaliw)', 'Mabini', 'Naga', 
            'Osmeña', 'Plaridel', 'Kalanggaman District (Pob.)', 
            'Ponong', 'San Agustin', 'Santo Niño', 'Tabunok', 
            'Tagaytay', 'Tinago District (Pob.)', 'Tugas', 'Imelda', 
            'Marcelo', 'Rivilla',
        ];
        
        $chiefConsultation = [
            'Chest Pain',
            'Shortness of Breath',
            'Headache',
            'Fever',
            'Abdominal Pain',
            'Cough',
            'Dizziness',
            'Fatigue',
            'Nausea and Vomiting',
            'Back Pain',
            'Joint Pain',
            'Chest Tightness'
        ];
    
        $chiefDental = 
        [
            'Toothache',
            'Gum Swelling/Bleeding',
            'Cavities ',           
            'Broken/Chipped Tooth',           
            'Sensitivity',                      
            'Bad Breath (Halitosis)',           
            'Loose Tooth',          
            'Jaw Pain',         
            'Dry Mouth (Xerostomia)',
            'Discoloration of Teeth',
        ];

        // Fetch existing patient IDs
        $patientIds = DB::table('patients')->pluck('patient_id');

        // Generate sample addresses for existing patients
        foreach ($patientIds as $patientId) {
            DB::table('addresses')->insert([
                'patient_id' => $patientId,
                'family_planning_id' => null,
                'barangay_id' => $faker->randomElement($barangayIds),
                'municipality_id' => 83707, // Assuming you have Municipality IDs between 1 and 50
                'province_id' => 837, // Assuming you have Province IDs between 1 and 20
                'region_id' => 8, // Assuming you have Region IDs between 1 and 5
                'created_at' => $createdAt,
                'updated_at' =>  $updatedAt,
            ]);
        }

        for ($i = 0; $i < 50; $i++) {   
            foreach ($patientIds as $patientId) {  

                // Generate consultation and dental records for each patient
            $consultationCreatedAt = $faker->dateTimeBetween('-5 years', 'now');
            $consultationUpdatedAt = $faker->dateTimeBetween($consultationCreatedAt, 'now');

            $consultationId = DB::table('consultations')->insertGetId([
                'patient_id' => $patientId,
                'blood_pressure' => $faker->randomElement(['120/80', '130/85', '125/90']),
                'body_temperature' => $faker->randomFloat(1, 35, 40),
                'height' => $faker->numberBetween(140, 200),
                'weight' => $faker->numberBetween(40, 100),
                'chief_complaints' => $faker->randomElement($chiefConsultation),
                'number_of_days' => $faker->numberBetween(1, 30),
                'created_at' => $consultationCreatedAt,
                'updated_at' =>  $consultationUpdatedAt,
            ]);

            $dentalCreatedAt = $faker->dateTimeBetween('-5 years', 'now');
            $dentalUpdatedAt = $faker->dateTimeBetween($dentalCreatedAt, 'now');

            $dentalId = DB::table('dentals')->insertGetId([
                'patient_id' => $patientId,
                'blood_pressure' => $faker->randomElement(['120/80', '130/85', '125/90']),
                'body_temperature' => $faker->randomFloat(1, 35, 40),
                'height' => $faker->numberBetween(140, 200),
                'weight' => $faker->numberBetween(40, 100),
                'chief_complaints' => $faker->randomElement($chiefDental),
                'number_of_days' => $faker->numberBetween(1, 30),
                'created_at' => $dentalCreatedAt,
                'updated_at' =>  $dentalUpdatedAt,
            ]);

            DB::table('consultation_analytics')->insert([
                'patient_id' => $patientId,
                'consultation_id' => $consultationId,
                'barangay_name' => $faker->randomElement($barangayNames),
                'age' => $faker->numberBetween(1, 100),
                'created_at' => $consultationCreatedAt,
                'updated_at' => $consultationUpdatedAt,
            ]);

            DB::table('dental_analytics')->insert([
                'patient_id' => $patientId,
                'dental_id' => $dentalId,
                'barangay_name' => $faker->randomElement($barangayNames),
                'age' => $faker->numberBetween(1, 100),
                'created_at' => $dentalCreatedAt,
                'updated_at' => $dentalUpdatedAt,
            ]);
          
           // Fetch random diagnosis where diagnosis_type is 1 for consultations_diagnosis
            $consultationDiagnosis = DB::table('diagnosis')
            ->where('diagnosis_type', 1)
            ->inRandomOrder()
            ->first(['diagnosis_id']);

            if ($consultationDiagnosis) {
            // Insert into consultations_diagnosis
            DB::table('consultations_diagnosis')->insert([
                'patient_id' => $patientId,
                'diagnosis_id' => $consultationDiagnosis->diagnosis_id,
                'description' => $faker->sentence(6),
                'created_at' => $consultationCreatedAt,
                'updated_at' => $consultationUpdatedAt,
            ]);

            // Store the consultation diagnosis ID for later use
            $consultationDiagnosisId = $consultationDiagnosis->diagnosis_id;
            }

            // Fetch random diagnosis where diagnosis_type is 2 for dental_diagnosis
            $dentalDiagnosis = DB::table('diagnosis')
            ->where('diagnosis_type', 2)
            ->inRandomOrder()
            ->first(['diagnosis_id']);

            if ($dentalDiagnosis) {
            // Insert into dental_diagnosis
            DB::table('dentals_diagnosis')->insert([
                'patient_id' => $patientId,
                'diagnosis_id' => $dentalDiagnosis->diagnosis_id,
                'description' => $faker->sentence(6),
                'created_at' => $dentalCreatedAt,
                'updated_at' => $dentalUpdatedAt,
            ]);

            // Store the dental diagnosis ID for later use
            $dentalDiagnosisId = $dentalDiagnosis->diagnosis_id;
            }

            // Insert into diagnosis_analytics
            $analyticsCreatedAt = $faker->dateTimeBetween('-5 years', 'now');
            $analyticsUpdatedAt = $faker->dateTimeBetween($analyticsCreatedAt, 'now');

            // Ensure that the IDs exist before attempting to insert
            if (isset($consultationDiagnosisId)) {
            DB::table('diagnosis_analytics')->insert([
                'patient_id' => $patientId,
                'diagnosis_id' => $consultationDiagnosisId, // Use the consultation diagnosis ID
                'barangay_name' => $faker->randomElement($barangayNames),
                'age' => $faker->numberBetween(1, 100),
                'created_at' => $analyticsCreatedAt,
                'updated_at' => $analyticsUpdatedAt,
            ]);
            }

            if (isset($dentalDiagnosisId)) {
            DB::table('diagnosis_analytics')->insert([
                'patient_id' => $patientId,
                'diagnosis_id' => $dentalDiagnosisId, // Use the dental diagnosis ID
                'barangay_name' => $faker->randomElement($barangayNames),
                'age' => $faker->numberBetween(1, 100),
                'created_at' => $analyticsCreatedAt,
                'updated_at' => $analyticsUpdatedAt,
            ]);
            }

            }
        }
    }
}