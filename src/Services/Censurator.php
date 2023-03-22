<?php

namespace App\Services;

use App\Entity\Wish;
use Doctrine\Common\Collections\Collection;

class Censurator
{
    private array $grosmots;

    public function isGrosMot($motSuspect): bool
    {
        $grosmots = ['pipi', 'caca', 'putain', 'merde'];

        $result = false;
        foreach ($grosmots as $grosmot) {
            if ($motSuspect === $grosmot) {
                $result = true;
            }
        }

        return $result;
    }

    public function __construct()
    {
    }

    public function censure(
        $wishes
    ): Collection {
        foreach ($wishes as $wish) {
            $this->purifyWish($wish);
        }

        return $wishes;
    }

    public function purifyWish(
        $wish
    ): Wish {
        $phrase = $wish->getDescription();
        $titre = $wish->getTitle();
        $phrasecensuree = '';
        $titreCensuree = '';
        $mots = explode(' ', $phrase);
        $motsTitre = explode(' ', $titre);
        foreach ($mots as $mot) {
            if ($this->isGrosMot($mot)) {
                $motCensure = substr($mot, 0, 1);
                for ($i = 1; $i < strlen($mot) - 1; ++$i) {
                    $motCensure .= '*';
                }
                $motCensure .= substr($mot, strlen($mot) - 1, 1);
                $mot = $motCensure;
            }
            $phrasecensuree .= $mot.' ';
        }

        $motsTitre = explode(' ', $titre);
        foreach ($motsTitre as $mot) {
            if ($this->isGrosMot($mot)) {
                $motCensure = substr($mot, 0, 1);
                for ($i = 1; $i < strlen($mot) - 1; ++$i) {
                    $motCensure .= '*';
                }
                $motCensure .= substr($mot, strlen($mot) - 1, 1);
                $mot = $motCensure;
            }
            $titreCensuree .= $mot.' ';
        }
        $wish->setDescription($phrasecensuree);
        $wish->setTitle($titreCensuree);

        return $wish;
    }
}
