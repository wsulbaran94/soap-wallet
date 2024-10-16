<?php
namespace App\Service;

use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Common\Service\ResponseMessage;

class ClientService {
  protected $clientRepository;

  public function __construct(ClientRepository $clientRepository)
  {
    $this->clientRepository = $clientRepository;
  }

  public function register($document, $name, $email, $phone) {
    $responseMessage = new ResponseMessage();
    $data = [
      'document' => $document,
      'name' => $name,
      'email' => $email,
      'phone' => $phone
    ];
    if (empty($data['document']) || empty($data['phone']) || empty($data['name']) || empty($data['email'])) {
      return $responseMessage->response(false, '01', 'Campos requeridos faltantes');
    }

    $document = $data['document'];
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];

    $exists = $this->clientRepository->byDocument($document);

    if ($exists) {
      return $responseMessage->response(false, '09', 'Cliente ya registrado');
    }

    $this->clientRepository->create($data);
    
    return $responseMessage->response(true, '00', 'Cliente registrado con éxito');
  }
}