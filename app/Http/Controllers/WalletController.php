<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Wallet;

class WalletController extends Controller
{
    //

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

        $document = $toJson['document'];
        $phone = $toJson['phone'];
        $amount = $toJson['amount'];

        if (empty($document) || empty($phone) || empty($amount)) {
            return $this->response(false, '01', 'Campos requeridos faltantes');
        }

        $existClient = Client::where('document', $document)->where('phone', $phone)->first();

        if (!$existClient) {
            return $this->response(false, '04', 'Cliente no encontrado');
        }

        $wallet = Wallet::where('client_id', $existClient->id)->first();

        if (!$wallet) {
            return $this->response(false, '05', 'Billetera no encontrada');
        }

        $wallet->amount = $wallet->amount + (int)$amount;

        $wallet->save();

        return $this->response(true, '00', 'Recarga exitosa', $wallet);

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
