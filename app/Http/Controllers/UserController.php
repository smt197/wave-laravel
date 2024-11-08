<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    protected $userService;
    protected $exportService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $role = $request->query('role');
            $users = $this->userService->index($role);

            return response()->json([
                'message' => 'Utilisateurs récupérés avec succès',
                'users' => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo');
            }

            $user = $this->userService->createUser($request->validated());
            return  $user;

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage(),
            ], $e->getMessage() === 'Action non autorisée' ? 403 : 500);
        }
    }

    // public function update(UpdateUserRequest $request, $id)
    // {
    //     try {
    //         $data = $request->validated();
            
    //         // Gérer l'upload de fichier
    //         if ($request->hasFile('photo')) {
    //             $data['photo'] = $request->file('photo');
    //         }

    //         $user = $this->userService->updateUser($id, $data);

    //         return response()->json([
    //             'message' => 'Utilisateur mis à jour avec succès',
    //             'user' => $user,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
    //             'error' => $e->getMessage(),
    //         ], $e->getMessage() === 'Action non autorisée' ? 403 : 500);
    //     }
    // }

    // public function exportExcel()
    // {
    //     // Stocker le fichier Excel dans le répertoire storage/app/public
    //     Excel::store(new UserExport, 'users.xlsx', 'public');
    
    //     // Télécharger le fichier après stockage
    //     return Excel::download(new UserExport, 'users.xlsx');
    // }
}