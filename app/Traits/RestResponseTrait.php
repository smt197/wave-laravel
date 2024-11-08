<?php

namespace App\Traits;
use App\Enums\StatusResponseEnum;

trait RestResponseTrait
{
    public function sendResponse(mixed $data = null, StatusResponseEnum $status = StatusResponseEnum::SUCCESS, $message = 'Ressource  trouvée', $codeStatut = 200)
    {
        return response()->json([
            'data' =>$data,
            'status' =>  $status->value,
            'message' => $message,
        ],$codeStatut);
    }
}
