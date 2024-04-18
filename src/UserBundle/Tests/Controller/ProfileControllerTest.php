<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');
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

    public function testAvis()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/avis');
    }

    public function testPaiement()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/paiement');
    }

    public function testModification()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modification');
    }

    public function testGestion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/gestion');
    }

}
