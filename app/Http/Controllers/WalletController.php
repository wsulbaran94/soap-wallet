<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Wallet;

class WalletController extends Controller
{
    //
    public function rechargeWallet (Request $request) {
        $xmlContent = $request->getContent();
        $xml = simplexml_load_string($xmlContent);
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

        return $this->response(true, '00', 'Recarga exitosa', ['balance' => $wallet->balance]);

    }

    public function balance (Request $request) {

        $xmlContent = $request->getContent();
        $xml = simplexml_load_string($xmlContent);
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

        return $this->response(true, '00', 'Consulta exitosa', ['balance' => $wallet->balance]);
    }

    private function response($success, $code, $message, $data = null)
    {
        if($code != '00') {
            return [
                'success' => $success,
                'cod_error' => $code,
                'message_error' => $message
            ];
        }
        
        return [
            'success' => $success,
            'cod_error' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}
