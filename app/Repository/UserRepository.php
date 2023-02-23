<?php

namespace App\Repository;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User;
    }

    public function get($filter)
    {
        $role = (isset($filter['role']) and $filter['role']) ? $filter['role'] : '';
        $this->applyFilter($filter);

        if ($role) {
            $this->model = $this->model->where('type', $role);
        }

        return $this->getOrPaginate($filter);
    }

    public function create($data): User
    {
        return User::create($data->toArray());
    }

    public function update($model, $data): User
    {
        $model->update($data->toArray());

        if ($data->password) {
            $model->password = $data->password;
            $model->save();
        }

        return $model->refresh();
    }
}
