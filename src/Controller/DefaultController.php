<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Services\MarvelService;
use App\Entities\Favoris;


class DefaultController extends Controller
{
    /**
     * Limite de personnages par page
     */
    private $limit;

    /**
     * Marvel Service
     */
    private $service;

    public function __construct(MarvelService $service) {
        $this->service = $service;
        $this->limit = 20;
    }

    /**
     * @Route("/{page}", name="characters_list", requirements={"page" = "\d+"})
     * @Template("list/list.html.twig")
     */
    public function index(Request $request, $page = 6)
    {
        // Création d'une liste de favoris vide par défaut
        $favoris = [];

        // Vérification de l'existance du cookie favoris
        if($request->cookies->get('favoris') !== null) {
            $favoris = unserialize($request->cookies->get('favoris'));
        }

        // Gestion de la pagination (par défaut page 6)
        $page = $page < 1 ? 6 : $page;
        $offset = ($page - 1) * $this->limit;
        
        // Appel de l'API Characters
        $response = $this->service->get("characters", [
            "offset" => $offset,
            "limit" => $this->limit
        ]);

        return array(
            "characters" => $response->data->results,
            "page" => intval($page),
            "totalPages" => round($response->data->total / $this->limit),
            "first" => $offset === 0,
            "last" => $response->data->count !== $this->limit,
            "firstOffset" => $offset + 1,
            "lastOffset" => $offset + $response->data->count,
            "totalResults" => $response->data->total,
            "favoris" => $favoris 
        );
    }

    /**
     * @Route("/details/{id}/", requirements={"id" = "\d+"}, name="characters_detail")
     * @Template("detail/detail.html.twig")
     * @Method({"GET"})
     */
    public function detail(Request $request, $id)
    {   
        $characters = $this->service->get("characters/".$id);
        $comics = $this->service->get("characters/".$id."/comics", [
            "orderBy" => "onsaleDate",
            "limit" => 3
        ]);
        return array(
            "character" => $characters->data->results[0],
            "comics" => $comics->data->results
        );
    }

    /**
     * @Route("/details/{id}/favoris", requirements={"id" = "\d+"}, name="characters_detail_favoris")
     * @Method({"POST"})
     */
    public function favoris(Request $request, $id)
    {
        // Création de la réponse
        $response = new Response();

        // Création d'une liste de favoris vide par défaut
        $favorisList = [];

        // Vérification de l'existance du cookie favoris
        if($request->cookies->get('favoris') !== null) {
            $favorisList = unserialize($request->cookies->get('favoris'));
        }

        // Vérification du nombre de favoris (Maximum 5)
        if(count($favorisList) > 4) {
            // On renvoit le code HTTP 403 (Forbidden)
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $response;
        }

        // Création d'un favoris
        $favoris = new Favoris(intVal($request->request->get('id')), $request->request->get('name'));

        // Ajout du favoris dans la liste des favoris
        array_push($favorisList, $favoris);

        // Création d'un cookie avec la liste des favoris mise à jour
        $cookie = new Cookie('favoris', serialize($favorisList));
        
        // Envoi du cookie dans le header de la réponse 
        $response->headers->setCookie($cookie);

        return $response;
    }
}