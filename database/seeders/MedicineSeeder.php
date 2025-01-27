<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicineSeeder extends Seeder
{
    public function run()
    {
        DB::table('medicines')->insert([
            ['medicines_name' => 'Amlodipine', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Metformin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Amoxicillin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Albuterol', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Atorvastatin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Ibuprofen', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Paracetamol', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Insulin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Ciprofloxacin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Prednisone', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Nitroglycerin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Salbutamol', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Lisinopril', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Omeprazole', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Clopidogrel', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Salmeterol', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Dexamethasone', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Tetracycline', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Aspirin', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Ranitidine', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Diclofenac', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Diphenhydramine', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Cetirizine', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Loratadine', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Fluticasone', 'medicine_type' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Toothpaste', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Mouthwash', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Fluoride Gel', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Antibiotic Gel', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Anesthetic Gel', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Desensitizing Toothpaste', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Dental Cement', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Oral Antiseptic', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Calcium Supplements', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Chlorhexidine', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Dental Wax', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Teething Gel', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Fluoride Varnish', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Oral Probiotics', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Sodium Bicarbonate Mouthwash', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Dental Implants', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Tooth Whitening Gel', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Gingival Packs', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Root Canal Filling', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Dental Fillings', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['medicines_name' => 'Crown and Bridge Materials', 'medicine_type' => 2, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}