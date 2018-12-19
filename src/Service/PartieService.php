<?php

namespace App\Service;

use App\Entity\Partie;
use App\Entity\Joueur;
use App\Entity\Carte;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Description of PartieService
 *
 * @author Administrateur
 */
class PartieService {
    
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @var \App\Repository\PartieRepository
     */
    private $pr;
    
    /**
     * @var \App\Repository\JoueurRepository
     */
    private $jr;
    
    /**
     * @var \App\Service\CarteService
     */
    private $cs;
    
    /**
     * @var \App\Service\CarteRepository
     */
    private $cr;
    
    function __construct(EntityManagerInterface $em, \App\Repository\PartieRepository $pr, \App\Repository\JoueurRepository $jr, \App\Service\CarteService $cs, \App\Repository\CarteRepository $cr ) {
        $this->em = $em;
        $this->pr = $pr;
        $this->jr = $jr;
        $this->cs = $cs;
        $this->cr = $cr;
    }
    public function listePartie(){
        $parties = $this->pr->listePartie();
        return $parties;
    }
    public function createPartie($nom){
        $partie = new Partie();
        $partie->setNom($nom);
        $partie->setEtat(Partie::ETAT_PARTIE_NON_DEMARREE);
        $this->em->persist($partie);
        $this->em->flush();
        
        return $partie;
    }
    
    public function joinPartie($partieID, $joueurID){
        $partie = $this->pr->find($partieID);
        
        if(count($partie->getJoueurs()) >= 5 ){
            throw new Exception("La partie contient deja 5 joueurs");     
        }
        
        $joueur = $this->jr->find($joueurID);
        $partie->addJoueur($joueur);
        $ordre = $this->pr->ordreMax($partieID);
        $joueur->setOrdre($ordre);
        
        $this->em->flush();
        return $partie;   
    } 
    
    public function demarrerPartie($idPartie){
        $partie = $this->pr->find($idPartie);
        $partie->setOrdreActuel(1);
        foreach($partie->getJoueurs() as $joueurAct ){
            for($i = 0; $i < 7; $i++){
                $carte = $this->cs->piocherCarte();
                $joueurAct->addCarte($carte);
                $carte->setJoueur($joueurAct);
                $this->em->persist($carte);
            }
            $joueurAct->setEtat(Joueur::ETAT_PAS_ELIMINE);
        }
        // definir etat des joueurs
        $partie->setEtat(Partie::ETAT_PARTIE_DEMARREE);
        $this->em->flush();
        return 'ok';
    }
    
    
    public function passerJoueurSuivant($partieid){
        $partie = $this->pr->find($partieid);
        $co = $partie->getOrdreActuel();
        $joueurs = $this->pr->getJoueurNonElimine($partieid);
        $nbjoueur = count($joueurs);
        if($nbjoueur == 1){
            $joueur = $this->jr->rechercherJoueurParPartieIdEtOrdre($partieid, $co);
            $joueur->setEtat(Joueur::ETAT_GAGNER);
        }
        
        foreach($joueurs as $joueur){
            $ordre[] = $joueur->getOrdre();
        }
        
        sort($ordre);
        $indice = array_search($co, $ordre);
        if($indice +1 >= $nbjoueur){
            $indice = 0;
        }
        else {
            $indice++;
        }

        $partie->setOrdreActuel($ordre[$indice]);
        
        $this->em->flush();
        return "ok"; 
    }
    
   
    public function passerTour($partieid){        
        $partie = $this->pr->find($partieid);
        $ordreActuel = $partie->getOrdreActuel();
        $joueur = $this->jr->rechercherJoueurParPartieIdEtOrdre($partieid, $ordreActuel);
        $carte = $this->cs->piocherCarte();
        $joueur->addCarte($carte);
        $carte->setJoueur($joueur);
        $this->em->persist($carte);
        $this->em->flush();
        $this->passerJoueurSuivant($partieid);
    }
    
    public function lancerSort($partieid, $carteid1, $carteid2, $targetid =null, $carteid3=null){
        $partie = $this->pr->find($partieid);
        $ordreActuel = $partie->getOrdreActuel();
        
        $joueur = $this->jr->rechercherJoueurParPartieIdEtOrdre($partieid, $ordreActuel);
        
        $typeCarte1 = $this->cr->find($carteid1)->getType();
        $typeCarte2 = $this->cr->find($carteid2)->getType();
        
        $cartesSortJouees = array($typeCarte1,$typeCarte2);
        
        $string;

        if(in_array(Carte::CORNE_LICORNE, $cartesSortJouees) && in_array(Carte::BAVE_CRAPAUD, $cartesSortJouees)){
            $this->sortInvisibilite($partie, $joueur);
            $string = "invisibilite";
        }elseif(in_array(Carte::CORNE_LICORNE, $cartesSortJouees) && in_array(Carte::MANDRAGORE, $cartesSortJouees)){
            $this->sortPhiltredAmour($joueur, $targetid);
            $string = "philtre d'amour";
        }elseif(in_array(Carte::BAVE_CRAPAUD, $cartesSortJouees) && in_array(Carte::LAPIS_LAZULI, $cartesSortJouees)){
            $this->sortHypnose($joueur, $targetid, $carteid3);
            $string =  "hypnose";
        }elseif(in_array(Carte::AILE_CHAUVE_SOURIS, $cartesSortJouees) && in_array(Carte::LAPIS_LAZULI, $cartesSortJouees)){
            $this->sortDivination($partie, $joueur->getId());
            $string = "Divination";
        }else{
            $string = "Sort invalide";
            // throw new Exception("Sort invalide");
        }

        
        $this->em->remove($this->cr->find($carteid1));
        $this->em->remove($this->cr->find($carteid2));

        $joueurs = $partie->getJoueurs();
        foreach($joueurs as $joueur){
            if(count($joueur->getCartes()) == 0){
                $joueur->setEtat(Joueur::ETAT_ELIMINE);
            }
        }
        
        $this->em->flush();
        $this->passerJoueurSuivant($partieid);
        
        return $string;
    }


    public function isSortValid($tabCartesId){

        $typeCarte1 = $this->cr->find($tabCartesId[0])->getType();
        $typeCarte2 = $this->cr->find($tabCartesId[1])->getType();
        
        $cartesSortJouees = array($typeCarte1,$typeCarte2);

        if(in_array(Carte::CORNE_LICORNE, $cartesSortJouees) && in_array(Carte::BAVE_CRAPAUD, $cartesSortJouees)){
            // $this->sortInvisibilite($partie, $joueur);
            return $string = "Invisibilite";
        }elseif(in_array(Carte::CORNE_LICORNE, $cartesSortJouees) && in_array(Carte::MANDRAGORE, $cartesSortJouees)){
            // $this->sortPhiltredAmour($joueur, $targetid);
            return $string = "Philtre d'amour";
        }elseif(in_array(Carte::BAVE_CRAPAUD, $cartesSortJouees) && in_array(Carte::LAPIS_LAZULI, $cartesSortJouees)){
            // $this->sortHypnose($joueur, $targetid, $carteid3);
            return $string =  "Hypnose";
        }elseif(in_array(Carte::AILE_CHAUVE_SOURIS, $cartesSortJouees) && in_array(Carte::LAPIS_LAZULI, $cartesSortJouees)){
            // $this->sortDivination($partie, $joueur->getId());
            return $string = "Divination";
        }else{
            return $string = "Sort invalide";
        }
    }
    
    private function sortInvisibilite($partie, $lanceur){
       $joueurs = $this->pr->getJoueurNonElimine($partie->getId());
       $cartesRecup = array();
       foreach($joueurs as $joueur){
           if($joueur->getId() != $lanceur->getId()){
               $carteJoueur = $joueur->getCartes();   
               $nbcarte = count($carteJoueur);
               
               if($nbcarte == 1){
                   $cartesRecup[] = $carteJoueur[0];
               } 
               else {
                    $indiceAleatoire = rand(0, $nbcarte-1);
                    $cartesRecup[] = $carteJoueur[$indiceAleatoire];
                    $joueur->removeCarte($carteJoueur[$indiceAleatoire]);
               }
           }
        }
        foreach ($cartesRecup as $carte){
            $lanceur->addCarte($carte);
            $carte->setJoueur($lanceur);
        }
        $this->em->flush();
    }
    
    private function sortPhiltredAmour($lanceur, $targetid){
        $joueurTarget = $this->jr->find($targetid);
        $cartesJoueurTarget = $joueurTarget->getCartes();
        $nb = count($cartesJoueurTarget)/2;
        var_dump($nb);
        $nbCarteAVoler = ceil($nb);
        var_dump($nbCarteAVoler);
        
        $this->volerCarte($joueurTarget,$lanceur,$nbCarteAVoler);
        $this->em->flush();
    }
    
    private function sortHypnose($lanceur, $targetid, $carte3id){
        $joueurTarget = $this->jr->find($targetid);
        $this->volerCarte($joueurTarget,$lanceur,3);
        
        $carteDonnee = $this->cr->find($carte3id);
        $this->ajouterCarte($lanceur,$joueurTarget,$carteDonnee);
        $this->em->flush();
    }
    
    private function sortDivination($partie, $lanceurid){
        $joueurs = $this->pr->getJoueurNonElimine($partie->getId());
        $cartesDesJoueurs = array();
        foreach($joueurs as $joueur){
            if($joueur->getId() != $lanceurid){
                $cartesjoueur = array();
                $cartes = $joueur->getCartes();
                foreach($cartes as $carte){
                    $cartesjoueur[] = $carte->getType();
                } 
                $cartesDesJoueurs[$joueur->getId()] = $cartesjoueur;   
            }
        }
        return $cartesDesJoueurs;
    }
    
    private function volerCarte($target,$lanceur,$nbCarteAVoler){
        $cartesJoueurTarget = $target->getCartes();
        $carteArray = array();
        foreach ($cartesJoueurTarget as $carte){
            $carteArray[] = $carte;
        }
        $nbCarteAVolerTemp = $nbCarteAVoler;
        for($i=0;$i<$nbCarteAVoler;$i++){
            $indice = rand(0, $nbCarteAVolerTemp-1);
            $carteVolee = $carteArray[$indice];
            array_splice($carteArray, $indice, 1);
            $this->ajouterCarte($target,$lanceur,$carteVolee);
            $nbCarteAVolerTemp--;
        }
        $this->em->flush();
    }

    private function ajouterCarte($joueurCible,$joueurBeneficiaire,$carte){
        $joueurCible->removeCarte($carte);
        $joueurBeneficiaire->addCarte($carte);
        $carte->setJoueur($joueurBeneficiaire);
        $this->em->flush();
    }
    
}
