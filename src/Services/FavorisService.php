<?php

namespace App\Services;

use App\Entities\Favoris;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FavorisService {

    public function __construct()
    {

    }

    /**
     * Récupération des favoris
     *
     * @param Request $request
     * @param $name
     * @return array|mixed
     */
    public function getFavoris(Request $request, $name)
    {
        // Création d'une liste de favoris vide par défaut
        $favoris = [];

        // Vérification de l'existance du cookie favoris
        if ($request->cookies->get($name) !== null) {
            $favoris = unserialize($request->cookies->get($name));
        }
        //throw new Exception("Il existe déjà un cookie avec ce nom.");
        return $favoris;
    }

    /**
     * Création d'un favoris et ajout à la liste
     *
     * @param Request $request
     * @param $favorisList
     * @param $name
     * @return Cookie
     */
    public function createFavoris(Request $request, $favorisList, $name): Cookie
    {
        // Création d'un favoris
        $favoris = new Favoris(intVal($request->request->get('id')), $request->request->get('name'));

        // Ajout du favoris dans la liste des favoris
        array_push($favorisList, $favoris);

        // Création d'un cookie avec la liste des favoris mise à jour
        $cookie = new Cookie($name, serialize($favorisList));
        return $cookie;
    }

    /**
     * Vérification du nombre maximum de favoris
     *
     * @param $favorisList
     * @param $size
     */
    public function checkFavoritesSize($favorisList, $size): void
    {
        if (count($favorisList) >= $size) {
            // On renvoit le code HTTP 403 (Forbidden)
            throw new AccessDeniedHttpException("Limite de favoris atteinte");
        }
    }
}