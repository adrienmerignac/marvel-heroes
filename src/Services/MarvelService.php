<?php

namespace App\Services;

use \GuzzleHttp\Client;

/**
 * Marvel Service
 * Requests the Marvel API
 */
class MarvelService {
    
    private $client;
    private $baseUrl;
    private $apiKey;
    private $privateKey;
    private $ts;

    public function __construct($baseUrl, $apiKey, $privateKey)
    {
        $this->client = new Client();

        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->privateKey = $privateKey;
        $this->ts = 123456789;
    }

    /**
     * Get resquest to Marvel API
     *
     * @param $route
     * @param array $params
     * @return mixed
     */
    public function get($route, $params = []) {
        $queryParams = array_merge($params, [
            "apikey" => $this->apiKey,
            "ts" => $this->ts,
            "hash" => $this->getHash()
        ]);

        $url = $this->baseUrl . $route;
        $response = $this->client->get($url, [
            "query" => $queryParams
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Generates the hash
     */
    private function getHash() {
        return md5($this->ts . $this->privateKey . $this->apiKey);
    }

    public function getClient(){
		return $this->client;
    }
    
    public function getBaseUrl(){
		return $this->baseUrl;
    }
    
    public function getApiKey(){
		return $this->apiKey;
    }
    
    public function getTs(){
		return $this->ts;
    }
    
    public function getPrivateKey(){
		return $this->privateKey;
	}

	public function setClient($client){
        $this->client = $client;
    }
}