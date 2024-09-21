<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Wallet;

use SoapWrapper;
use Illuminate\Http\Request;
use Mtownsend\XmlToArray\XmlToArray;

class ClientController extends Controller
{
    //

    public function __construct()
    {
        SoapWrapper::add(function ($service) {
            $service
                ->name('ClientService')
                ->wsdl(public_path('wsdl/client.wsdl'))
                ->trace(true)
                ->cache(0);
        });
    }

    public function register (Request $request) {

        $xmlContent = file_get_contents('php://input');

        libxml_use_internal_errors(true);
        
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo "Error: {$error->message}\n";
            }
            libxml_clear_errors();
            return $this.response(false, '01', 'Error en el XML');
        }

        $toJson = json_decode(json_encode($xml), true);

        $document = $toJson['document'];
        $name = $toJson['name'];
        $email = $toJson['email'];
        $phone = $toJson['phone'];

        if (empty($document) || empty($name) || empty($email) || empty($phone)) {
            return $this.response(false, '02', 'Faltan datos en el XML');
        }

        $client = Client::create([
            'document' => $document,
            'email' => $email,
            'phone' => $phone,
            'name' => $name
        ]);

        $wallet = Wallet::create([
            'client_id' => $client->id,
            'balance' => 0
        ]);

        return $this->response(true, '00', 'Cliente registrado exitosamente');
    }

    private function response($success, $code, $message, $data = null)
    {
        return [
            'success' => $success,
            'cod_error' => $code,
            'message_error' => $message,
            'data' => $data
        ];
    }
}
