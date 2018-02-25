<?php

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndexWithIntegerParam()
    {
        $client = static::createClient();

        $client->request('GET', '/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndexWithStringParam()
    {
        $client = static::createClient();

        $client->request('GET', '/test');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetailWithIntegerParamAndIdExist()
    {
        $client = static::createClient();

        $client->request('GET', '/details/1011346/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDetailWithIntegerParamAndIdNotExist()
    {
        $client = static::createClient();

        $client->request('GET', '/details/0/');

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testDetailWithStringParam()
    {
        $client = static::createClient();

        $client->request('GET', '/test');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testWithIntegerParamFavorisAndIdExist()
    {
        $client = static::createClient();

        $client->request('POST', '/details/1011346/favoris');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testWithIntegerParamFavorisAndIdNotExist()
    {
        $client = static::createClient();

        $client->request('POST', '/details/0/favoris');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testWithStringParamFavoris()
    {
        $client = static::createClient();

        $client->request('POST', '/details/toto/favoris');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
