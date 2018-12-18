<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\JoueurService;
use App\Repository\JoueurRepository;
use App\Service\PartieService;
use App\Repository\PartieRepository;

class PartieController extends AbstractController
{   
    
     /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion()
    {   
        return $this->render('partie/connexion.html.twig');
    }

    /**
     * @Route("/connexionInit")
     *
     * @param [string] $pseudo
     * @param [int] $avatar
     */
    public function connexionInit(JoueurService $joueurService, Request $req){
        $pseudo = $_POST['pseudo'];
        $avatar = $_POST['avatar'];
        $joueur = $joueurService->inscriptionOuReinitialisation($pseudo,$avatar);
        $this->addSessionVariable("idJoueur",$joueur->getId(),$req);
        return $this->redirectToRoute("app_partie_listepartie");
    }

    /**
     * @Route ("/rejoindrePartie")
     *
     * @return void
     */
    public function rejoindrePartie(PartieService $partieService, Request $req){
        if(isset($_POST['idPartie'])){
            $this->addSessionVariable('idPartie',$_POST['idPartie'],$req);
        }
        $idPartie = $_POST['idPartie'] ?? $this->getSessionVariable('idPartie',$req);
        $idJoueur = $this->getSessionVariable('idJoueur',$req);
        $partie =  $partieService->joinPartie($idPartie,$idJoueur);
        
        return $this->redirectToRoute("app_partie_lobbypartie");
    }

      /**
     * @Route ("/liste-partie")
     */
    public function listePartie(JoueurRepository $joueurRepository, Request $req){
        $idJoueur = $this->getSessionVariable('idJoueur',$req);
        $joueurActuel = $joueurRepository->find($idJoueur);
        return $this->render('partie/liste_partie.html.twig',['joueurActuel'=>$joueurActuel]);
    }

    /**
     * @Route ("/ajaxListePartie")
     *
     * @return void
     */
    public function ajaxListePartie(PartieService $partieService){
        $listeParties = $partieService->listePartie();
        // return $listeParties;
        return $this->render('partie/_liste_partie.html.twig',["listeParties" =>$listeParties]);
    }

    /**
     * @Route ("/creerPartie")
     */
    public function creerPartie(PartieService $partieService){
        $nomPartie = $_POST['nom'];
        $partie =  $partieService->createPartie($nomPartie);
        return $this->json($partie);
    }

    /**
     * @Route ("/lobby")
     */
    public function lobbyPartie(PartieRepository $partieRepository, Request $req){
        $partieActuelle = $partieRepository->find($this->getSessionVariable('idPartie',$req));
        $joueurs = $partieActuelle->getJoueurs();
        return $this->render('partie/_lobby.html.twig',['listeJoueurs'=>$joueurs,'nomPartie'=>$partieActuelle->getNom()]);
    }

     /**
     * @Route ("/lobbyVide")
     */
    public function lobbyPartieVide(PartieRepository $partieRepository, Request $req){
        $partieActuelle = $partieRepository->find($this->getSessionVariable('idPartie',$req));
        return $this->render('partie/lobby.html.twig',['nomPartie'=>$partieActuelle->getNom(),'idPartie'=>$partieActuelle->getId()]);
    }


     /**
     * @Route ("/plateauDeJeu")
     */
    public function plateauDeJeu(PartieService $partieService, PartieRepository $partieRepository, Request $req){
        $idPartie = $this->getSessionVariable("idPartie",$req);
        
        $partie = $partieRepository->find($idPartie);
        if($partie->getEtat() !== "DEMARREE"){
            $partieService->demarrerPartie($idPartie);
        }
     
        $joueurs = $partie->getJoueurs();
        
        foreach($joueurs as $joueur){
            if($joueur->getId() === $this->getSessionVariable('idJoueur',$req)){
                $joueurPrincipal = $joueur;
            }else{
                $joueursAdverses[] = $joueur;
            }
        }

        return $this->render('partie/plateau-de-jeu.html.twig',
                            [
                                'listeJoueursAdverse'=>$joueursAdverses,
                                'joueurActuel'=>$joueurPrincipal
                            ]);
    }
    /**
     * @Route ("/etatPartie")
     *
     * @param PartieRepository $partieRepository
     * @return void
     */
    public function etatPartie(PartieRepository $partieRepository, Request $req){
        $idPartie = $this->getSessionVariable("idPartie",$req);
        $partie = $partieRepository->find($idPartie);
        $etatPartie = $partie->getEtat();
 
        return $this->json($etatPartie);

    }

    public function addSessionVariable($paramName,$paramValue, $req){

        $req->getSession()->set($paramName, $paramValue);
       
    }

    public function getSessionVariable($paramName, $req){
        $paramName = $req->getSession()->get($paramName);
        return $paramName;
    }
}
