<?php
namespace ContactTest\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAccessData extends AbstractFixture
{
    /**
     * Load the Access roles (minimally needed for the application)
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $access = new \Contact\Entity\Access();
        $access->setAccess('office');
        $manager->persist($access);
        $manager->flush();

        $access = new \Contact\Entity\Access();
        $access->setAccess('user');
        $manager->persist($access);
        $manager->flush();

        $access = new \Contact\Entity\Access();
        $access->setAccess('public');
        $manager->persist($access);
        $manager->flush();
    }
}
