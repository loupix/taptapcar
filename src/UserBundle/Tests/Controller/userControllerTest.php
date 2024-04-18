<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class userControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    }

    public function testInscription()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/inscription');
    }

    public function testDeconnexion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deconnexion');
    }

    public function testSupression()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/supression');
    }

}
