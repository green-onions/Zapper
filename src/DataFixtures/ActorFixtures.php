<?php


namespace App\DataFixtures;


use Faker\Factory;
use App\Entity\Actor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActorFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln',
        'Norman Reedus',
        'Lauren Cohan',
        'Danai Gurira',
    ];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $key => $name) {
            $actor = new Actor();
            $actor->setName($name);
            $actor->addProgram($this->getReference('program_0'));
            $manager->persist($actor);
            //$this->addReference('actor_' . $key, $actor);
        }
        $manager->flush();

        $faker = Factory::create('en_US');

        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                $actor = new Actor();
                $actor->setName($faker->name);
                $actor->addProgram($this->getReference('program_' . $i));
                $manager->persist($actor);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
