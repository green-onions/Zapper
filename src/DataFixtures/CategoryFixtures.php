<?php


namespace App\DataFixtures;


use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    const CATEGORIES = [
        'Comédie',
        'Horreur',
        'Science-fiction',
        'Policier',
        'Fantastique',
        'Espionnage'
    ];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $this->addReference('categorie_' . $key, $category);
        }
        $manager->flush();
    }
}
