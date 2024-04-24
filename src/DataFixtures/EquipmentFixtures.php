<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Liste des équipements
        $equipmentNames = ['Wi-Fi', 'Projecteur', 'Tableau', 'Prises électriques', 'Télévision', 'Climatisation'];

        // Création des équipements
        foreach ($equipmentNames as $name) {
            $equipment = new Equipment();
            $equipment->setName($name);
            $manager->persist($equipment);
        }

        // Enregistrement des données dans la base de données
        $manager->flush();
    }
}