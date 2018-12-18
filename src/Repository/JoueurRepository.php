<?php

namespace App\Repository;

use App\Entity\Joueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Joueur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joueur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joueur[]    findAll()
 * @method Joueur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoueurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Joueur::class);
    }
    
    public function rechercherJoueurParPartieIdEtOrdre($partieId, $ordre){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("j");
        $qb->from("App:Joueur", "j");
        $qb->join("j.partie","p");
        $qb->where("p.id=:ID_PARTIE");
        $qb->andWhere("j.ordre=:ORDRE");
        $qb->setParameter("ORDRE", $ordre);
        $qb->setParameter("ID_PARTIE", $partieId);
        
        $query = $qb->getQuery();
        return $query->getSingleResult();
    }
}
