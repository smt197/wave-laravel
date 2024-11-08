<?php
namespace App\Services\User;


interface UserService
{
    public function createUser(array $data);
    public function canCreateUser($userRole, $newUserRole);
    public function index($role = null);
    public function updateUser($uid, array $data);
    public function findUserByEmail($email);
}
