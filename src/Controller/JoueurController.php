<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class JoueurController extends AbstractController
{
    

    /**
     * @Route ("/setCookie")
     *
     * @return void
     */
    public function setCookie(){

        $cookie = new Cookie('ThemePref', 'Rouge', strtotime('now + 10 minutes'));
        $res = new Response();
        $res->headers->setCookie( $cookie );
        $res->send();

    }
    /**
     * @Route("/session")
     *
     * @return void
     */
    public function addSessionVariable(\Symfony\Component\HttpFoundation\Request $req){

        $req->getSession()->set("idJoueur", "31000000");
        $idJoueur = $req->getSession()->get("idJoueur");
        return $this->json($idJoueur);
    }

    /**
     * @Route("/getJson")
     *
     * @return void
     */
    public function getJson(){
        $tab = ["a"=>'1',"b"=>'2',"c"=>'3'];
        return $this->json($tab);
    }

  /**
   * @Route ("/demarrer-partie/{idPartie}/{idJoueur}")
   */
    public function demarrerPartie($idPartie,$idJoueur){
        return $this->json($idJoueur);
    }





    
}
