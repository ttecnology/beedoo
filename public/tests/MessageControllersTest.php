<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class MessageControllersTest extends TestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false,
        ]);
    }

    public function testPostWithEmptyMessageReturns400()
    {
        $response = $this->client->post('/messages', [
            'json' => [
                'message' => '',
            ],
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJsonStringEqualsJsonString('{"error": "A mensagem é obrigatória."}', $response->getBody()->getContents());
    }

    public function testPostWithLongMessageReturns400()
    {
        $response = $this->client->post('/messages', [
            'json' => [
                'message' => str_repeat('a', 301),
            ],
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJsonStringEqualsJsonString('{"error": "A mensagem é muito longa."}', $response->getBody()->getContents());
    }

    public function testPostWithValidMessageReturns200()
    {
        $response = $this->client->post('/messages', [
            'json' => [
                'message' => '::Teste::',
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJsonStringEqualsJsonString('{"message": "::Teste::"}', $response->getBody()->getContents());
    }

    public function testGetWithValidSearchParamsReturns200()
    {
        $response = $this->client->get('/messages?search=Hello');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJson($response->getBody()->getContents());
    }

    public function testGetWithEmptySearchParamsReturns200()
    {
        $response = $this->client->get('/messages');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJson($response->getBody()->getContents());
    }
}