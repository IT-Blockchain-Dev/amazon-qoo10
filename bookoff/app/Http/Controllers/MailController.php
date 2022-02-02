<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDemo;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{
    //
    public function sendEmail(Request $request){
        $email = $request -> email_address;
        $mailData = [
            'title' => '注文受付',
            'body' => 'はじめまして。
                       注文いただきありがとうございます。
                       間もなく発送いたします。お待ちください。
                       よろしくお願いいたします。'
            
        ];

        Mail::to($email)->send(new EmailDemo($mailData));
        
        return response()->json([
            'message' => 'Email has been sent.'
        ],Response::HTTP_OK);
    }

}
