<?php

namespace App\Jobs;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendClientEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $login;
    protected $pdfFilePath;

    /**
     * Crée une nouvelle instance du Job.
     *
     * @param string $login
     * @param string $pdfFilePath
     */
    public function __construct($login, $pdfFilePath)
    {
        $this->login = $login;
        $this->pdfFilePath = $pdfFilePath;
    }

    /**
     * Exécute le job.
     *
     * @param EmailService $emailService
     * @return void
     */
    public function handle(EmailService $emailService)
    {
        // Envoyer l'email avec le fichier PDF attaché
        $emailService->sendQrCodeEmail($this->login, $this->pdfFilePath);
    }
}
