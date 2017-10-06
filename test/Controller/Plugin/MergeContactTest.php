<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace ContactTest\Controller\Plugin;

use Admin\Entity\Access;
use Affiliation\Entity\Affiliation;
use Affiliation\Entity\Financial;
use Contact\Controller\ContactAdminController;
use Contact\Controller\Plugin\MergeContact;
use Contact\Entity\Address;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Cv;
use Contact\Entity\Dnd;
use Contact\Entity\OpenId;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\Photo;
use Contact\Entity\Web;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Contact\Entity\Log;
use Contact\Entity\Note;
use Contact\Entity\Email;
use General\Entity\Gender;
use General\Entity\Title;
use Invoice\Entity\Invoice;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Organisation;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Program\Entity\Doa;
use Program\Entity\Domain;
use Program\Entity\Funder;
use Program\Entity\Nda;
use Program\Entity\RoadmapLog;
use Program\Entity\Technology;
use Project\Entity\Contract;
use Project\Entity\Description\Description;
use Project\Entity\Document\Document;
use Project\Entity\Idea\Idea;
use Project\Entity\Idea\Partner;
use Project\Entity\Project;
use Project\Entity\Rationale;
use Project\Entity\Report\EffortSpent;
use Project\Entity\Report\Item;
use Project\Entity\Report\WorkpackageDescription;
use Project\Entity\Version\Version;
use Publication\Entity\Download;
use Publication\Entity\Publication;
use Testing\Util\AbstractServiceTest;
use Zend\I18n\Translator\Translator;
use Zend\Log\Logger;
use Zend\Stdlib\DispatchableInterface;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class MergeContactTest
 * @package ContactTest\Controller\Plugin
 */
final class MergeContactTest extends AbstractServiceTest
{
    /** @var Contact */
    private $source;

    /** @var Contact */
    private $target;

    /** @var Translator */
    private $translator;

    /**
     * Set up basic properties
     */
    public function setUp()
    {
        $this->source = $this->createSource();
        $this->target = $this->createTarget();
        $this->translator = $this->setUpTranslatorMock();
    }

    /**
     * Test the basic __invoke magic method returning the plugin instance
     *
     * @covers \Contact\Controller\Plugin\MergeContact::__invoke
     * @covers \Contact\Controller\Plugin\MergeContact::__construct
     */
    public function testInvoke()
    {
        $mergeContact = new MergeContact($this->getEntityManagerMock(), $this->translator);
        $instance = $mergeContact();
        $this->assertSame($mergeContact, $instance);
    }

    /**
     * Test the pre-merge checks
     *
     * @covers \Contact\Controller\Plugin\MergeContact::checkMerge
     */
    public function testCheckMerge()
    {
        $mergeContact = new MergeContact($this->getEntityManagerMock(), $this->translator);

        // Run the merge check
        $errors = $mergeContact()->checkMerge($this->source, $this->target);

        $this->assertEquals([], $errors);
    }

    /**
     * Test the actual merge
     *
     * @covers \Contact\Controller\Plugin\MergeContact::merge
     */
    public function testMerge()
    {
        /** @var DispatchableInterface $controllerMock */
        $controllerMock = $this->setUpControllerMock();
        $mergeOrganisation = new MergeContact($this->setUpEntityManagerMock(), $this->translator);
        $mergeOrganisation->setController($controllerMock);

        $result = $mergeOrganisation()->merge($this->source, $this->target);

    }

    /**
     * Test a failing merge
     *
     * @covers \Contact\Controller\Plugin\MergeContact::merge
     */
    public function testMergeFail()
    {
        $entityManagerMock = $this->setUpEntityManagerMock(true);
        $mergeContact = new MergeContact($entityManagerMock, $this->translator);
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods(['err'])
            ->getMock();

        $loggerMock->expects($this->once())
            ->method('err')
            ->with($this->stringContains('Oops!'))
            ->will($this->returnSelf());

        $responseNoLog = $mergeContact()->merge($this->source, $this->target);
        $responseLog = $mergeContact()->merge($this->source, $this->target, $loggerMock);

        $this->assertEquals(false, $responseNoLog['success']);
        $this->assertEquals('Oops!', $responseNoLog['errorMessage']);
        $this->assertEquals(false, $responseLog['success']);
    }

    /**
     * @return Contact
     */
    private function createSource(): Contact
    {
        $gender = new Gender();
        $gender->setId(1);

        $title = new Title();
        $title->setId(1);

        $source = new Contact();
        $source->setId(1);
        $source->setFirstName('Test');
        $source->setMiddleName('von');
        $source->setLastName('Dummy');
        $source->setEmail('test.von.dummy@itea3.org');
        $source->setGender($gender);
        $source->setTitle($title);
        $source->setPosition('Tester');
        $source->setDepartment('Test department');
        $source->setDateOfBirth(new \DateTime('1970-01-01'));
        $source->setDateCreated(new \DateTime('2015-01-01'));
        $source->setLastUpdate(new \DateTime());
        $source->setMessenger(1);

        $cv = new Cv();
        $cv->setId(1);
        $source->setCv($cv);

        $contactOrganisation = new ContactOrganisation();
        $contactOrganisation->setId(1);
        $contactOrganisation->setContact($source);
        $source->setContactOrganisation($contactOrganisation);

        $access = new Access();
        $access->setId(1);
        $source->setAccess(new ArrayCollection([$access]));

        $emailAddress = new Email();
        $emailAddress->setId(1);
        $emailAddress->setEmail('test.von.dummy@itea3.org');
        $emailAddress->setContact($source);
        $source->setEmailAddress(new ArrayCollection([$emailAddress]));

        $address = new Address();
        $address->setId(1);
        $address->setContact($source);
        $source->setAddress(new ArrayCollection([$address]));

        $phone = new Phone();
        $phone->setId(1);
        $phone->setContact($source);
        $source->setPhone(new ArrayCollection([$phone]));

        $web = new Web();
        $web->setId(1);
        $web->setContact($source);
        $source->setWeb(new ArrayCollection([$web]));

        $optIn = new OptIn();
        $optIn->setId(1);
        $optIn->setContact(new ArrayCollection([$source]));
        $source->setOptIn(new ArrayCollection([$optIn]));

        $project = new Project();
        $project->setId(1);
        $project->setContact($source);
        $source->setProject(new ArrayCollection([$project]));

        $rationale = new Rationale();
        $rationale->setId(1);
        $rationale->setContact($source);
        $source->setRationale(new ArrayCollection([$rationale]));

        $projectDescription = new Description();
        $projectDescription->setId(1);
        $projectDescription->setContact(new ArrayCollection([$source]));
        $source->setProjectDescription(new ArrayCollection([$projectDescription]));

        $version = new Version();
        $version->setId(1);
        $version->setContact($source);
        $source->setProjectVersion(new ArrayCollection([$version]));

        $reportItem = new Item();
        $reportItem->setId(1);
        $reportItem->setContact($source);
        $source->setProjectReportItem(new ArrayCollection([$reportItem]));

        $workpackageDescription = new WorkpackageDescription();
        $workpackageDescription->setId(1);
        $workpackageDescription->setContact($source);
        $source->setProjectReportWorkpackageDescription(new ArrayCollection([$workpackageDescription]));

        $effortSpent = new EffortSpent();
        $effortSpent->setId(1);
        $effortSpent->setContact($source);
        $source->setProjectReportEffortSpent(new ArrayCollection([$effortSpent]));

        $projectDocument = new Document();
        $projectDocument->setId(1);
        $projectDocument->setContact($source);
        $source->setProjectDocument(new ArrayCollection([$projectDocument]));

        $dnd = new Dnd();
        $dnd->setId(1);
        $dnd->setContact($source);
        $source->setDnd(new ArrayCollection([$dnd]));

        $contract = new Contract();
        $contract->setId(1);
        $contract->setContact($source);
        $source->setContract(new ArrayCollection([$contract]));

        $contractVersion = new Contract\Version();
        $contractVersion->setId(1);
        $contractVersion->setContact($source);
        $source->setContractVersion(new ArrayCollection([$contractVersion]));

        $nda = new Nda();
        $nda->setId(1);
        $nda->setContact($source);
        $source->setNda(new ArrayCollection([$nda]));

        $ndaApproved = new Nda();
        $ndaApproved->setId(2);
        $ndaApproved->setApprover($source);
        $source->setNdaApprover(new ArrayCollection([$ndaApproved]));

        $roadmapLog = new RoadmapLog();
        $roadmapLog->setId(1);
        $roadmapLog->setContact($source);
        $source->setRoadmapLog(new ArrayCollection([$roadmapLog]));

        $programDoa = new Doa();
        $programDoa->setId(1);
        $programDoa->setContact($source);
        $source->setProgramDoa(new ArrayCollection([$programDoa]));

        $parentDoa = new \Organisation\Entity\Parent\Doa();
        $parentDoa->setId(1);
        $parentDoa->setContact($source);
        $source->setParentDoa(new ArrayCollection([$parentDoa]));

        $openId = new OpenId();
        $openId->setId(1);
        $openId->setIdentity('xxxxxxxxxx');
        $openId->setContact($source);
        $source->setOpenId(new ArrayCollection([$openId]));

        $domain = new Domain();
        $domain->setId(1);
        $domain->setContact(new ArrayCollection([$source]));
        $source->setDomain(new ArrayCollection([$domain]));

        $idea = new Idea();
        $idea->setId(1);
        $idea->setContact($source);
        $source->setIdea(new ArrayCollection([$idea]));

        $favouriteIdea = new Idea();
        $favouriteIdea->setId(2);
        $favouriteIdea->setFavourite(new ArrayCollection([$source]));
        $source->setFavouriteIdea(new ArrayCollection([$favouriteIdea]));

        $technology = new Technology();
        $technology->setId(1);
        $technology->setContact(new ArrayCollection([$source]));
        $source->setTechnology(new ArrayCollection([$technology]));

        $organisationLog = new \Organisation\Entity\Log();
        $organisationLog->setId(1);
        $organisationLog->setContact($source);
        $source->setOrganisationLog(new ArrayCollection([$organisationLog]));

        $ideaPartner = new Partner();
        $ideaPartner->setId(1);
        $ideaPartner->setContact($source);
        $source->setIdeaPartner(new ArrayCollection([$ideaPartner]));

        $affiliation = new Affiliation();
        $affiliation->setId(1);
        $affiliation->setContact($source);
        $source->setAffiliation(new ArrayCollection([$affiliation]));

        $affiliationLog = new \Affiliation\Entity\Log();
        $affiliationLog->setId(1);
        $affiliationLog->setContact($source);
        $source->setAffiliationLog(new ArrayCollection([$affiliationLog]));

        $financial = new Financial();
        $financial->setId(1);
        $financial->setContact($source);
        $source->setFinancial(new ArrayCollection([$financial]));

        $affiliationDescription = new \Affiliation\Entity\Description();
        $affiliationDescription->setId(1);
        $affiliationDescription->setContact($source);
        $source->setAffiliationDescription(new ArrayCollection([$affiliationDescription]));

        $affiliationVersion = new \Affiliation\Entity\Version();
        $affiliationVersion->setId(1);
        $affiliationVersion->setContact($source);
        $source->setAffiliationVersion(new ArrayCollection([$affiliationVersion]));

        $invoice = new Invoice();
        $invoice->setId(1);
        $invoice->setContact($source);
        $source->setInvoice(new ArrayCollection([$invoice]));

        $parent = new OParent();
        $parent->setId(1);
        $parent->setContact($source);
        $source->setParent(new ArrayCollection([$parent]));

        $parentFinancial = new \Organisation\Entity\Parent\Financial();
        $parentFinancial->setId(1);
        $parentFinancial->setContact($source);
        $source->setParentFinancial(new ArrayCollection([$parentFinancial]));

        $parentOrganisation = new Organisation();
        $parentOrganisation->setId(1);
        $parentOrganisation->setContact($source);
        $source->setParentOrganisation(new ArrayCollection([$parentOrganisation]));

        $publication = new Publication();
        $publication->setId(1);
        $publication->setContact($source);
        $source->setPublication(new ArrayCollection([$publication]));

        $publicationDownload = new Download();
        $publicationDownload->setId(1);
        $publicationDownload->setContact($source);
        $source->setPublicationDownload(new ArrayCollection([$publicationDownload]));

        $photo = new Photo();
        $photo->setId(1);
        $photo->setContact($source);
        $source->setPhoto(new ArrayCollection([$photo]));

        $associate = new Affiliation();
        $associate->setId(2);
        $associate->setAssociate(new ArrayCollection([$source]));
        $source->setAssociate(new ArrayCollection([$associate]));

        $funder = new Funder();
        $funder->setId(1);
        $source->setFunder($funder);

        $deeplinkContact = new \Deeplink\Entity\Contact();
        $deeplinkContact->setId(1);
        $deeplinkContact->setContact($source);
        $source->setDeeplinkContact(new ArrayCollection([$deeplinkContact]));

        return $source;
    }

    /**
     * @return Contact
     */
    private function createTarget(): Contact
    {
        $target = new Contact();
        $target->setId(2);
        $target->setDateCreated(new \DateTime('2017-01-01'));
        $target->setLastUpdate(new \DateTime('2017-01-01'));

        $emailAddress = new Email();
        $emailAddress->setId(2);
        $emailAddress->setEmail('duplicate@itea3.org');
        $emailAddress->setContact($target);
        $target->setEmailAddress(new ArrayCollection([$emailAddress]));

        $optIn = new OptIn();
        $optIn->setId(2);
        $optIn->setContact(new ArrayCollection([$target]));
        $target->setOptIn(new ArrayCollection([$optIn]));

        $openId = new OpenId();
        $openId->setId(2);
        $openId->setIdentity('yyyyyyyyyy');
        $openId->setContact($target);
        $target->setOpenId(new ArrayCollection([$openId]));

        $domain = new Domain();
        $domain->setId(2);
        $domain->setContact(new ArrayCollection([$target]));
        $target->setDomain(new ArrayCollection([$domain]));

        $favouriteIdea = new Idea();
        $favouriteIdea->setId(3);
        $favouriteIdea->setFavourite(new ArrayCollection([$target]));
        $target->setFavouriteIdea(new ArrayCollection([$favouriteIdea]));

        $technology = new Technology();
        $technology->setId(2);
        $technology->setContact(new ArrayCollection([$target]));
        $target->setTechnology(new ArrayCollection([$technology]));

        $associate = new Affiliation();
        $associate->setId(3);
        $associate->setAssociate(new ArrayCollection([$target]));
        $target->setAssociate(new ArrayCollection([$associate]));

        return $target;
    }

    /**
     * Set up the translator mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpTranslatorMock(): MockObject
    {
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->setMethods(['translate'])
            ->getMock();

        // Just let the translator return the untranslated string
        $translatorMock->expects($this->any())
            ->method('translate')
            ->will($this->returnArgument(0));

        return $translatorMock;
    }

    /**
     * Set up the controller mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpControllerMock(): MockObject
    {
        $contact = new Contact();
        $contact->setId(3);

        $zfcUserAuthenticationMock = $this->getMockBuilder(ZfcUserAuthentication::class)
            ->setMethods(['getIdentity'])
            ->getMock();
        $zfcUserAuthenticationMock->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($contact));

        $controllerMock = $this->getMockBuilder(ContactAdminController::class)
            ->setMethods(['zfcUserAuthentication'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('zfcUserAuthentication')
            ->will($this->returnValue($zfcUserAuthenticationMock));

        return $controllerMock;
    }

    /**
     * Set up the entity manager mock object with expectations depending on the chosen merge strategy.
     * @param bool $throwException
     *
     * @return EntityManager|MockObject
     */
    private function setUpEntityManagerMock(bool $throwException = false): MockObject
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'remove', 'flush'])
            ->getMock();

        // Short circuit when an exception should be thrown
        if ($throwException) {
            $exception = new ORMException('Oops!');
            $entityManagerMock->expects($this->any())->method('persist')->will($this->throwException($exception));
            $entityManagerMock->expects($this->any())->method('remove')->will($this->throwException($exception));
            $entityManagerMock->expects($this->any())->method('flush')->will($this->throwException($exception));

            return $entityManagerMock;
        }

        // Setup the parameters depending on merge strategy
        $params = [
            [$this->isInstanceOf(Log::class)],
            [$this->isInstanceOf(Note::class)],
        ];

        $entityManagerMock->expects($this->exactly(count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects($this->once())->method('remove')->with($this->source);
        $entityManagerMock->expects($this->exactly(2))->method('flush');

        return $entityManagerMock;
    }
}