<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Services\MarvelService;
use App\Services\FavorisService;

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

    /**
     * Favoris Service
     */
    private $favorisService;

    protected $requestStack;

    public function __construct(MarvelService $service, FavorisService $favorisService, RequestStack $requestStack) {
        $this->service = $service;
        $this->limit = 20;
        $this->favorisService = $favorisService;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/{page}", name="characters_list", requirements={"page" = "\d+"})
     * @Template("list/list.html.twig")
     * @param int $page
     * @return array
     */
    public function index($page = 6)
    {
        $request = $this->requestStack->getCurrentRequest();

        // Création d'une liste de favoris vide par défaut
        $favoris = $this->favorisService->getFavoris($request, "characters");

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
     * @param $id
     * @return array
     */
    public function detail($id)
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
     * @Route("/comic/{page}", name="comics_list", requirements={"page" = "\d+"})
     * @Template("list/comic.html.twig")
     * @param int $page
     * @return array
     */
    public function comics($page = 8)
    {

        $request = $this->requestStack->getCurrentRequest();
        $favoris = $this->favorisService->getFavoris($request, "comics");


        // Gestion de la pagination (par défaut page 6)
        $page = $page < 1 ? 6 : $page;
        $offset = ($page - 1) * $this->limit;

        // Appel de l'API Characters
        $response = $this->service->get("comics", [
            "offset" => $offset,
            "limit" => $this->limit
        ]);

        return array(
            "comics" => $response->data->results,
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
     * @Route("/comic/{id}/", requirements={"id" = "\d+"}, name="comics_detail")
     * @Template("detail/comicdetail.html.twig")
     * @Method({"GET"})
     * @param $id
     * @return array
     */
    public function comicDetail($id)
    {
        $comics = $this->service->get("comics/".$id);
        $creators = $this->service->get("comics/".$id."/creators");
        $characters = $this->service->get("comics/".$id."/characters");
        return array(
            "comic" => $comics->data->results[0],
            "creators" => $creators->data->results,
            "characters" => $characters->data->results
        );
    }

    /**
     * @Route("/details/{id}/favoris", requirements={"id" = "\d+"}, name="characters_detail_favoris")
     * @Method({"POST"})
     */
    public function favoris()
    {
        $request = $this->requestStack->getCurrentRequest();
        $name = "characters";

        // Création de la réponse
        $response = new Response();

        // Création d'une liste de favoris vide par défaut
        $favorisList = $this->favorisService->getFavoris($request, $name);

        // Vérification du nombre de favoris (Maximum 5)
        $this->favorisService->checkFavoritesSize($favorisList, 5);

        // Création d'un favoris
        $cookie = $this->favorisService->createFavoris($request, $favorisList, $name);

        // Envoi du cookie dans le header de la réponse 
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Route("/comic/{id}/favoris", requirements={"id" = "\d+"}, name="comic_detail_favoris")
     * @Method({"POST"})
     */
    public function comicsFavoris()
    {
        $request = $this->requestStack->getCurrentRequest();
        $name = "comics";

        // Création de la réponse
        $response = new Response();

        // Création d'une liste de favoris vide par défaut
        $favorisList = $this->favorisService->getFavoris($request, $name);

        // Vérification du nombre de favoris (Maximum 8)
        $this->favorisService->checkFavoritesSize($favorisList, 8);

        // Création d'un favoris
        $cookie = $this->favorisService->createFavoris($request, $favorisList, $name);

        // Envoi du cookie dans le header de la réponse
        $response->headers->setCookie($cookie);

        return $response;
    }
}