<?php

namespace App\Controller;

use App\Repository\EtablissementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class EtablissementController extends AbstractController
{
    private EtablissementRepository $etablissementRepository ;
    private UserRepository $userRepository ;

    /**
     * @param EtablissementRepository $etablissementRepository
     * @param UserRepository $userRepository
     */
    public function __construct(EtablissementRepository $etablissementRepository, UserRepository $userRepository)
    {
        $this->etablissementRepository = $etablissementRepository;
        $this->userRepository = $userRepository;
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

    #[Route('/etablissements/favoris/{slug}', name:'app_etablissement_favory', methods:['GET','POST'])]
    public function etablissementFavory(EntityManagerInterface $entityManager,$slug):Response{
        $user = $this->userRepository->find($this->getUser());
        $etablissement= $this->etablissementRepository->findOneBy(["slug" => $slug]);
        if($user->getFavoris()->contains($etablissement)){
            $user->removeFavory($etablissement);
        }else{
            $user->addFavory($etablissement);
        }
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_etablissement_favoris');

    }

    #[Route('/etablissements/favoris', name: 'app_etablissements_favoris' ,priority: 1)]
    public function getEtablissementsFavoris(PaginatorInterface $paginator, Request $request): Response
    {
       $idUser = $this->getUser();
       $favoris = $this->userRepository->find($idUser)->getFavoris() ;

        $etablissements = $paginator->paginate(
            $favoris,
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );



        return $this->render('etablissement/etablissements_favoris.html.twig', [
            "etablissements" => $etablissements
        ]);
    }

}
