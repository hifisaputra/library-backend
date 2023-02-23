<?php

namespace App\Repository;

class BaseRepository
{
    protected $model;

    protected $originalModel;

    public function get($filter)
    {
        return $this->applyFilter($filter)->getOrPaginate($filter);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($model, $data)
    {
        if (is_integer($model)) $model = $this->model->findOrFail($model);

        $model->update($data);

        return $model->refresh();
    }

    public function show($model)
    {
        if (is_integer($model) || is_string($model)) $model = $this->model->findOrFail($model);

        return $model;
    }

    public function delete($model)
    {
        if (is_integer($model)) $model = $this->model->findOrFail($model);

        $model->delete();

        return $model;
    }

    public function applyFilter($filter)
    {
        $search = (isset($filter['search']) and $filter['search']) ? '%' . $filter['search'] . '%' : '';
        $order = (isset($filter['order']) and $filter['order']) ? $filter['order'] : '';

        $from = (isset($filter['from']) and $filter['from']) ? $filter['from'] : '';
        $to = (isset($filter['to']) and $filter['to']) ? $filter['to'] : '';

        $this->applySearch($search)->applyOrder($order)->applyDate($from, $to);

        return $this;
    }

    public function applySearch($search): BaseRepository
    {
        if ($search && method_exists($this->model, 'search')) {
            $this->model = $this->model->search($this->model, $search);
        }

        return $this;
    }

    public function applyDate($from = '', $to = ''): BaseRepository
    {
        if ($from) {
            $this->model = $this->model->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $this->model = $this->model->whereDate('created_at', '<=', $to);
        }

        return $this;
    }

    public function applyOrder($order): BaseRepository
    {
        if ($order) {
            $order = explode('-', $order);
            $fillable = $this->originalModel->getFillable();

            $column = $order[0] ? $order[0] : '';
            $direction = $order[1] ? strtolower($order[1]) : '';

            if (in_array($column, $fillable) && ($direction === 'asc' || $direction === 'desc')) {
                $this->model = $this->model->orderBy($column, $direction);
            }
        } else {
            $this->model = $this->model->latest();
        }

        return $this;
    }

    public function getOrPaginate($filter)
    {
        if (isset($filter['limit']) && $filter['limit'])
            return $this->model->paginate($filter['limit']);

        return $this->model->get();
    }
}
