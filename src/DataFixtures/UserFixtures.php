<?php

namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        for ($i=0;$i<=20;$i++){

            $user =new User();
            $user->setPrenom($faker->firstName());
            $user->setNom($faker->lastName());
            $randNb= $faker->numberBetween(1,3);
            if ( $randNb ==1 ){
                $user->setPseudo($faker->word);
            }
            $user->setEmail($faker->email());
            $user->setPassword(password_hash('guytoupd',PASSWORD_BCRYPT));
            $user->setCreatedAt(new \DateTime());

            $bool = $faker->numberBetween(0,1);
            if ($bool == 0){
                $user->setActif(0);
            }else{
                $user->setActif(1);
            }
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);

        }
        $manager->flush();
    }
}
