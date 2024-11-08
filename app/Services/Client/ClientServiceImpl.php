<?php
namespace App\Services\Client;

use App\Events\PhotoUploaded;
use App\Exceptions\ServiceError;
use App\Jobs\SendClientEmailJob;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Client\ClientRepository;
use App\Services\Client\ClientService;
use App\Services\PdfService;
use App\Services\QrCodeService;
use App\Services\UploadService;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClientServiceImpl implements ClientService
{
    protected $clientRepository;

    protected $clientService;
    protected $uploadService;
    protected $qrCodeService;
    protected $emailService;
    protected $pdfService;

    public function __construct(ClientRepository $clientRepository,UploadService $uploadService, QrCodeService $qrCodeService, PdfService $pdfService)
    {
        $this->clientRepository = $clientRepository;
        $this->uploadService = $uploadService;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
    }

    public function storeClient(array $data)
    {
        DB::beginTransaction();
    
        try {
            Log::info('Début de storeClient avec les données:', ['data' => $data]);
    
            // Extraire les données du client
            $clientData = array_intersect_key($data, array_flip(['surname','telephone', 'adresse', 'solde', 'soldeMax','cumulTransaction']));
            
            Log::info('Données client extraites:', ['clientData' => $clientData]);
    
            // Vérifier que toutes les données client requises sont présentes
            if (!isset($clientData['surname']) || !isset($clientData['adresse']) || !isset($clientData['telephone']) || !isset($clientData['soldeMax'])){
                throw new Error("Données client incomplètes.");
            }


            // Créer le client
            $client = $this->clientRepository->create($clientData);
            Log::info('Client créé avec succès:', ['client' => $client]);
            
            // Vérifier si les données utilisateur sont fournies
            if (isset($data['user'])) {
                Log::info('Données utilisateur trouvées:', ['userData' => $data['user']]);
    
                // Vérifier que toutes les données utilisateur requises sont présentes
                $requiredUserFields = ['name', 'email', 'password', 'role_id', 'statut'];
                foreach ($requiredUserFields as $field) {
                    if (!isset($data['user'][$field])) {
                        Log::error("Champ utilisateur manquant: $field");
                        throw new Error("Champ utilisateur manquant : $field");
                    }
                }
    
                $roleId = $data['user']['role_id'];
                $role = Role::find($roleId);
                
                if (!$role) {
                    throw new Error("Le rôle spécifié n'existe pas.");
                }
                
                // Préparer les données utilisateur
                $userData = [
                    'name' => $data['user']['name'],
                    'email' => $data['user']['email'],
                    'statut' => $data['user']['statut'],
                    'password' => bcrypt($data['user']['password']),
                    'role_id' => $role->id,
                    'photo' => null,
                ];
                
                Log::info('Données utilisateur préparées:', ['userData' => $userData]);
    
                // Créer l'utilisateur associé
                $user = User::create($userData);
                Log::info('Utilisateur créé avec succès:', ['user' => $user]);
                
                // Associer l'utilisateur au client
                $client->user()->associate($user);
                $client->save();
    
                // Déclencher l'événement de téléchargement de photo
                if (isset($data['user']['photo'])) {
                    $file = $data['user']['photo'];
                    $filePath = $this->uploadService->uploadImageAndConvertToBase64($file);
                    event(new PhotoUploaded($filePath, $user->id));
                    Log::info('Photo uploadée et événement déclenché');
                }
            } else {
                Log::info('Aucune donnée utilisateur fournie');
            }
            
            // Générer le code QR pour le client
            $qrData = $client->surname . ' ' . $client->telephone;
            $qrCodeBase64 = $this->qrCodeService->generateBase64QrCode($qrData);
            $client->qr_code = $qrCodeBase64;
            $client->save();
            Log::info('Code QR généré et sauvegardé');
    
            // Générer le PDF avec le code QR
            $pdf = $this->pdfService->generateQrCodePdf($qrCodeBase64);
            Log::info('PDF généré');
    
            // Dispatcher le Job pour envoyer l'email avec le PDF
            if ($client->user && $client->user->email) {
                SendClientEmailJob::dispatch($client->user->email, $pdf);
                Log::info('Job d\'envoi d\'email dispatché');
            }
    
            DB::commit();
            Log::info('Transaction commise avec succès');
            return $client;
    
        } catch (Error $e) {
            DB::rollBack();
            Log::error('ServiceError attrapée:', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur inattendue:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Error('Erreur inattendue: ' . $e->getMessage());
        }
    }

    public function getAllClients(Request $request){
        return $this->clientRepository->all($request);
    }
  
    public function createClient(array $data)
    {
        return $this->clientRepository->create($data);
    }

    public function getClientById($id)
    {
            return $this->clientRepository->find($id);
    }

    public function getClientByTelephone($telephone)
    {
        return $this->clientRepository->findByTelephone($telephone);
    }

    public function addUserToClient($id, array $data)
    {
        return $this->clientRepository->addUserToClient($id, $data);
    }

    public function updateClient($id, array $data){
        return $this->clientRepository->update($id, $data);
    }
    public function deleteClient($id){
        return $this->clientRepository->delete($id);
    }



    public function getClientWithPhotoInBase64($telephone)
    {
        // Étape 1 : Récupérer le client en fonction du numéro de téléphone
        $client = $this->getClientByTelephone($telephone);
    
        // Étape 2 : Vérifier si le client existe et s'il a une photo
        if ($client && $client->photo) {
            // Étape 3 : Lire le fichier photo depuis le disque
            $path = str_replace('/storage/', '', $client->photo);
            $photoContent = Storage::disk('public')->get($path);
    
            // Étape 4 : Convertir le contenu de la photo en base64
            $client->photo = base64_encode($photoContent);
        }
    
        // Étape 5 : Retourner l'objet client avec la photo en base64 (si disponible)
        return $client;
    }


}
