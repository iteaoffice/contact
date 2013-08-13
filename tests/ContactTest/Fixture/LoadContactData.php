<?php
namespace ContactTest\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContactData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load the Contact
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $contact = new \Contact\Entity\Contact();
        $contact->setFirstName('Jan');
        $contact->setMiddleName('van der');
        $contact->setLastName('Dam');
        $contact->setEmail('test@example.com');
        $contact->setState(1);
        $contact->setPassword('password');
        $contact->setMessenger('messenger');
        $contact->setDateOfBirth(new \DateTime());

        $contact->setGender($manager->find('General\Entity\Gender', 1));
        $contact->setTitle($manager->find('General\Entity\Title', 1));

        $manager->persist($contact);
        $manager->flush();
    }

    /**
     * fixture classes fixture is dependent on
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'ContactTest\Fixture\LoadGenderData',
            'ContactTest\Fixture\LoadTitleData'
        ); //
    }
}
