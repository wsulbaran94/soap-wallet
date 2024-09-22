<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class ClientControllerTest extends TestCase
{

    use DatabaseTransactions;
    
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_create_client (): void {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document>123456782</document>
                    <name>Jane Doe</name>
                    <phone>555123459</phone>
                    <email>janedoe@yopmail.com</email>
                </request>';

 
        $response= $this->call('POST', 'soap/client/register', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Cliente registrado exitosamente',
            'data' => null
        ]);
    }

    public function test_create_client_empty_values(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                    <document></document>
                    <name></name>
                    <phone></phone>
                    <email></email>
                </request>';

        $response= $this->call('POST', 'soap/client/register', [], [], [], [], $xml);
        
        $response->assertStatus(200);

        $response->assertJson([
            'success' => false,
            'cod_error' => '01',
            'message_error' => 'Campos requeridos faltantes'
        ]);
    }
    
}
