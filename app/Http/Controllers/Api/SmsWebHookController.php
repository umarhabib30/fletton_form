<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SmsWebHookController extends Controller
{
    public function sendSms(Request $request)
    {
        $request->validate([
            'To' => 'required',
            'Body' => 'required',
            'From' => 'required'
        ]);

        $accountSid = config('services.twilio.sid');
        $authToken = config('services.twilio.auth_token');

        if (empty($accountSid) || empty($authToken)) {
            return response()->json([
                'message' => 'Twilio credentials are not configured (TWILIO_SID/TWILIO_AUTH_TOKEN).',
            ], 500);
        }

        $response = Http::withBasicAuth($accountSid, $authToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                'To' => $request->input('To'),
                'From' => $request->input('From'),
                'Body' => $request->input('Body'),
            ]);

        return response()->json($response->json(), $response->status());
    }
}
