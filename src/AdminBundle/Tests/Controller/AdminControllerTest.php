<?php

namespace AdminBundle\Tests\Controller;

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

    public function testContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');
    }

    public function testErreurs()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/erreurs');
    }

}
