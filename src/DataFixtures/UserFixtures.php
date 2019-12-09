<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public const DEFAULT_PASSWORD = 'password';
    public const DEFAULT_NAMES = array(
        'Mia',
        'Amos',
        'Medge',
        'Aaron',
        'Plato',
        'Murphy',
        'Raya',
        'Gwendolyn',
        'Nero',
        'Jared',
        'Lance',
        'Angelica',
        'Amela',
        'Chantale',
        'Louis',
        'Gabriel',
        'Portia',
        'Calvin',
        'Alexa',
        'Pascale',
        'Gray'
    );

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->createUser($manager, ['ROLE_FOO']);
            $this->createUser($manager, ['ROLE_BAR']);
        }
        $this->createTestUser($manager);
    }

    function randomName()
    {
        return self::DEFAULT_NAMES[rand(0, count(self::DEFAULT_NAMES) - 1)];
    }

    /**
     * @param string[] $roles
     */
    private function createUser(ObjectManager $manager, array $roles): void
    {
        $userEntity = new User();
        $userEntity->setName(self::randomName());
        $userEntity->setEmail(sprintf('%s@gmail.com', $userEntity->getName()));
        $userEntity->setPlainPassword('password');
        $manager->persist($userEntity);
        $manager->flush();
    }

    /**
     * @param string[] $roles
     */
    private function createTestUser(ObjectManager $manager): void
    {
        $userEntity = new User();
        $userEntity->setName('ad ministrator');
        $userEntity->setEmail('admin@admin.com');
        $userEntity->setPlainPassword('password');
        $manager->persist($userEntity);
        $manager->flush();
    }
}
