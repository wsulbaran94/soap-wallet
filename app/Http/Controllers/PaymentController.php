<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Wallet;
use App\Models\Payment;

class PaymentController extends Controller
{

    public function payment(Request $request)
    {   
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

        if ($wallet->balance < $amount) {
            return $this->response(false, '06', 'Saldo insuficiente');
        }

        $sessionId = uniqid();
        $token = rand(100000, 999999);

        $buildPayment = [
            'client_id' => $existClient->id,
            'sesion_id' => $sessionId,
            'token' => (string)$token,
            'amount' => $amount,
            'status' => 'PENDING'
        ];

        $payment = Payment::create($buildPayment);

        $data = ['email' => $existClient->email, 'sessionId' => $sessionId, 'token' => $token];
        
        return $this->response(true, '00', 'Pago registrado',  $data);
    }

    public function confirmPayment () {
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

        if (empty($toJson['sessionId']) || empty($toJson['token'])) {
            return $this->response(false, '01', 'Campos requeridos faltantes');
        }

        $sessionId = $toJson['sessionId'];
        $token = $toJson['token'];

        

        $payment = Payment::where('sesion_id', $sessionId)->where('token', $token)->where('status', 'PENDING')->first();

        if (!$payment) {
            return $this->response(false, '07', 'Pago no encontrado o token incorrecto');
        }

        $payment->status = 'CONFIRMED';
        $payment->save();

        $wallet = Wallet::where('client_id', $payment->client_id)->first();

        if ($wallet->balance < $payment->amount) {
            return $this->response(false, '06', 'Saldo insuficiente');
        }

        $wallet->balance = $wallet->balance - $payment->amount;

        $wallet->save();

        return $this->response(true, '00', 'Pago confirmado', $wallet);
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
