<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    private Comment $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function findAverageByTeacherId(int $teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)->avg('rating');
    }

    public function findAverageByUserAccountId(int $userAccountId)
    {
        return $this->model->where('user_account_id', $userAccountId)->avg('rating');
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }
}
