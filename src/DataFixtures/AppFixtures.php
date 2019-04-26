<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Article;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 20; $i++) { 
            $content = $faker->realText(500);
            $title = $faker->realText(20);
            $dateCreation = $faker->dateTime();
            $nbLikes = $faker->randomNumber(2, true);

            $articles = (new Article())
                            ->setTitle($title)
                            ->setContent($content)
                            ->setCreatedAt($dateCreation)
                            ->setNbLike($nbLikes);

            $manager->persist($articles);
        }

        $manager->flush();
    }
}
