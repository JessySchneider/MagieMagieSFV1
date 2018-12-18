<?php

namespace App\Repository;

use App\Entity\Partie;
use App\Entity\Joueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Partie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partie[]    findAll()
 * @method Partie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartieRepository extends ServiceEntityRepository
{
     
    public function listePartie(){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p');
        $qb->from('App:Partie', 'p');
        // $qb->join('p.joueurs', 'j');
        $qb->where('p.etat = :Etat') ;
        // $qb->having('COUNT(j) <= 4');
        $qb->setParameter('Etat', Partie::ETAT_PARTIE_NON_DEMARREE);
        $query = $qb->getQuery();
        //$res = $query->getScalarResult();
        $listeParties = $query->getResult();

        return $listeParties;
    }
    
     public function ordreMax($partieId){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("MAX(j.ordre) +1");
        $qb->from("App:Joueur", "j");
        $qb->join("j.partie", "p");
        $qb->andWhere("p.id = ".$partieId);
        
        $query = $qb->getQuery();
        $l = $query->getSingleScalarResult();
        
        if($l == null){
            return 1;
        }
        return $l;
    }
    
    public function getJoueurNonElimine($partieid){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("j");
        $qb->from("App:Joueur","j");
        $qb->join("j.partie", "p");
        $qb->where("p.id=:PARTIE_ID");
        $qb->andWhere("j.etat=:NON_ELIMINE");
        $qb->setParameter("PARTIE_ID",$partieid);
        $qb->setParameter("NON_ELIMINE", Joueur::ETAT_PAS_ELIMINE);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Partie::class);
    }
    
 
}