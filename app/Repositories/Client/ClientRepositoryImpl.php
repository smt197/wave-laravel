<?php
namespace App\Repositories\Client;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Client\ClientRepository;
use Error;
use Exception;

class ClientRepositoryImpl implements ClientRepository
{
    public function all()
    {
        return Client::all();
    }

    public function find($id)
    {
        return Client::find($id);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update($id, array $data)
    {
        $client = $this->find($id);
        $client->update($data);
        return $client;
    }

    public function delete($id)
    {
        $client = $this->find($id);
        $client->delete();
        return $client;
    }

    public function findByTelephone($telephone)
    {
        // return Client::with('user:id,surname,prenom,login,photo')->firstOrFail();
    }
    // recuperer la photo d'un client

    public function findPhoto($id){
        // return Client::find($id)->photo;
    }


    public function addUserToClient($id, array $data)
    {
        try {
            // Recherche du client par son ID
            $client = $this->find($id);

            if ($client->user_id) {
                throw new Error('Ce client a déjà un compte utilisateur.');
            }

            $user = User::create([
                'name' => $data['nom'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'photo' => $data['photo'],
                'statut' => $data['status'],
                'role_id' => $data['role_id'],
            ]);

            $client->user_id = $user->id;
            $client->save();

            return $client;

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'ajout de l\'utilisateur au client: ' . $e->getMessage(),
                'code' => 400
            ], 400);
        } catch (\Throwable $e) {
            // Gestion des autres erreurs non spécifiques
            throw new Error('Une erreur inattendue s\'est produite: ' . $e->getMessage());
        }
    }

   
}
