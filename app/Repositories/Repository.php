<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Str;

abstract class Repository
{
    /**
     * @var Model|Builder
     */
    protected $model = null;

    /**
     * @param mixed $id
     * @param array $columns
     * @param mixed $relations
     * @return mixed
     */
    public function findById(mixed $id,array $columns = ['*'],mixed $relations = null): mixed
    {

        if (!is_null($relations)) {
            $obj = $this->model::with($relations)->find($id, $columns);
        } else {
            $obj = $this->model::find($id, $columns);
        }
        if (is_null($obj)) {
            throw new RecordsNotFoundException(Str::snake(class_basename($this->model)).' not found');
        }
        return $obj;
    }

    public function findBy(mixed $id,string $column,mixed $relations = null): mixed
    {
        if (!is_null($relations)) {
            $obj = $this->model::with($relations)->where($column, "=",$id)->get();
        } else {
            $obj = $this->model::where($column, "=",$id)->get();
        }
        if (is_null($obj)) {
            throw new RecordsNotFoundException(Str::snake(class_basename($this->model)).' not found');
        }
        return $obj;
    }

    public function getAll(int $paginating = null): mixed
    {
        if ($paginating) {
            return $this->model::paginate($paginating);
        }
        return $this->model::get();
    }

    public function create($data,callable $onSuccess = null)
    {
        $model = $this->model::create($data);
        if(!is_null($model) && !is_null($onSuccess)) $onSuccess($model);
        return $model;
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data): mixed
    {
        return $this->model::where('id', $id)->update($data);
    }

    /**
     * @param $id
     * @return int
     */
    public function delete($id): int
    {
        return $this->model::where('id', $id)->delete($id);
    }
}
