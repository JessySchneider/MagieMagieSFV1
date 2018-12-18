<?php

namespace App\Service;

use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JoueurService
 *
 * @author Administrateur
 */
class JoueurService {
    
    private $joueurRepository;
    private $entityManager;

    public function __construct( \App\Repository\JoueurRepository $rep, \Doctrine\ORM\EntityManagerInterface $em) {
      $this->joueurRepository = $rep;
      $this->entityManager = $em;
    }
    
    public function inscriptionOuReinitialisation($pseudo, $avatar){
        $joueur = $this->joueurRepository->findOneByPseudo($pseudo);
        
        if($joueur == null){
            $joueur = new \App\Entity\Joueur();
            $joueur->setPseudo($pseudo);
            $joueur->setAvatar($avatar);
            $this->entityManager->persist($joueur);
            
        } else {
            $joueur->setAvatar($avatar);
            foreach($joueur->getCartes() as $carteAct){
                $joueur->removeCarte($carteAct);
                $this->entityManager->remove($carteAct);
            }
        }
        $this->entityManager->flush();
        return $joueur;
    }
}