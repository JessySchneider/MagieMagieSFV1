<?php
namespace App\Service;

use App\Entity\Carte;

class CarteService {
    //put your code here
    
    
    public function piocherCarte(){
        $carte = new Carte();
        $nr = rand(0,4);
        switch ($nr) {
            case 0 : 
                $carte->setType(Carte::BAVE_CRAPAUD);
                break;
            case 1 :
                $carte->setType(Carte::AILE_CHAUVE_SOURIS);
                break;
            case 2 :
                $carte->setType(Carte::MANDRAGORE);
                break;
            case 3 :
                $carte->setType(Carte::LAPIS_LAZULI);
                break;
            case 4 :
                $carte->setType(Carte::CORNE_LICORNE);
                break;
        }
        return $carte;
    }
}
