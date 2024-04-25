<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UtilisateurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i++) {
            $user = new Utilisateur();
            $user->setEmail('user' . $i . '@example.com');
            $user->setRoles(['ROLE_USER']); 
            $user->setPassword(password_hash('password' . $i, PASSWORD_DEFAULT)); // 
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setDateOfBirth($faker->dateTimeBetween('-60 years', '-18 years')); // Generate date of birth between 18 and 60 years ago
            $user->setCity($faker->city);
            $user->setPostalCode($faker->postcode);
            $phoneNumber = str_replace(['(', ')', ' ', '-'], '', $faker->phoneNumber);
            $user->setPhoneNumber((int)$phoneNumber);
            $user->setCountry($faker->country);
            $user->setStreet($faker->streetAddress);

            $manager->persist($user);
        }

    
        $masterUser = new Utilisateur();
        $masterUser->setEmail('a@a.com'); // Setting the email to 'a@a.com'
        $masterUser->setRoles(['ROLE_USER', 'ROLE_ADMIN']); // Adding the admin role
        $masterUser->setPassword(password_hash('a@a.com', PASSWORD_DEFAULT)); // You can generate hashed passwords using password_hash() function
        $masterUser->setFirstName('Master');
        $masterUser->setLastName('User');
        $masterUser->setDateOfBirth($faker->dateTimeBetween('-60 years', '-18 years')); // Generate date of birth between 18 and 60 years ago
        $masterUser->setCity($faker->city);
        $masterUser->setPostalCode($faker->postcode);
        $phoneNumber = str_replace(['(', ')', ' ', '-'], '', $faker->phoneNumber);
        $masterUser->setPhoneNumber((int)$phoneNumber);
        $masterUser->setCountry($faker->country);
        $masterUser->setStreet($faker->streetAddress);

        $manager->persist($masterUser);

        $manager->flush();
    }
}
