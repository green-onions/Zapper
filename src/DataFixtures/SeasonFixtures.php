<?php


namespace App\DataFixtures;


use App\Entity\Season;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');

        for ($i = 0; $i <= 5; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                $season = new Season();
                $season->setNumber($j);
                $season->setYear(2005 + $j);
                $season->setProgram($this->getReference('program_' . $i));
                $season->setDescription($faker->paragraph(4));
                $manager->persist($season);
                $this->addReference('program_' . $i . ', ' . 'season_' . $j, $season);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
