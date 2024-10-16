<?php

namespace App\Common\Service;

use Illuminate\Database\Eloquent\Model;

abstract class CRUDServices {
  protected $model;

  protected function __construct(Model $model) {
    $this->model = $model;
  }

  public function create(array $data)
    {
      return $this->model->create($data);
    }

  public function read($id = null)
  {
    if ($id) {
      return $this->model->find($id);
    }

    return $this->model->all();
  }

  public function update($id, array $data)
  {
    $record = $this->model->find($id);
    if ($record) {
      $record->update($data);
      return $record;
    }

    return null;
  }

  public function delete($id)
  {
    $record = $this->model->find($id);
    if ($record) {
      return $record->delete();
    }

    return false;
  }
}