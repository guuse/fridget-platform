<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Box;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

final class BoxFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['id' => 'DESC']);

        foreach ($users as $user) {
            if ($user->getEmail() == 'admin@admin.com') {
                $this->createTestBox($manager, $user);
            } else {
                $this->createBox($manager, $user);
            }
        }
    }

    /**
     * @param User $user
     */
    private function createBox(ObjectManager $manager, User $user): void
    {
        $boxEntity = new Box();
        $boxEntity->setName(sprintf('%s_\'s box', $user->getName()));
        $boxEntity->setDescription(sprintf('This is the box of %s', $user->getName()));
        $boxEntity->setUser($user);
        $manager->persist($boxEntity);
        $manager->flush();
    }

    /**
     * @param User $user
     */
    private function createTestBox(ObjectManager $manager, User $user): void
    {
        for ($i = 0; $i < 3; $i++) {
            $boxEntity = new Box();
            $boxEntity->setName(sprintf('%s_\'s box number %s', $user->getName(), $i + 1));
            $boxEntity->setDescription(sprintf('This is the box of %s. This is box numer %s.', $user->getName(), $i + 1));
            $boxEntity->setUser($user);
            $manager->persist($boxEntity);
            $manager->flush();
        }
    }
}
