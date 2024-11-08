<?php
namespace App\Repositories\Client;

use Illuminate\Http\Request;

interface ClientRepository
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByTelephone($telephone);
    public function addUserToClient($id, array $data);
    public function findPhoto($id);
}
