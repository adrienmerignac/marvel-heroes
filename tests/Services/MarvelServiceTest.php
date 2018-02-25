<?php

namespace App\Tests\Services;

use App\Services\MarvelService;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MarvelServiceTest extends TestCase {

    protected $service;
    protected $baseUrl;
    protected $apiKey;
    protected $privateKey;
    protected $client;

    /**
     * Méthode appelée pour chaque test
     */
    protected function setUp() {
        $this->baseUrl = "baseUrl";
        $this->apiKey = "apiKey";
        $this->privateKey = "privateKey";

        $response = new ResponseTest();
        $clientMock = $this->getMockBuilder('Client')
            ->setMethods(array('get'))
            ->getMock();

        $clientMock
            ->method('get')
            ->willReturn($response);

        $this->service = new MarvelService($this->baseUrl, $this->apiKey, $this->privateKey);
        $this->service->setClient($clientMock);
        //$this->client = new ClientTest();

    }

    public function testInit() {
        $this->assertEquals("baseUrl", $this->service->getBaseUrl());
        $this->assertEquals("apiKey", $this->service->getApiKey());
        $this->assertEquals("privateKey", $this->service->getPrivateKey());
        $this->assertEquals(123456789, $this->service->getTs());
    }

    public function testGetWithRouteAndNoParams() {
        $response = $this->service->get("characters");

        $this->assertEquals(3, $response->data->count);
        $this->assertEquals(100, $response->data->total);
        $this->assertEquals(1, $response->data->results[0]->id);
    }

    public function testGetWithRouteAndParams() {
        $params = [
            "orderBy" => "name",
            "limit" => 3
        ];
        $response = $this->service->get("characters", $params);

        $this->assertEquals(3, $response->data->count);
        $this->assertEquals(100, $response->data->total);
        $this->assertEquals(1, $response->data->results[0]->id);
    }
}