<?php

namespace App\Tests;

use App\Repository\PartieRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PartieRepositoryTest extends WebTestCase
{
    /**
     *
     * @var App\Repository\PartieRepository
     */
    private $partieRepository;
    
    public function testListerPartieNonDemarreeMoinsDe4Joueur(){
        $res = $this->partieRepository->listePartie();
        $this->assertNotNull($res);
    }
    
    
    protected function setUp() {
        $this->partieRepository = self::bootKernel()->getContainer()->get("App\Repository\PartieRepository");
    }

    
}
