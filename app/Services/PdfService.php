<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateQrCodePdf($qrCodeBase64)
    {
        $pdf = Pdf::loadView('pdfs.qr_code', compact('qrCodeBase64'));
        
        $pdfFilePath = 'pdfs/apprenant_qrcode_' . time() . '.pdf';
        
        \Illuminate\Support\Facades\Storage::put('public/' . $pdfFilePath, $pdf->output());

        return $pdfFilePath;
    }
}