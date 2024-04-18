<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testUsers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users');
    }

    public function testAnnonces()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/annonces');
    }

    public function testReservations()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reservations');
    }

    public function testPaiements()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/paiements');
    }

    public function testContacts()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contacts');
    }

    public function testErreurs()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/erreurs');
    }

}
