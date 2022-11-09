<?php

namespace App\Command;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use League\Csv\Statement;




#[AsCommand(
    name: 'app:import-villes-franche-comte',
    description: 'import les villes de franche comte',
    hidden: false,

)]
class ImportVille extends Command{

    private EntityManagerInterface $manager;


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;

        parent::__construct();

    }


    protected function execute(InputInterface $input, OutputInterface $output): int{

        $stream = fopen('documentation/villes.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

    //build a statement
        $stmt = Statement::create();

    //query your records from the document
        $records = $stmt->process($csv);

        foreach ($records as $record) {
            $numero=$record["Département"];
            if($numero==25 || $numero==39 || $numero==70 || $numero==90){
                $ville=new Ville();
                $ville->setNom($record["Commune"]);
                $ville->setCodePostal($record["Code postal"]);
                $ville->setDepartement($record["Nom département"]);
                $ville->setNumeroDepartement($record["Département"]);
                $ville->setRegion($record["Région"]);
                $this->manager->persist($ville);

            }

        }
        $this->manager->flush();
            //do something here
    return Command::SUCCESS;

    }
}