<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testMe()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/me');
    }

    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get');
    }

    public function testModif()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modif');
    }

}
