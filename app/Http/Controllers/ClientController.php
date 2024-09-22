<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Wallet;

use Illuminate\Http\Request;

class ClientController extends Controller
{

    public function register (Request $request) {

        $xmlContent = $request->getContent();
        $xml = simplexml_load_string($xmlContent);
        $toJson = json_decode(json_encode($xml), true);

        if (empty($toJson['document']) || empty($toJson['phone']) || empty($toJson['name']) || empty($toJson['email'])) {
            return $this->response(false, '01', 'Campos requeridos faltantes');
        }

        $document = $toJson['document'];
        $name = $toJson['name'];
        $email = $toJson['email'];
        $phone = $toJson['phone'];

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
