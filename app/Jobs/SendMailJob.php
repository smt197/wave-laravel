<?php

namespace App\Jobs;

use App\Mail\InscriptionApprenantMail; // Correct import
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $pdfFilePath;
    protected $mailData;

    public function __construct($email, $pdfFilePath, $mailData)
    {
        $this->email = $email;
        $this->pdfFilePath = $pdfFilePath;
        $this->mailData = $mailData;
    }

    public function handle(EmailService $emailService)
    {
        $emailService->sendQrCodeEmail($this->email, $this->pdfFilePath, $this->mailData);
    }
}