<?php


use PHPUnit\Framework\TestCase;
use App\Service\ClientService;
use App\Repositories\ClientRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ClientServiceTest extends TestCase {
  use DatabaseTransactions;

  private $clientService;
  private $clientRepository;

  public function setUp(): void {
    $this->clientRepository = $this->createMock(ClientRepository::class);
    $this->clientService = new ClientService($this->clientRepository);
  }

  public function test_register_client() {
    $document = '20202020';
    $name = 'John Perro';
    $email = 'pruebita@yopmail.com' ;
    $phone = '77757575757';

    $this->clientRepository->method('byDocument')->willReturn(false);
    $this->clientRepository->method('create')->willReturn(true);

    $response = $this->clientService->register($document, $name, $email, $phone);

    $this->assertEquals(true, $response['success']);
    $this->assertEquals('00', $response['cod_error']);
    $this->assertEquals('Cliente registrado con éxito', $response['message']);
  }

  public function test_register_client_exists() {
    $document = '20202020';
    $name = 'John Perro';
    $email = 'pruebita@yopmail.com' ;
    $phone = '77757575757';
    
    $this->clientRepository->method('byDocument')->willReturn(true);
    $this->clientRepository->method('create')->willReturn(true);

    $response = $this->clientService->register($document, $name, $email, $phone);

    $this->assertEquals(false, $response['success']);
    $this->assertEquals('09', $response['cod_error']);
    $this->assertEquals('Cliente ya registrado', $response['message_error']);
  }
}