<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= 3; $i++) {
            $subscriber = new User();
            $subscriber->setPseudo($faker->firstName);
            $subscriber->setEmail($faker->email);
            $subscriber->setPassword($this->passwordEncoder->encodePassword($subscriber, 'zapper'));
            $subscriber->setRoles(['ROLE_SUBSCRIBER']);
            $manager->persist($subscriber);
        }

        $admin = new User();
        $admin->setPseudo('ZapperMaster');
        $admin->setEmail('zapper.series@gmail.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'zapper'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
