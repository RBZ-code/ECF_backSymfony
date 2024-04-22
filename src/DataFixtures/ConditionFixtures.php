<?php

namespace App\DataFixtures;

use App\Entity\Condition;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ConditionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $conditionsData = [
            ['name' => 'excellent état'],
            ['name' => 'bon état'],
            ['name' => 'état moyen'],
            ['name' => 'mauvais état'],
        ];

        foreach ($conditionsData as $conditionData) {
            $condition = new Condition();
            $condition->setName($conditionData['name']);
            $manager->persist($condition);
        }

        $manager->flush();
    }
}