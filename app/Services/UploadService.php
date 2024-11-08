<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class UploadService
{
    /**
     * Téléverse une image en local.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path = 'images'): string
    {
        try {
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($path, $filename, 'public');
            return Storage::url($filePath); // URL local
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'upload de l'image : " . $e->getMessage());
        }
    }

    /**
     * Convertir une image en base64 après upload.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function uploadImageAndConvertToBase64(UploadedFile $file, $path = 'images'): string
    {
        // Téléverser l'image
        $filePath = $this->uploadImage($file, $path);
        return $filePath;

        // Lire le contenu du fichier
        $fileContent = Storage::disk('public')->get($filePath);

        // Convertir le contenu en base64
        return base64_encode($fileContent);
    }
}