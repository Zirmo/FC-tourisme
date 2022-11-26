<?php

namespace App\Controller;

use App\Repository\EtablissementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EtablissementController extends AbstractController
{
    private EtablissementRepository $etablissementRepository ;

    /**
     * @param EtablissementRepository $etablissementRepository
     */
    public function __construct(EtablissementRepository $etablissementRepository)
    {
        $this->etablissementRepository = $etablissementRepository;
    }

    #[Route('/etablissements', name: 'app_etablissements')]
    public function getEtablissements(PaginatorInterface $paginator, Request $request): Response
    {
        // recuperer les infos dans la base de donnees
        // le controleur fais appel au modele (classe du modele) afin de recuperer la liste des articles
        // $repository = new ArticleRepository();

        //mise en place de la pagination

        $etablissements = $paginator->paginate(
            $this->etablissementRepository->findBy(["actif" => true], ['nom' => 'ASC'],),
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );


        return $this->render('etablissement/etablissements.html.twig', [
            "etablissements" => $etablissements
        ]);
    }

    #[Route('/etablissements/{slug}', name: 'app_etablissement_slug', methods: ['GET', 'POST'])]
    public function getEtablissement( Request $request, $slug): Response
    {


        return $this->renderForm('etablissement/etablissement_info.html.twig', [
            "etablissement" => $this->etablissementRepository->findOneBy(["slug" => $slug]),

        ]);
    }
}
