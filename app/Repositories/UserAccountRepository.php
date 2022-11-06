<?php

namespace App\Repositories;

use App\Models\UserAccount;

class UserAccountRepository
{
    private UserAccount $model;

    public function __construct(UserAccount $model)
    {
        $this->model = $model;
    }

    public function findById(int $id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
