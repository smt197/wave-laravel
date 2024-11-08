<?php

namespace App\Services\Firebase;

interface FirebaseServiceInterface
{
    public function createUser($data);
    public function storeUserDetails($uid, $details);
    public function findAll();
    public function updateUser($uid, $data);
    public function findUserByEmail($email);
}