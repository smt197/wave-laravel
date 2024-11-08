<?php
namespace App\Listeners;

use App\Services\UploadService;
use App\Models\User;
use App\Events\PhotoUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandlePhotoUpload implements ShouldQueue
{
    use InteractsWithQueue;

    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Gérer l'événement de photo uploadée.
     *
     * @param \App\Events\PhotoUploaded $event
     * @return void
     */
    public function handle(PhotoUploaded $event)
    {
        // Traitez le fichier à partir du chemin
    $photoPath = $event->filePath;
    Log::info("Chemin de la photo: " . $photoPath);  // Log pour déboguer

    // Mettre à jour la photo de l'utilisateur
    $user = User::find($event->userId);
    if ($user) {
        $user->photo = $photoPath;
        Log::info("Mise à jour de l'utilisateur avec ID: " . $user->id);  // Log pour déboguer
        $user->save();
    }
}
}