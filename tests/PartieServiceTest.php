<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PartieServiceTest extends WebTestCase
{
  /**
     *
     * @var App\Repository\PartieService
     */
    private $partieService;
    
    //public function testCreatePartieOK(){
        //$partie = $this->partieService->createPartie("partie ??? ");
      //  $this->assertNotNull($partie);
    //} 
    
//    public function testjoinPartieOK(){
//       $bool = $this->partieService->joinPartie(1,1);
//       $bool = $this->partieService->joinPartie(1,2);
//       $bool = $this->partieService->joinPartie(1,3);
//       $bool = $this->partieService->joinPartie(1,4);
//       $this->assertNotEquals(0,$bool);
//    }
    
//    public function testDemarrerPartieOK(){
//        $string = $this->partieService->demarrerPartie(1);
//        $this->assertEquals('ok', $string);
//    }

   public function testlistepartieOK(){
       $partie = $this->partieService->listePartie();
       $this->assertNotNull($partie);
   }
    
    // public function testpasserJoueurSuivantOK(){
    //    $this->partieService->passerTour(1);
    //    $this->assertTrue(true);
    // }
    
//    public function testLancerSortOK(){
//        $string = $this->partieService->lancerSort(1, 23, 100,1,25);
//        $this->assertEquals("invisibilite",$string);
//    }
   
   
     protected function setUp() {
        $this->partieService = self::bootKernel()->getContainer()->get("App\Service\PartieService");
    }
   
}
