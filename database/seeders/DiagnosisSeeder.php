<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosisSeeder extends Seeder
{
    public function run()
    {
        DB::table('diagnosis')->insert([
            ['diagnosis_name' => 'Hypertension Stage 1', 'diagnosis_type' => 1, 'diagnosis_code' => 'I10', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Diabetes Mellitus Type 2', 'diagnosis_type' => 1, 'diagnosis_code' => 'E11', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Pneumonia', 'diagnosis_type' => 1, 'diagnosis_code' => 'J18', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Chronic obstructive pulmonary disease', 'diagnosis_type' => 1, 'diagnosis_code' => 'J44', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Disease of the heart', 'diagnosis_type' => 1, 'diagnosis_code' => 'I51', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Gonorrhea', 'diagnosis_type' => 1, 'diagnosis_code' => 'A54', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'URTI', 'diagnosis_type' => 1, 'diagnosis_code' => 'J06.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Malaria', 'diagnosis_type' => 1, 'diagnosis_code' => 'B50', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Urinary Tract Infection', 'diagnosis_type' => 1, 'diagnosis_code' => 'N39.0', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Musculoskeletal Disease', 'diagnosis_type' => 1, 'diagnosis_code' => 'M79.1', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Allergic Dermatitis', 'diagnosis_type' => 1, 'diagnosis_code' => 'L23', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Aortic Valve Disease', 'diagnosis_type' => 1, 'diagnosis_code' => 'I35', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Anemia', 'diagnosis_type' => 1, 'diagnosis_code' => 'D50', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Otitis Media', 'diagnosis_type' => 1, 'diagnosis_code' => 'H66', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Punctured Wound', 'diagnosis_type' => 1, 'diagnosis_code' => 'S00.91', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Post-Nasal Drip', 'diagnosis_type' => 1, 'diagnosis_code' => 'J31.0', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Hypertension Stage 2', 'diagnosis_type' => 1, 'diagnosis_code' => 'I11', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Bronchial Asthma', 'diagnosis_type' => 1, 'diagnosis_code' => 'J45', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Impetigo', 'diagnosis_type' => 1, 'diagnosis_code' => 'L01.0', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Whooping Cough', 'diagnosis_type' => 1, 'diagnosis_code' => 'A37', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Allergic Rhinitis', 'diagnosis_type' => 1, 'diagnosis_code' => 'J30.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Pediatric Community-Acquired Pneumonia', 'diagnosis_type' => 1, 'diagnosis_code' => 'J18.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Toxic Goiter', 'diagnosis_type' => 1, 'diagnosis_code' => 'E05.0', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Healing Wound', 'diagnosis_type' => 1, 'diagnosis_code' => 'S01.01', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Viral Infection', 'diagnosis_type' => 1, 'diagnosis_code' => 'B34', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Internal Parasitism', 'diagnosis_type' => 1, 'diagnosis_code' => 'B77', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Hypertensive Vascular Disease', 'diagnosis_type' => 1, 'diagnosis_code' => 'I11.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Phimosis', 'diagnosis_type' => 1, 'diagnosis_code' => 'N47', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Abscess (e.g., R Axilla, L Mandibular Area, R Foot)', 'diagnosis_type' => 1, 'diagnosis_code' => 'L02', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Closed Fracture (e.g., L Forearm)', 'diagnosis_type' => 1, 'diagnosis_code' => 'S52.5', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Tonsillopharyngitis', 'diagnosis_type' => 1, 'diagnosis_code' => 'J03.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Amoebiasis', 'diagnosis_type' => 1, 'diagnosis_code' => 'A06', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Benign Positional Vertigo', 'diagnosis_type' => 1, 'diagnosis_code' => 'H81.1', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Hyperglycemia', 'diagnosis_type' => 1, 'diagnosis_code' => 'R73.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Epistaxis', 'diagnosis_type' => 1, 'diagnosis_code' => 'R04.0', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Osteoarthritis', 'diagnosis_type' => 1, 'diagnosis_code' => 'M15', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Infected Wound', 'diagnosis_type' => 1, 'diagnosis_code' => 'T81.4', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Intestinal Amoebiasis', 'diagnosis_type' => 1, 'diagnosis_code' => 'A06.9', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Toothache', 'diagnosis_type' => 2, 'diagnosis_code' => 'K08.8', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Caries (Tooth Decay)', 'diagnosis_type' => 2, 'diagnosis_code' => 'K02', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Gingivitis', 'diagnosis_type' => 2, 'diagnosis_code' => 'K05.0', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Periodontitis', 'diagnosis_type' => 2, 'diagnosis_code' => 'K05.1', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Abscess', 'diagnosis_type' => 2, 'diagnosis_code' => 'K04.6', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Bruxism (Teeth Grinding)', 'diagnosis_type' => 2, 'diagnosis_code' => 'G47.63', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Dental Malocclusion', 'diagnosis_type' => 2, 'diagnosis_code' => 'K07.6', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Tooth Erosion', 'diagnosis_type' => 2, 'diagnosis_code' => 'K03.1', 'created_at' => now(), 'updated_at' => now()],
            ['diagnosis_name' => 'Halitosis (Bad Breath)', 'diagnosis_type' => 2, 'diagnosis_code' => 'R19.3', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}