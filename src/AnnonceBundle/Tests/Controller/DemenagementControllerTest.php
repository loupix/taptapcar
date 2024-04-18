<?php

namespace AnnonceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemenagementControllerTest extends WebTestCase
{
    public function testAjout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ajout');
    }

    public function testModif()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modif');
    }

    public function testSupprime()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/supprime');
    }

    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get');
    }

}
