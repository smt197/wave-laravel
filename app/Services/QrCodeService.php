<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    public function generateBase64QrCode($data)
    {
        $render = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $qrCode = new Writer($render);
        $qrCodeImage = $qrCode->writeString($data);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);
    }
}