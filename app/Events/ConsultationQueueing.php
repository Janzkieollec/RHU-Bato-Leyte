<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationQueueing implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $patient;
    public $gender;
    public $address;
    public $encryptedPatientId;
    public $emergencyPurposes;

    public function __construct($patientWithAddress, $encryptedPatientId, $emergencyPurposes)
    {
        $this->patient = $patientWithAddress; // The patient with address data
        $this->gender = $this->patient->gender->gender_name ?? null; // Assuming gender relation exists
        $this->encryptedPatientId = $encryptedPatientId; // Set the encrypted patient_id
        $this->emergencyPurposes = $emergencyPurposes; // Add emergency purposes

        // Set address details
        $this->address = [
            'barangay' => $this->patient->barangay_name ?? null,
            'municipality' => $this->patient->municipality_name ?? null,
            'province' => $this->patient->province_name ?? null,
            'region' => $this->patient->region_id ?? null,
        ];
    }
      
    public function broadcastOn()
    {
        return new Channel('consultation-queueing');
    }

    public function broadcastAs()
    {
        return 'consultation-queueing.added';
    }
}