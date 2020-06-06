<?php


namespace App\DataFixtures;


use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Pixlforge\ChuckNorrisJokes\JokeFactory;

class EpisodeFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker   = Factory::create('fr_FR');
        $joke    = new JokeFactory();
        $slugify = new Slugify();

        for ($i = 0; $i <= 4; $i++) {
            for ($k = 1; $k <= 5; $k++) {
                for ($j = 1; $j <= rand(6, 12); $j++) {
                    $episode = new Episode();
                    $episode->setNumber($j);
                    $episode->setSeason($this->getReference('program_' . $i . ', ' . 'season_' . ($k)));
                    $episode->setTitle($faker->sentence(5, true));
                    $episode->setSynopsis($joke->getRandomJoke());
                    $episode->setSlug($slugify->generate($episode->getTitle()));
                    $manager->persist($episode);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
