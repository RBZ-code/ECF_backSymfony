<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Book;
use App\Entity\Condition;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $conditions = $manager->getRepository(Condition::class)->findAll();
        
        // Generate dummy books
        for ($i = 1; $i <= 100; $i++) {
            $book = new Book();
            $book->setTitle($faker->sentence(3));
            $book->setAuthor($faker->name);
            $book->setYearPublished(mt_rand(1980, 2020));

            // Generate unique image URL using Lorem Picsum with a different seed (based on $i)
            $imageSeed = $i; // Use $i as a seed for generating unique image
            $imageUrl = 'https://picsum.photos/200/300?random=' . $imageSeed;       
            $book->setImage($imageUrl);

            $book->setAvailable(true);
            $book->setRating(mt_rand(1, 5));
            $book->setDescription($faker->paragraph(5));

            $randomCondition = $conditions[array_rand($conditions)];
            $book->setBookCondition($randomCondition);

            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ConditionFixtures::class, // Load ConditionFixtures before BookFixtures
        ];
    }
}
