<?php

namespace App\Http\Controllers;
use SoapWrapper;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Wallet;

class WalletController extends Controller
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

    public function rechargeWallet (Request $request) {
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

        if (empty($toJson['document']) || empty($toJson['phone']) || empty($toJson['amount'])) {
            return $this->response(false, '01', 'Campos requeridos faltantes');
        }

        $document = $toJson['document'];
        $phone = $toJson['phone'];
        $amount = $toJson['amount'];

        $existClient = Client::where('document', $document)->where('phone', $phone)->first();

        if (!$existClient) {
            return $this->response(false, '04', 'Cliente no encontrado');
        }

        $wallet = Wallet::where('client_id', $existClient->id)->first();

        if (!$wallet) {
            return $this->response(false, '05', 'Billetera no encontrada');
        }

        $wallet->balance = $wallet->balance + (int)$amount;

        $wallet->save();

        return $this->response(true, '00', 'Recarga exitosa', $wallet);

    }

    public function balance (Request $request) {
        $xmlContent = file_get_contents('php://input');

        libxml_use_internal_errors(true);
        
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo "Error: {$error->message}\n";
            }
            libxml_clear_errors();
            return $this->response(false, '01', 'Error en el XML');
        }

        $toJson = json_decode(json_encode($xml), true);

        if (empty($toJson['document']) || empty($toJson['phone'])) {
            return $this->response(false, '01', 'Campos requeridos faltantes');
        }

        $document = $toJson['document'];
        $phone = $toJson['phone'];

        

        $existClient = Client::where('document', $document)->where('phone', $phone)->first();

        if (!$existClient) {
            return $this->response(false, '04', 'Cliente no encontrado');
        }

        $wallet = Wallet::where('client_id', $existClient->id)->first();

        if (!$wallet) {
            return $this->response(false, '05', 'Billetera no encontrada');
        }

        return $this->response(true, '00', 'Consulta exitosa', $wallet);
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
