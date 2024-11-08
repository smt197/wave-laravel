<?php
namespace App\Repositories\User;


interface UserRepository
{
    public function create(array $data, string $firebaseUid);
    public function update($uid, array $data);
}
