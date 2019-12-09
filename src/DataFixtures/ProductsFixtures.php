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

    public function getDependencies()
    {
        return array(
            BoxFixtures::class,
        );
    }

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => 'admin@admin.com'));
        $boxes = $user->getBox();

        foreach ($boxes as $box) {
            for ($i = 0; $i < 10; $i++) {
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
        $date = new \DateTime(sprintf('+%s day', rand(3,5)));
        $productEntity = new Products();
        $productEntity->setName(sprintf('Product #%s', $number));
        $productEntity->setDescription(sprintf('Description of product #%s', $number));
        $productEntity->setAmount(rand(1, 5));
        $productEntity->setBox($box);
        $productEntity->setExpires($date);
        $productEntity->setUnit('100 gram');
        $manager->persist($productEntity);
        $manager->flush();
    }
}
