<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Box;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

final class ProductsFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var EntityManagerInterface */
    private $em;

    private $prods;

    public function getDependencies()
    {
        return array(
            BoxFixtures::class,
        );
    }

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->prods =  array(
            'Melk',
            'Kip',
            'Taart',
            'Fruitsap',
            'Brood',
            'Kaas',
            'Basilicum',
            'Asperges',
            'Appel',
            'Diepvries pizza',
            'Eieren'
        );
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => 'admin@admin.com'));
        $boxes = $user->getBox();

        foreach ($boxes as $box) {
            $max = rand(4,10);
            for ($i = 0; $i < $max; $i++) {
                $this->createProduct($manager, $box, $i);
            }
        }
    }

    /**
     * @param Box $box
     * @param int $number
     */
    private function createProduct(ObjectManager $manager, $box, $number): void
    {
        $arrayShuffled = $this->prods;
        shuffle($arrayShuffled);
        $name = $arrayShuffled[$number];
        $date = new \DateTime(sprintf('+%s day', rand(3,5)));
        $productEntity = new Products();
        $productEntity->setName($name);
        $productEntity->setDescription(sprintf('Description of product %s', $name));
        $productEntity->setAmount(rand(1, 5));
        $productEntity->setBox($box);
        $productEntity->setExpires($date);
        $productEntity->setUnit('100 gram');
        $manager->persist($productEntity);
        $manager->flush();
    }
}
