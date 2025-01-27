<?php
namespace App\Events;

use App\Models\PatientLimit;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PatientLimitSet implements ShouldBroadcast
{
    public $message;
    public $patientLimit;

    public function __construct($message, PatientLimit $patientLimit)
    {
        $this->message = $message;
        $this->patientLimit = $patientLimit; // Send patient limit data
    }

    public function broadcastOn()
    {
        return new Channel('nurse-notifications');
    }

    public function broadcastAs()
    {
        return 'max-patients-set';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'patientLimit' => [
                'current_patients' => $this->patientLimit->current_patients,
                'max_patients' => $this->patientLimit->max_patients,
            ]
        ];
    }
}