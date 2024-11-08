<?php

namespace App\Services;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendQrCodeEmail($email, $pdfFilePath)
    {
        // Envoi de l'email avec le PDF en pièce jointe
        Mail::send([], [], function ($message) use ($email, $pdfFilePath) {
            $message->to($email)
                    ->subject('Votre QR Code')
                    ->attach(storage_path('app/public/' . $pdfFilePath), [
                        'mime' => 'application/pdf',
                    ]);
        });
    }
}
