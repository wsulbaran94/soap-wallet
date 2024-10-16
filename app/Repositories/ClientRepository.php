<?
namespace App\Repositories;

use App\Models\Client;

class ClientRepository{
  protected $model;

  public function __construct(Client $client) {
    $this->model = $client;
  }

  public function create(array $data)
  {
    return $this->model->create($data);
  }

  public function read($query)
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

  public function byDocument($document)
  {
    $getClient = $this->model->where('document', $document)->first();
    
    if ($getClient) {
      return $getClient;
    }

    return null;
  }
}