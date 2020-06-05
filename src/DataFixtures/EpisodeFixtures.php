<?php


namespace App\DataFixtures;


use App\Entity\Episode;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= 4; $i++) {
            for ($j = 1; $j <= 8; $j++) {
                $episode = new Episode();
                $episode->setNumber($j);
                $episode->setSeason($this->getReference('program_' . $i . ', ' . 'season_' . ($i + 1)));
                $episode->setTitle($faker->title);
                $episode->setSynopsis($faker->paragraph(3));
                $manager->persist($episode);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
