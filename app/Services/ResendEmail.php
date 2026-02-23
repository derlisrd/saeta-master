<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResendEmail
{

    public function __construct()
    {
        
    }


    public function sendEmail(string $email, $view){
      
        $http = Http::withHeaders([
            'Authorization' =>  'Bearer ' . env('RESEND_API_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.resend.com/emails', [
            'from' => 'onboarding@resend.dev',
            'to' => $email,
            'subject' => 'Hello World',
            'html' => $view
        ]);

    }
}
