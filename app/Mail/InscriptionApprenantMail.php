<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InscriptionApprenantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    protected $pdfFilePath;

    public function __construct($mailData, $pdfFilePath)
    {
        $this->mailData = $mailData;
        $this->pdfFilePath = $pdfFilePath;
        Log::info('Mail Data at construct:', $this->mailData);

    }
    
    public function build()
    {
        $staticData = [
            'email' => 'example@example.com',
            'password' => 'testPassword'
        ];

        return $this->subject('Inscription à la plateforme')
        ->view('pdfs.qr_code', ['mailData' => $staticData])
            ->attach(storage_path('app/public/' . $this->pdfFilePath), [
                'as' => 'qr_code.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
