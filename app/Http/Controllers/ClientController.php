<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\Client\ClientService;
use App\Services\PdfService;
use App\Services\QrCodeService;
use App\Services\UploadService;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    use RestResponseTrait;

    protected $clientService;
    protected $uploadService;
    protected $qrCodeService;
    protected $emailService;
    protected $pdfService;


   
    public function __construct(ClientService $clientService, UploadService $uploadService, QrCodeService $qrCodeService, PdfService $pdfService)
    {
        $this->clientService = $clientService;
        $this->uploadService = $uploadService;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
        // $this->authorizeResource(Client::class, 'client');
    }
    public function index()
    {
        //
    }

   

    
    public function store(StoreClientRequest $request)
    {
        try {
            $client = $this->clientService->storeClient($request->validated());
            return new ClientResource($client);
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500];
        } catch (\Throwable $e) {
            return ['error' => 'Erreur inattendue: ' . $e->getMessage(), StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
