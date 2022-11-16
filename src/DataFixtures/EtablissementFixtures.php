<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Repository\CategorieRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class EtablissementFixtures extends Fixture
{
    private SluggerInterface $slugger;
    private VilleRepository $villeRepository;
    private CategorieRepository $categorieRepository;

    //demander a symfony d'injecter le slugger au niveau du constructeur


    public function __construct(SluggerInterface $slugger, VilleRepository $villeRepository, CategorieRepository $categorieRepository)
    {
        $this->villeRepository = $villeRepository;
        $this->slugger = $slugger;
        $this->categorieRepository = $categorieRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $num = $this->villeRepository->findAll();
        $min = min($num);
        $max = max($num);
        $numCat = $this->categorieRepository->findAll();
        $minCat = min($numCat);
        $maxCat = max($numCat);

        for ($i = 0; $i < 300; $i++) {
            $numCate = $faker->numberBetween($minCat->getId(), $maxCat->getId());
            $numVille = $faker->numberBetween($min->getId(), $max->getId());
            $etablissement = new Etablissement();
            $etablissement->setNom($faker->realTextBetween(5, 20))
                ->setSlug($this->slugger->slug($etablissement->getNom())->lower())
                ->setDescription($faker->paragraph)
                ->setTelephone($faker->phoneNumber)
                ->setAdresse($faker->streetAddress());

            $etablissement->setVille($this->villeRepository->find($numVille))
                ->setEmail($faker->email())

                //a modifier plus tard
                ->setImage($faker->imageUrl(360, 360, "hotel", true))

                ->setActif($faker->boolean())
                ->setAccueil($faker->boolean());

            //a modifier plus tard
            $etablissement->addCategorie($this->categorieRepository->find($numCate));

            $etablissement->setCreatedAt($faker->dateTimeBetween("-1 years"));

            $manager->persist($etablissement);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
