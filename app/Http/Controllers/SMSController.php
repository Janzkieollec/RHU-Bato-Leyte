<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use GuzzleHttp\Client;
use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\ApiException;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Illuminate\Support\Str;

class SMSController extends Controller
{
    public function sendInfobipSMS(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string',
            'message' => 'required|string',
        ]);
    
        // Format the contact number to include the country code
        $contact = ltrim($validated['contact'], '0'); // Remove leading zero
        $formattedContact = '63' . $contact;
    
        $configuration = new Configuration(
            host: '4ej3kp.api.infobip.com',
            apiKey: '14ce4b208c7d7c737168ef7e189a8e04-3d909ea6-e767-489d-8407-12ae18eff443'
        );
    
        $sendSmsApi = new SmsApi(config: $configuration);
    
        $message = new SmsTextualMessage(
            destinations: [
                new SmsDestination(to: $formattedContact) // Use the formatted contact number
            ],
            from: 'RHU-Bato',
            text: $validated['message']
        );
    
        $request = new SmsAdvancedTextualRequest(messages: [$message]);
    
        try {
            $smsResponse = $sendSmsApi->sendSmsMessage($request);
            return response()->json(['status' => 'success', 'message' => 'Message sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}