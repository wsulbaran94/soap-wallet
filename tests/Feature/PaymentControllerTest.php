<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Pre-registro de cliente
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <name>Jane Doe</name>
                    <phone>555123459</phone>
                    <email>janedoe@yopmail.com</email>
                </request>';

        $this->call('POST', 'soap/client/register', [], [], [], [], $xml);

        // Recarga de billetera
        $xmlWallet = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <phone>555123459</phone>
                    <amount>1000</amount>
                </request>';
        
        $this->call('POST', 'soap/wallet/recharge', [], [], [], [], $xmlWallet);
    }

    public function test_create_payment(): void
    {

        $xmlPayment = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <phone>555123459</phone>
                    <amount>1000</amount>
                </request>';
        
        $responsePayment = $this->call('POST', 'soap/payment', [], [], [], [], $xmlPayment);

        $responsePayment->assertStatus(200);
        $responsePayment->assertJson([
            'success' => true,
            'cod_error' => '00',
            'message' => 'Pago registrado',
        ]);
    }

    public function test_create_payment_with_missing_fields(): void
    {
        $xmlPayment = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456789</document>
                    <amount>100</amount>
                </request>';
        
        $responsePayment = $this->call('POST', 'soap/payment', [], [], [], [], $xmlPayment);

        $responsePayment->assertStatus(200);
        $responsePayment->assertJson([
            'success' => false,
            'cod_error' => '01',
            'message_error' => 'Campos requeridos faltantes',
        ]);
    }

    public function test_create_payment_with_client_not_found(): void
    {
        $xmlPayment = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>1234567892</document>
                    <phone>555123456</phone>
                    <amount>100</amount>
                </request>';
        
        $responsePayment = $this->call('POST', 'soap/payment', [], [], [], [], $xmlPayment);

        $responsePayment->assertStatus(200);
        $responsePayment->assertJson([
            'success' => false,
            'cod_error' => '04',
            'message_error' => 'Cliente no encontrado',
        ]);
    }

    public function test_create_payment_insufficient_wallet_balance(): void
    {
        $xmlPayment = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <phone>555123459</phone>
                    <amount>50000</amount>
                </request>';
        
        $responsePayment = $this->call('POST', 'soap/payment', [], [], [], [], $xmlPayment);

        $responsePayment->assertStatus(200);
        $responsePayment->assertJson([
            'success' => false,
            'cod_error' => '06',
            'message_error' => 'Saldo insuficiente',
        ]);
    }

}
