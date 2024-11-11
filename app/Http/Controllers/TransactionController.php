<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'receiverPhone' => 'required|string|exists:clients,telephone',
            'amount' => 'required|numeric|min:500',
        ]);

        $user = Auth::user();
        $senderClient = Client::where('user_id', $user->id)->firstOrFail();

        if ($senderClient->solde < $validated['amount']) {
            return response()->json(['message' => 'Solde insuffisant pour le transfert'], 400);
        }

        $receiverClient = Client::where('telephone', $validated['receiverPhone'])->firstOrFail();

        try {
            $transaction = Transaction::create([
                'type' => 'transfert',
                'amount' => $validated['amount'],
                'client_id' => $receiverClient->id,
                'status' => 'validé',
            ]);

            $senderClient->decrement('solde', $validated['amount']);
            $receiverClient->increment('solde', $validated['amount']);

            return response()->json([
                'message' => 'Transfert effectué avec succès',
                'transaction' => $transaction
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du transfert'], 500);
        }
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:500',
            'method' => 'required|string|in:Carte Bancaire,Orange Money',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }
        $client = Client::where('user_id', $user->id)->first();
        

        if (!$client) {
            return response()->json(['message' => 'Client non trouvé pour cet utilisateur'], 404);
        }

        try {
            $transaction = Transaction::create([
                'type' => 'dépôt',
                'amount' => $validated['amount'],
                'client_id' => $client->id,
                'method' => $validated['method'],
                'status' => 'validé',
            ]);

            $client->increment('solde', $validated['amount']);

            return response()->json([
                'message' => 'Dépôt effectué avec succès',
                'transaction' => $transaction
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du dépôt', 'error' => $e->getMessage()], 500);
        }
    }


    public function getTransactionByUser()
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }
    
        // Récupérer le client associé à l'utilisateur
        $client = Client::where('user_id', $user->id)->first();
    
        if (!$client) {
            return response()->json(['message' => 'Client non trouvé pour cet utilisateur'], 404);
        }
    
        // Récupérer les transactions du client avec les informations du client
        $transactions = Transaction::where('client_id', $client->id)
            ->orderBy('created_at', 'desc') // Trier par date de création
            ->get();
    
        // Ajouter les informations du client dans chaque transaction
        $transactionsWithClientInfo = $transactions->map(function ($transaction) use ($client) {
            // Vous pouvez ajouter les informations supplémentaires que vous souhaitez, par exemple:
            $transaction->client_name = $client->surname;
            $transaction->client_phone = $client->telephone; // Exemple d'ajout du téléphone du client
            $transaction->client_solde = $client->solde; // Exemple d'ajout du téléphone du client
            $transaction->client_qr_code = $client->qr_code; // Exemple d'ajout du téléphone du client

            return $transaction;
        });
    
        return response()->json([
            'message' => 'Transactions récupérées avec succès',
            'transactions' => $transactionsWithClientInfo
        ], 200);
    }

    // fonction getBalance() pour afficher le solde du client connectee
    public function getBalance(){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }
        $client = Client::where('user_id', $user->id)->first();
        if (!$client) {
            return response()->json(['message' => 'Client non trouvé pour cet utilisateur'], 404);
        }
        return response()->json([
           'message' => 'Solde du client',
           'solde' => $client->solde,
           'qr_code' => $client->qr_code 
        ], 200);
    }
    


}
