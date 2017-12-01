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
use Admin\Entity\Session;
use Affiliation\Entity\Affiliation;
use Affiliation\Entity\DoaReminder;
use Affiliation\Entity\Financial;
use Affiliation\Entity\Loi;
use Affiliation\Entity\LoiReminder;
use Calendar\Entity\Calendar;
use Calendar\Entity\ScheduleContact;
use Contact\Controller\ContactAdminController;
use Contact\Controller\Plugin\MergeContact;
use Contact\Entity\Address;
use Contact\Entity\Community;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Cv;
use Contact\Entity\Dnd;
use Contact\Entity\OpenId;
use Contact\Entity\OptIn;
use Contact\Entity\Phone;
use Contact\Entity\Photo;
use Contact\Entity\Profile;
use Contact\Entity\Selection;
use Contact\Entity\SelectionContact;
use Contact\Entity\Web;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Contact\Entity\Log;
use Contact\Entity\Note;
use Contact\Entity\Email;
use Event\Entity\Badge\Badge;
use Event\Entity\Exhibition\Tour;
use Event\Entity\Exhibition\Voter;
use Event\Entity\Registration;
use General\Entity\EmailMessage;
use General\Entity\Gender;
use General\Entity\Title;
use Invoice\Entity\Invoice;
use Invoice\Entity\Journal;
use Invoice\Entity\Journal\Entry;
use Invoice\Entity\Reminder;
use Mailing\Entity\Mailing;
use News\Entity\Blog;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Organisation;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Program\Entity\Doa;
use Program\Entity\Domain;
use Program\Entity\Funder;
use Program\Entity\Nda;
use Program\Entity\RoadmapLog;
use Program\Entity\Technology;
use Project\Entity\Achievement;
use Project\Entity\Booth;
use Project\Entity\ChangeRequest\CostChange;
use Project\Entity\ChangeRequest\Country;
use Project\Entity\ChangeRequest\Process;
use Project\Entity\Contract;
use Project\Entity\Description\Description;
use Project\Entity\Document\Document;
use Project\Entity\Evaluation\Evaluation;
use Project\Entity\Idea\Idea;
use Project\Entity\Idea\Message;
use Project\Entity\Idea\MessageBoard;
use Project\Entity\Idea\Partner;
use Project\Entity\Invite;
use Project\Entity\Project;
use Project\Entity\Rationale;
use Project\Entity\Report\EffortSpent;
use Project\Entity\Report\Item;
use Project\Entity\Report\Report;
use Project\Entity\Report\WorkpackageDescription;
use Project\Entity\Result\Result;
use Project\Entity\Review\Review;
use Project\Entity\Version\Version;
use Project\Entity\Workpackage\Workpackage;
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

        $this->assertTrue($result['success']);
        $this->assertSame($this->source->getFirstName(), $this->target->getFirstName());
        $this->assertSame($this->source->getMiddleName(), $this->target->getMiddleName());
        $this->assertSame($this->source->getLastName(), $this->target->getLastName());
        $this->assertSame($this->source->getEmail(), $this->target->getEmail());
        $this->assertSame($this->source->getGender(), $this->target->getGender());
        $this->assertSame($this->source->getTitle(), $this->target->getTitle());
        $this->assertSame($this->source->getPosition(), $this->target->getPosition());
        $this->assertSame($this->source->getDepartment(), $this->target->getDepartment());
        $this->assertSame($this->source->getDateOfBirth(), $this->target->getDateOfBirth());
        $this->assertSame($this->source->getDateCreated(), $this->target->getDateCreated());
        $this->assertSame($this->source->getLastUpdate(), $this->target->getLastUpdate());
        $this->assertSame($this->source->getMessenger(), $this->target->getMessenger());

        $this->assertSame(1, $this->target->getAccess()->count());
        $this->assertSame(1, $this->target->getAccess()->first()->getId());
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

        $profile = new Profile();
        $profile->setId(1);
        $profile->setContact($source);
        $source->setProfile($profile);

        $community = new Community();
        $community->setId(1);
        $community->setContact($source);
        $source->setCommunity(new ArrayCollection([$community]));

        $registration = new Registration();
        $registration->setId(1);
        $registration->setContact($source);
        $source->setRegistration(new ArrayCollection([$registration]));

        $badge = new Badge();
        $badge->setId(1);
        $badge->setContact($source);
        $source->setBadge(new ArrayCollection([$badge]));

        $badgeContact = new \Event\Entity\Badge\Contact();
        $badgeContact->setId(1);
        $badgeContact->setContact($source);
        $source->setBadgeContact(new ArrayCollection([$badgeContact]));

        $boothContact = new \Event\Entity\Booth\Contact();
        $boothContact->setId(1);
        $boothContact->setContact($source);
        $source->setBoothContact(new ArrayCollection([$boothContact]));

        $projectBooth = new Booth();
        $projectBooth->setId(1);
        $projectBooth->setContact($source);
        $source->setProjectBooth(new ArrayCollection([$projectBooth]));

        $organisationBooth = new \Organisation\Entity\Booth();
        $organisationBooth->setId(1);
        $organisationBooth->setContact($source);
        $source->setOrganisationBooth(new ArrayCollection([$organisationBooth]));

        $boothFinancial = new \Event\Entity\Booth\Financial();
        $boothFinancial->setId(1);
        $boothFinancial->setContact($source);
        $source->setBoothFinancial(new ArrayCollection([$boothFinancial]));

        $note = new Note();
        $note->setId(1);
        $note->setContact($source);
        $source->setNote(new ArrayCollection([$note]));

        $selection = new Selection();
        $selection->setId(1);
        $selection->setContact($source);
        $source->setSelection(new ArrayCollection([$selection]));

        $selectionContact = new SelectionContact();
        $selectionContact->setId(1);
        $selectionContact->setContact($source);
        $source->setSelectionContact(new ArrayCollection([$selectionContact]));

        $mailingContact = new \Mailing\Entity\Contact();
        $mailingContact->setId(1);
        $mailingContact->setContact($source);
        $source->setMailingContact(new ArrayCollection([$mailingContact]));

        $mailing = new Mailing();
        $mailing->setId(1);
        $mailing->setContact($source);
        $source->setMailing(new ArrayCollection([$mailing]));

        $emailMessage = new EmailMessage();
        $emailMessage->setId(1);
        $emailMessage->setContact($source);
        $source->setEmailMessage(new ArrayCollection([$emailMessage]));

        $result = new Result();
        $result->setId(1);
        $result->setContact($source);
        $source->setResult(new ArrayCollection([$result]));

        $workpackage = new Workpackage();
        $workpackage->setId(1);
        $workpackage->setContact($source);
        $source->setWorkpackage(new ArrayCollection([$workpackage]));

        $workpackageDocument = new \Project\Entity\Workpackage\Document();
        $workpackageDocument->setId(1);
        $workpackageDocument->setContact($source);
        $source->setWorkpackageDocument(new ArrayCollection([$workpackageDocument]));

        $ideaMessage = new Message();
        $ideaMessage->setId(1);
        $ideaMessage->setContact($source);
        $source->setIdeaMessage(new ArrayCollection([$ideaMessage]));

        $evaluation = new Evaluation();
        $evaluation->setId(1);
        $evaluation->setContact($source);
        $source->setEvaluation(new ArrayCollection([$evaluation]));

        $calendar = new Calendar();
        $calendar->setId(1);
        $calendar->setContact($source);
        $source->setCalendar(new ArrayCollection([$calendar]));

        $calendarContact = new \Calendar\Entity\Contact();
        $calendarContact->setId(1);
        $calendarContact->setContact($source);
        $source->setCalendarContact(new ArrayCollection([$calendarContact]));

        $calendarDocument = new \Calendar\Entity\Document();
        $calendarDocument->setId(1);
        $calendarDocument->setContact($source);
        $source->setCalendarDocument(new ArrayCollection([$calendarDocument]));

        $scheduleContact = new ScheduleContact();
        $scheduleContact->setId(1);
        $scheduleContact->setContact($source);
        $source->setScheduleContact(new ArrayCollection([$scheduleContact]));

        $projectReview = new Review();
        $projectReview->setId(1);
        $projectReview->setContact($source);
        $source->setProjectReview(new ArrayCollection([$projectReview]));

        $reviewContact = new \Project\Entity\Review\Contact();
        $reviewContact->setId(1);
        $reviewContact->setContact($source);
        $source->setProjectReviewContact($reviewContact);

        $projectVersionReview = new \Project\Entity\Version\Review();
        $projectVersionReview->setId(1);
        $projectVersionReview->setContact($source);
        $source->setProjectVersionReview(new ArrayCollection([$projectVersionReview]));

        $projectReport = new Report();
        $projectReport->setId(1);
        $projectReport->setContact($source);
        $source->setProjectReport(new ArrayCollection([$projectReport]));

        $projectCalendarReview = new \Project\Entity\Calendar\Review();
        $projectCalendarReview->setId(1);
        $projectCalendarReview->setContact($source);
        $source->setProjectCalendarReview(new ArrayCollection([$projectCalendarReview]));

        $projectReportReview = new \Project\Entity\Report\Review();
        $projectReportReview->setId(1);
        $projectReportReview->setContact($source);
        $source->setProjectReportReview(new ArrayCollection([$projectReportReview]));

        $invite = new Invite();
        $invite->setId(1);
        $invite->setContact($source);
        $source->setInvite(new ArrayCollection([$invite]));

        /*$pca = new Pca();
        $pca->setId(1);
        $pca->setContact($source);
        $source->setPca(new ArrayCollection([$pca]));*/

        $inviteContact = new Invite();
        $inviteContact->setId(2);
        $inviteContact->setInviteContact(new ArrayCollection([$source]));
        $source->setInviteContact(new ArrayCollection([$inviteContact]));

        $ideaInvite = new \Project\Entity\Idea\Invite();
        $ideaInvite->setId(1);
        $ideaInvite->setContact($source);
        $source->setIdeaInvite(new ArrayCollection([$ideaInvite]));

        $ideaMessageboard = new MessageBoard();
        $ideaMessageboard->setId(1);
        $ideaMessageboard->setContact($source);
        $source->setIdeaMessageboard(new ArrayCollection([$ideaMessageboard]));

        $ideaInviteContact = new \Project\Entity\Idea\Invite();
        $ideaInviteContact->setId(2);
        $ideaInviteContact->setInviteContact(new ArrayCollection([$source]));
        $source->setIdeaInviteContact(new ArrayCollection([$ideaInviteContact]));

        $loi = new Loi();
        $loi->setId(1);
        $loi->setContact($source);
        $source->setLoi(new ArrayCollection([$loi]));

        $loiApprover = new Loi();
        $loiApprover->setId(2);
        $loiApprover->setApprover($source);
        $source->setLoiApprover(new ArrayCollection([$loiApprover]));

        $affiliationDoa = new \Affiliation\Entity\Doa();
        $affiliationDoa->setId(1);
        $affiliationDoa->setContact($source);
        $source->setAffiliationDoa(new ArrayCollection([$affiliationDoa]));

        $permitContact = new \Admin\Entity\Permit\Contact();
        $permitContact->setId(1);
        $permitContact->setContact($source);
        $source->setPermitContact(new ArrayCollection([$permitContact]));

        $session = new Session();
        $session->setId(1);
        $session->setContact($source);
        $source->setSession(new ArrayCollection([$session]));

        $voter = new Voter();
        $voter->setId(1);
        $voter->setContact(new ArrayCollection([$source]));
        $source->setVoter(new ArrayCollection([$voter]));

        $tour = new Tour();
        $tour->setId(1);
        $tour->setContact($source);
        $source->setTour(new ArrayCollection([$tour]));

        $tourContact = new Tour();
        $tourContact->setId(2);
        $tourContact->setTourContact(new ArrayCollection([$source]));
        $source->setTourContact(new ArrayCollection([$tourContact]));

        $doaReminderReveiver = new DoaReminder();
        $doaReminderReveiver->setId(1);
        $doaReminderReveiver->setReceiver($source);
        $source->setDoaReminderReceiver(new ArrayCollection([$doaReminderReveiver]));

        $doaReminderSender = new DoaReminder();
        $doaReminderSender->setId(2);
        $doaReminderSender->setSender($source);
        $source->setDoaReminderSender(new ArrayCollection([$doaReminderSender]));

        $loiReminderReveiver = new LoiReminder();
        $loiReminderReveiver->setId(1);
        $loiReminderReveiver->setReceiver($source);
        $source->setLoiReminderReceiver(new ArrayCollection([$loiReminderReveiver]));

        $loiReminderSender = new LoiReminder();
        $loiReminderSender->setId(2);
        $loiReminderSender->setSender($source);
        $source->setLoiReminderSender(new ArrayCollection([$loiReminderSender]));

        $blog = new Blog();
        $blog->setId(1);
        $blog->setContact($source);
        $source->setBlog(new ArrayCollection([$blog]));

        $blogMessage = new \News\Entity\Message();
        $blogMessage->setId(1);
        $blogMessage->setContact($source);
        $source->setBlogMessage(new ArrayCollection([$blogMessage]));

        $journalEntry = new Entry();
        $journalEntry->setId(1);
        $journalEntry->setContact($source);
        $source->setJournalEntry(new ArrayCollection([$journalEntry]));

        $journal = new Journal();
        $journal->setId(1);
        $journal->setContact($source);
        $source->setJournal(new ArrayCollection([$journal]));

        $organisationJournal = new Journal();
        $organisationJournal->setId(2);
        $organisationJournal->setOrganisationContact($source);
        $source->setOrganisationJournal(new ArrayCollection([$organisationJournal]));

        $invoiceLog = new \Invoice\Entity\Log();
        $invoiceLog->setId(1);
        $invoiceLog->setContact($source);
        $source->setInvoiceLog(new ArrayCollection([$invoiceLog]));

        $invoiceReminder = new Reminder();
        $invoiceReminder->setId(1);
        $invoiceReminder->setContact($source);
        $source->setReminder(new ArrayCollection([$invoiceReminder]));

        $achievement = new Achievement();
        $achievement->setId(1);
        $achievement->setContact($source);
        $source->setAchievement(new ArrayCollection([$achievement]));

        $projectLog = new \Project\Entity\Log();
        $projectLog->setId(1);
        $projectLog->setContact($source);
        $source->setProjectLog(new ArrayCollection([$projectLog]));

        $changeRequestProcess = new Process();
        $changeRequestProcess->setId(1);
        $changeRequestProcess->setContact($source);
        $source->setChangeRequestProcess(new ArrayCollection([$changeRequestProcess]));

        $changeRequestCostChange = new CostChange();
        $changeRequestCostChange->setId(1);
        $changeRequestCostChange->setContact($source);
        $source->setChangeRequestCostChange(new ArrayCollection([$changeRequestCostChange]));

        $changeRequestCountry = new Country();
        $changeRequestCountry->setId(1);
        $changeRequestCountry->setContact($source);
        $source->setChangeRequestCountry(new ArrayCollection([$changeRequestCountry]));

        $versionContact = new \Project\Entity\Version\Contact();
        $versionContact->setId(1);
        $versionContact->setContact($source);
        $source->setVersionContact(new ArrayCollection([$versionContact]));

        $workpackageContact = new \Project\Entity\Workpackage\Contact();
        $workpackageContact->setId(1);
        $workpackageContact->setContact($source);
        $source->setWorkpackageContact(new ArrayCollection([$workpackageContact]));

        $logCreatedBy = new Log();
        $logCreatedBy->setId(1);
        $logCreatedBy->setCreatedBy($source);
        $source->setLogCreatedBy(new ArrayCollection([$logCreatedBy]));

        $log = new Log();
        $log->setId(2);
        $log->setContact($source);
        $source->setLog(new ArrayCollection([$log]));

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

        $inviteContact = new Invite();
        $inviteContact->setId(3);
        $inviteContact->setInviteContact(new ArrayCollection([$target]));
        $target->setInviteContact(new ArrayCollection([$inviteContact]));

        $ideaInviteContact = new \Project\Entity\Idea\Invite();
        $ideaInviteContact->setId(3);
        $ideaInviteContact->setInviteContact(new ArrayCollection([$target]));
        $target->setIdeaInviteContact(new ArrayCollection([$ideaInviteContact]));

        $voter = new Voter();
        $voter->setId(2);
        $voter->setContact(new ArrayCollection([$target]));
        $target->setVoter(new ArrayCollection([$voter]));

        $tourContact = new Tour();
        $tourContact->setId(3);
        $tourContact->setTourContact(new ArrayCollection([$target]));
        $target->setTourContact(new ArrayCollection([$tourContact]));

        return $target;
    }

    /**
     * Set up the translator mock object.
     *
     * @return Translator|MockObject
     */
    private function setUpTranslatorMock()
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
    private function setUpControllerMock()
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
    private function setUpEntityManagerMock(bool $throwException = false)
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

        $entityManagerMock->expects($this->exactly(\count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects($this->once())->method('remove')->with($this->source);
        $entityManagerMock->expects($this->exactly(2))->method('flush');

        return $entityManagerMock;
    }

    /**
     * Free memory
     */
    public function tearDown()
    {
        $this->source = null;
        $this->target = null;
        $this->translator = null;
    }
}