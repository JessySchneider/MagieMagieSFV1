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
use App\Repository\CarteRepository;

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

    /**
     * @Route ("/castASpell")
     *
     * @param PartieService $partieService
     * @return void
     */
    public function castASpell(PartieService $partieService){
        $cartes = $_POST['cartes'];
        $sort = $partieService->isSortValid($cartes);

        return $this->json($sort);

    }

    /**
     * @Route ("/lancerSort")
     *
     * @param PartieService $partieService
     * @param Request $req
     * @return void
     */
    public function lancerSort(PartieService $partieService, Request $req){
        $cartes = $_POST['cartes'];
        $carteId1 = $cartes[0];
        $carteId2 = $cartes[1];

        $idCible = $_POST['cible'] ?? NULL;
        $carteId3 = $_POST['carteDonnee'] ?? NULL;

        $idPartie = $this->getSessionVariable('idPartie',$req);

        $reponse = $partieService->lancerSort($idPartie,$carteId1,$carteId2,$idCible,$carteId3);
        return $this->json($reponse);
    }

    /**
     * @Route ("/aLaMain")
     *
     * @param PartieRepository $partieRepository
     * @param Request $req
     * @return void
     */
    public function aLaMain(JoueurRepository $joueurRepository, PartieRepository $partieRepository, Request $req){
        $idPartie = $this->getSessionVariable('idPartie',$req);
        $idJoueur = $this->getSessionVariable("idJoueur",$req);
        $ordreJoueur = $joueurRepository->find($idJoueur)->getOrdre();
        $partie = $partieRepository->find($idPartie);

        $ordreActuel = $partie->getOrdreActuel();
        $aLaMain = false;

        if($ordreActuel == $ordreJoueur){
            $aLaMain = true;
        }

        return $this->json($aLaMain);
    }

    /**
     * @Route ("/getCarteJoueurActuel")
     *
     * @param JoueurRepository $joueurRepository
     * @param PartieRepository $partieRepository
     * @param Request $req
     * @return void
     */
    public function getCarteJoueurActuel(JoueurRepository $joueurRepository, PartieRepository $partieRepository, Request $req){
        $idJoueur =  $this->getSessionVariable("idJoueur",$req);
        $joueurActuel = $joueurRepository->find($idJoueur);
       
        return $this->render('partie/_plateau-de-jeu-joueur-actuel.html.twig',  ['joueurActuel'=>$joueurActuel]);
    }

    /**
     * @Route ("/getCarteJoueursAdverses")
     *
     * @param JoueurRepository $joueurRepository
     * @param PartieRepository $partieRepository
     * @param Request $req
     * @return void
     */
    public function getCarteJoueursAdverses(JoueurRepository $joueurRepository, PartieRepository $partieRepository, Request $req){
        $idPartie =  $this->getSessionVariable("idPartie",$req);
        $partie = $partieRepository->find($idPartie);

        $joueurs = $partie->getJoueurs();    
        foreach($joueurs as $joueur){
            if($joueur->getId() === $this->getSessionVariable('idJoueur',$req)){
                $joueurPrincipal = $joueur;
            }else{
                $joueursAdverses[] = $joueur;
            }
        }   
       
        return $this->render('partie/_plateau-de-jeu-joueur-adverse.html.twig',  ['listeJoueursAdverse'=>$joueursAdverses]);
    }

    /**
     * @Route ("/passerTour")
     */
    public function passerTour( PartieService $partieService, Request $req){
        $idPartie = $this->getSessionVariable('idPartie',$req);
        $partieService->passerTour($idPartie);
        return $this->json("ok");
    }

    public function addSessionVariable($paramName,$paramValue, $req){

        $req->getSession()->set($paramName, $paramValue);
       
    }

    public function getSessionVariable($paramName, $req){
        $paramName = $req->getSession()->get($paramName);
        return $paramName;
    }
}
 