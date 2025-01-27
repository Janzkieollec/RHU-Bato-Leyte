<?php

namespace App\Services;

use Twilio\Rest\Client;

class SMSService
{
    public function sendSMS($contact, $message)
    {
        $sid = env('TWILIO_SID');
        $auth_token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_PHONE_NUMBER');
        
        $client = new Client($sid, $auth_token);
        
        try {
            $client->messages->create(
                $contact, // the recipient's phone number
                [
                    'from' => $from, // your Twilio phone number
                    'body' => $message
                ]
            );

            return ['status' => 'success', 'message' => 'Message sent successfully!'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}