<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
 
        $room1 = new Room();
        $room1->setName('Le placard sous l\'escalier');
        $room1->setCapacity(3);
        $room1->setAvailable(true);
        $manager->persist($room1);

        $room2 = new Room();
        $room2->setName('Griffondor');
        $room2->setCapacity(15);
        $room2->setAvailable(true);
        $manager->persist($room2);
        
        $room3 = new Room();
        $room3->setName('Serpentard');
        $room3->setCapacity(20);
        $room3->setAvailable(false); // Cette salle n'est pas disponible
        $manager->persist($room3);

        $room4 = new Room();
        $room4->setName('Poufsouffle');
        $room4->setCapacity(20);
        $room4->setAvailable(true); // Cette salle n'est pas disponible
        $manager->persist($room4);

        $room5 = new Room();
        $room5->setName('Serdaigle');
        $room5->setCapacity(20);
        $room5->setAvailable(true); // Cette salle n'est pas disponible
        $manager->persist($room5);

        // Enregistrement des données dans la base de données
        $manager->flush();
    }
}