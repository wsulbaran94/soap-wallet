<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class WalletControllerTest extends TestCase
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
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_balance_success(): void  
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <phone>555123459</phone>
                </request>';

 
        $response= $this->call('POST', 'soap/wallet/balance', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Consulta exitosa',
            'data' => [
                'balance' => $response['data']['balance']
            ]
        ]);
    }

    public function test_empty_values_balance(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document></document>
                    <phone></phone>
                </request>';

        $response= $this->call('POST', 'soap/wallet/balance', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => false,
            'cod_error' => '01',
            'message_error' => 'Campos requeridos faltantes'
        ]);
    }

    public function test_client_not_found_balance(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>1234567824</document>
                    <phone>555123459</phone>
                </request>';

        $response= $this->call('POST', 'soap/wallet/balance', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => false,
            'cod_error' => '04',
            'message_error' => 'Cliente no encontrado'
        ]);
    }

    public function test_reload_wallet_success(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <phone>555123459</phone>
                    <amount>1000</amount>
                </request>';

        $response= $this->call('POST', 'soap/wallet/recharge', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Recarga exitosa',
            'data' => [
                'balance' => $response['data']['balance']
            ]
        ]);
    }

    public function test_empty_values_reload_wallet(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document></document>
                    <phone></phone>
                    <amount></amount>
                </request>';

        $response= $this->call('POST', 'soap/wallet/recharge', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => false,
            'cod_error' => '01',
            'message_error' => 'Campos requeridos faltantes'
        ]);
    }

    public function test_client_not_found_reload_wallet(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <phone>555123456</phone>
                    <amount>1000</amount>
                </request>';

        $response= $this->call('POST', 'soap/wallet/recharge', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => false,
            'cod_error' => '04',
            'message_error' => 'Cliente no encontrado'
        ]);
    }
    
}
