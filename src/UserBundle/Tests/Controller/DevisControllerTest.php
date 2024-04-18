<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DevisControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get');
    }

    public function testValider()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/valider');
    }

    public function testRefuser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/refuser');
    }

    public function testGetall()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/getAll');
    }

}
