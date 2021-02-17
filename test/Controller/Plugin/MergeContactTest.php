<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace ContactTest\Controller\Plugin;

use Admin\Entity\Access;
use Admin\Entity\Pageview;
use Admin\Entity\Permit\Role;
use Admin\Entity\Session;
use Affiliation\Entity\Affiliation;
use Affiliation\Entity\Doa\Reminder as DoaReminder;
use Affiliation\Entity\Financial;
use Affiliation\Entity\Loi;
use Calendar\Entity\Calendar;
use Contact\Controller\ContactAdminController;
use Contact\Controller\Plugin\MergeContact;
use Contact\Entity\Address;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\Dnd;
use Contact\Entity\Email;
use Contact\Entity\Log;
use Contact\Entity\Note;
use Contact\Entity\OptIn;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Phone;
use Contact\Entity\Photo;
use Contact\Entity\Profile;
use Contact\Entity\Selection;
use Contact\Entity\SelectionContact;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use ErrorHeroModule\Handler\Logging;
use Evaluation\Entity\Evaluation;
use Evaluation\Entity\Reviewer;
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
use Organisation\Entity\ParentEntity;
use Organisation\Entity\Parent\Organisation;
use PHPUnit\Framework\MockObject\MockObject;
use Program\Entity\Doa;
use Program\Entity\Funder;
use Program\Entity\Nda;
use Project\Entity\Achievement;
use Project\Entity\Booth;
use Project\Entity\Changelog;
use Project\Entity\ChangeRequest\CostChange;
use Project\Entity\ChangeRequest\Country;
use Project\Entity\ChangeRequest\Process;
use Project\Entity\Contract;
use Project\Entity\Description\Description;
use Project\Entity\Document\Document;
use Project\Entity\Idea\Idea;
use Project\Entity\Idea\Message;
use Project\Entity\Idea\Partner;
use Project\Entity\Invite;
use Project\Entity\Pca;
use Project\Entity\Project;
use Project\Entity\Rationale;
use Project\Entity\Report\EffortSpent;
use Project\Entity\Report\Item;
use Project\Entity\Report\Report;
use Project\Entity\Report\WorkpackageDescription;
use Project\Entity\Result\Result;
use Project\Entity\Version\Version;
use Project\Entity\Workpackage\Workpackage;
use Publication\Entity\Download;
use Publication\Entity\Publication;
use Testing\Util\AbstractServiceTest;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Stdlib\DispatchableInterface;

use function count;

/**
 * Class MergeContactTest
 *
 * @package ContactTest\Controller\Plugin
 */
final class MergeContactTest extends AbstractServiceTest
{
    private ?Contact $source = null;
    private ?Contact $target = null;
    private ?TranslatorInterface $translator = null;
/**
     * Set up basic properties
     */
    public function setUp(): void
    {
        $this->source     = $this->createSource();
        $this->target     = $this->createTarget();
        $this->translator = $this->setUpTranslatorMock();
    }

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
        $source->setDateOfBirth(new DateTime('1970-01-01'));
        $source->setDateCreated(new DateTime('2015-01-01'));
        $source->setLastUpdate(new DateTime());
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
        $selfApprovedNda = new Nda();
        $selfApprovedNda->setId(2);
        $selfApprovedNda->setContact($source);
        $selfApprovedNda->setApprover($source);
        $source->setNda(new ArrayCollection([$nda, $selfApprovedNda]));
        $ndaApproved = new Nda();
        $ndaApproved->setId(3);
        $ndaApproved->setApprover($source);
        $source->setNdaApprover(new ArrayCollection([$ndaApproved]));
        $programDoa = new Doa();
        $programDoa->setId(1);
        $programDoa->setContact($source);
        $source->setProgramDoa(new ArrayCollection([$programDoa]));
        $parentDoa = new \Organisation\Entity\Parent\Doa();
        $parentDoa->setId(1);
        $parentDoa->setContact($source);
        $source->setParentDoa(new ArrayCollection([$parentDoa]));
        $idea = new Idea();
        $idea->setId(1);
        $idea->setContact($source);
        $source->setIdea(new ArrayCollection([$idea]));
        $favouriteIdea = new Idea();
        $favouriteIdea->setId(2);
        $favouriteIdea->setFavourite(new ArrayCollection([$source]));
        $source->setFavouriteIdea(new ArrayCollection([$favouriteIdea]));
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
        $parent = new ParentEntity();
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
        $registration = new Registration();
        $registration->setId(1);
        $registration->setContact($source);
        $source->setRegistration(new ArrayCollection([$registration]));
        $badge = new Badge();
        $badge->setId(1);
        $badge->setContact($source);
        $source->setBadge(new ArrayCollection([$badge]));
        $badge2 = new Badge();
        $badge2->setId(2);
        $badgeContact = new \Event\Entity\Badge\Contact();
        $badgeContact->setId(1);
        $badgeContact->setBadge($badge2);
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
        $selection2 = new Selection();
        $selection2->setId(2);
        $selectionContact = new SelectionContact();
        $selectionContact->setId(1);
        $selectionContact->setSelection($selection2);
        $selectionContact->setContact($source);
        $source->setSelectionContact(new ArrayCollection([$selectionContact]));
        $mailing2 = new Mailing();
        $mailing2->setId(2);
        $mailingContact = new \Mailing\Entity\Contact();
        $mailingContact->setId(1);
        $mailingContact->setMailing($mailing2);
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
        $projectReview = new Reviewer();
        $projectReview->setId(1);
        $projectReview->setContact($source);
        $source->setProjectReviewers(new ArrayCollection([$projectReview]));
        $reviewContact = new Reviewer\Contact();
        $reviewContact->setId(1);
        $reviewContact->setContact($source);
        $source->setProjectReviewerContact($reviewContact);
        $projectVersionReviewer = new \Project\Entity\Version\Reviewer();
        $projectVersionReviewer->setId(1);
        $projectVersionReviewer->setContact($source);
        $source->setProjectVersionReviewers(new ArrayCollection([$projectVersionReviewer]));
        $projectReport = new Report();
        $projectReport->setId(1);
        $projectReport->setContact($source);
        $source->setProjectReport(new ArrayCollection([$projectReport]));
        $projectCalendarReviewer = new \Project\Entity\Calendar\Reviewer();
        $projectCalendarReviewer->setId(1);
        $projectCalendarReviewer->setContact($source);
        $source->setProjectCalendarReviewers(new ArrayCollection([$projectCalendarReviewer]));
        $projectReportReviewer = new \Project\Entity\Report\Reviewer();
        $projectReportReviewer->setId(1);
        $projectReportReviewer->setContact($source);
        $source->setProjectReportReviewers(new ArrayCollection([$projectReportReviewer]));
        $invite = new Invite();
        $invite->setId(1);
        $invite->setContact($source);
        $source->setInvite(new ArrayCollection([$invite]));
        $pca = new Pca();
        $pca->setId(1);
        $pca->setContact($source);
        $source->setPca(new ArrayCollection([$pca]));
        $inviteContact = new Invite();
        $inviteContact->setId(2);
        $inviteContact->setInviteContact(new ArrayCollection([$source]));
        $source->setInviteContact(new ArrayCollection([$inviteContact]));
        $ideaInvite = new \Project\Entity\Idea\Invite();
        $ideaInvite->setId(1);
        $ideaInvite->setContact($source);
        $source->setIdeaInvite(new ArrayCollection([$ideaInvite]));
        $ideaInviteContact = new \Project\Entity\Idea\Invite();
        $ideaInviteContact->setId(2);
        $ideaInviteContact->setInviteContact(new ArrayCollection([$source]));
        $source->setIdeaInviteContact(new ArrayCollection([$ideaInviteContact]));
        $loi = new Loi();
        $loi->setId(1);
        $loi->setContact($source);
        $selfApprovedLoi = new Loi();
        $selfApprovedLoi->setId(2);
        $selfApprovedLoi->setContact($source);
        $selfApprovedLoi->setApprover($source);
        $source->setLoi(new ArrayCollection([$loi, $selfApprovedLoi]));
        $loiApprover = new Loi();
        $loiApprover->setId(3);
        $loiApprover->setApprover($source);
        $source->setLoiApprover(new ArrayCollection([$loiApprover]));
        $affiliationDoa = new \Affiliation\Entity\Doa();
        $affiliationDoa->setId(1);
        $affiliationDoa->setContact($source);
        $source->setAffiliationDoa(new ArrayCollection([$affiliationDoa]));
        $role = new Role();
        $role->setId(1);
        $permitContact = new \Admin\Entity\Permit\Contact();
        $permitContact->setId(1);
        $permitContact->setContact($source);
        $permitContact->setRole($role);
        $permitContact->setKeyId(1);
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
        $doaReminderReceiver = new DoaReminder();
        $doaReminderReceiver->setId(1);
        $doaReminderReceiver->setReceiver($source);
        $source->setDoaReminderReceiver(new ArrayCollection([$doaReminderReceiver]));
        $doaReminderSender = new DoaReminder();
        $doaReminderSender->setId(2);
        $doaReminderSender->setSender($source);
        $source->setDoaReminderSender(new ArrayCollection([$doaReminderSender]));
        $blog = new Blog();
        $blog->setId(1);
        $blog->setContact($source);
        $source->setBlog(new ArrayCollection([$blog]));
        $blogMessage = new \News\Entity\Blog\Message();
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
        $source->setProjectlog(new ArrayCollection([$projectLog]));
        $projectChangeLog = new Changelog();
        $projectChangeLog->setId(1);
        $projectChangeLog->setContact($source);
        $source->setProjectChangelog(new ArrayCollection([$projectChangeLog]));
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
        $source->getWorkpackageContact()->add($workpackageContact);
        $logCreatedBy = new Log();
        $logCreatedBy->setId(1);
        $logCreatedBy->setCreatedBy($source);
        $source->getLogCreatedBy()->add($logCreatedBy);
        $log = new Log();
        $log->setId(2);
        $log->setContact($source);
        $source->getLog()->add($log);
        $pageView = new Pageview();
        $pageView->setId(1);
        $pageView->setContact($source);
        $source->getPageview()->add($pageView);
        return $source;
    }

    private function createTarget(): Contact
    {
        $target = new Contact();
        $target->setId(2);
        $target->setDateCreated(new DateTime('2017-01-01'));
        $target->setLastUpdate(new DateTime('2017-01-01'));
        $emailAddress = new Email();
        $emailAddress->setId(2);
        $emailAddress->setEmail('duplicate@itea3.org');
        $emailAddress->setContact($target);
        $target->setEmailAddress(new ArrayCollection([$emailAddress]));
        $optIn = new OptIn();
        $optIn->setId(2);
        $optIn->setContact(new ArrayCollection([$target]));
        $target->setOptIn(new ArrayCollection([$optIn]));
        $selection3 = new Selection();
        $selection3->setId(3);
        $selectionContact = new SelectionContact();
        $selectionContact->setId(2);
        $selectionContact->setSelection($selection3);
        $selectionContact->setContact($target);
        $target->setSelectionContact(new ArrayCollection([$selectionContact]));
        $mailing3 = new Mailing();
        $mailing3->setId(3);
        $mailingContact = new \Mailing\Entity\Contact();
        $mailingContact->setId(2);
        $mailingContact->setMailing($mailing3);
        $mailingContact->setContact($target);
        $target->setMailingContact(new ArrayCollection([$mailingContact]));
        $favouriteIdea = new Idea();
        $favouriteIdea->setId(3);
        $favouriteIdea->setFavourite(new ArrayCollection([$target]));
        $target->setFavouriteIdea(new ArrayCollection([$favouriteIdea]));
        $associate = new Affiliation();
        $associate->setId(3);
        $associate->setAssociate(new ArrayCollection([$target]));
        $target->setAssociate(new ArrayCollection([$associate]));
        $badge3 = new Badge();
        $badge3->setId(3);
        $badgeContact = new \Event\Entity\Badge\Contact();
        $badgeContact->setId(2);
        $badgeContact->setBadge($badge3);
        $badgeContact->setContact($target);
        $target->setBadgeContact(new ArrayCollection([$badgeContact]));
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
            ->onlyMethods(['translate'])
            ->getMock();
// Just let the translator return the untranslated string
        $translatorMock
            ->method('translate')
            ->will(self::returnArgument(0));
        return $translatorMock;
    }

    /**
     * Test the basic __invoke magic method returning the plugin instance
     *
     * @covers \Contact\Controller\Plugin\MergeContact::__invoke
     * @covers \Contact\Controller\Plugin\MergeContact::__construct
     */
    public function testInvoke(): void
    {
        $mergeContact = new MergeContact($this->getEntityManagerMock(), $this->translator);
        $instance = $mergeContact();
        self::assertSame($mergeContact, $instance);
    }

    /**
     * Test the pre-merge checks
     *
     * @covers \Contact\Controller\Plugin\MergeContact::checkMerge
     */
    public function testCheckMerge(): void
    {
        $mergeContact = new MergeContact($this->getEntityManagerMock(), $this->translator);
// Run the merge check
        $errors = $mergeContact()->checkMerge($this->source, $this->target);
        self::assertEquals([], $errors);
    }

    /**
     * Test the pre-merge checks failing
     *
     * @covers \Contact\Controller\Plugin\MergeContact::checkMerge
     */
    public function testCheckMergeFail(): void
    {
        $mergeContact = new MergeContact($this->getEntityManagerMock(), $this->translator);
// Run the merge check
        $officeContact = new OfficeContact();
        $this->source->setOfficeContact($officeContact);
        $errors = $mergeContact()->checkMerge($this->source, $this->source);
        self::assertEquals('txt-cant-merge-the-same-contact', $errors[0]);
        self::assertEquals('txt-cant-merge-office-contacts', $errors[1]);
        $this->source->setOfficeContact(null);
    }

    /**
     * Test the actual merge
     *
     * @covers \Contact\Controller\Plugin\MergeContact::merge
     */
    public function testMerge(): void
    {
        /** @var DispatchableInterface $controllerMock */
        $controllerMock = $this->setUpControllerMock();
        $mergeOrganisation = new MergeContact($this->setUpEntityManagerMock(), $this->translator);
        $mergeOrganisation->setController($controllerMock);
        $result = $mergeOrganisation()->merge($this->source, $this->target);
        self::assertTrue($result['success']);
        self::assertSame($this->source->getFirstName(), $this->target->getFirstName());
        self::assertSame($this->source->getMiddleName(), $this->target->getMiddleName());
        self::assertSame($this->source->getLastName(), $this->target->getLastName());
        self::assertSame($this->source->getEmail(), $this->target->getEmail());
        self::assertSame($this->source->getGender(), $this->target->getGender());
        self::assertSame($this->source->getTitle(), $this->target->getTitle());
        self::assertSame($this->source->getPosition(), $this->target->getPosition());
        self::assertSame($this->source->getDepartment(), $this->target->getDepartment());
        self::assertSame($this->source->getDateOfBirth(), $this->target->getDateOfBirth());
        self::assertSame($this->source->getDateCreated(), $this->target->getDateCreated());
        self::assertSame($this->source->getLastUpdate(), $this->target->getLastUpdate());
        self::assertSame(1, $this->target->getAccess()->count());
        self::assertSame(1, $this->target->getAccess()->first()->getId());
        self::assertSame(2, $this->target->getEmailAddress()->count());
        self::assertSame('duplicate@itea3.org', $this->target->getEmailAddress()->get(0)->getEmail());
        self::assertSame('test.von.dummy@itea3.org', $this->target->getEmailAddress()->get(1)->getEmail());
        self::assertSame(1, $this->target->getAddress()->count());
        self::assertSame(1, $this->target->getAddress()->first()->getId());
        self::assertSame(1, $this->target->getPhone()->count());
        self::assertSame(1, $this->target->getPhone()->first()->getId());
        self::assertSame(2, $this->target->getOptIn()->count());
        self::assertSame(2, $this->target->getOptIn()->get(0)->getId());
        self::assertSame(1, $this->target->getOptIn()->get(1)->getId());
        self::assertSame(1, $this->target->getProject()->count());
        self::assertSame(1, $this->target->getProject()->first()->getId());
        self::assertSame(1, $this->target->getRationale()->count());
        self::assertSame(1, $this->target->getRationale()->first()->getId());
        self::assertSame(1, $this->target->getProjectDescription()->count());
        self::assertSame(1, $this->target->getProjectDescription()->first()->getId());
        self::assertSame(1, $this->target->getProjectVersion()->count());
        self::assertSame(1, $this->target->getProjectVersion()->first()->getId());
        self::assertSame(1, $this->target->getProjectReportItem()->count());
        self::assertSame(1, $this->target->getProjectReportItem()->first()->getId());
        self::assertSame(1, $this->target->getProjectReportWorkpackageDescription()->count());
        self::assertSame(1, $this->target->getProjectReportWorkpackageDescription()->first()->getId());
        self::assertSame(1, $this->target->getProjectReportEffortSpent()->count());
        self::assertSame(1, $this->target->getProjectReportEffortSpent()->first()->getId());
        self::assertSame(1, $this->target->getProjectDocument()->count());
        self::assertSame(1, $this->target->getProjectDocument()->first()->getId());
        self::assertSame(1, $this->target->getDnd()->count());
        self::assertSame(1, $this->target->getDnd()->first()->getId());
        self::assertSame(1, $this->target->getContract()->count());
        self::assertSame(1, $this->target->getContract()->first()->getId());
        self::assertSame(1, $this->target->getContractVersion()->count());
        self::assertSame(1, $this->target->getContractVersion()->first()->getId());
        self::assertSame(2, $this->target->getNda()->count());
        self::assertSame(1, $this->target->getNda()->first()->getId());
        self::assertSame(2, $this->target->getNda()->next()->getId());
        self::assertSame(1, $this->target->getNdaApprover()->count());
        self::assertSame(3, $this->target->getNdaApprover()->first()->getId());
        self::assertSame(1, $this->target->getProgramDoa()->count());
        self::assertSame(1, $this->target->getProgramDoa()->first()->getId());
        self::assertSame(1, $this->target->getParentDoa()->count());
        self::assertSame(1, $this->target->getParentDoa()->first()->getId());
        self::assertSame(1, $this->target->getContactOrganisation()->getId());
        self::assertSame(1, $this->target->getIdea()->count());
        self::assertSame(1, $this->target->getIdea()->first()->getId());
        self::assertSame(2, $this->target->getFavouriteIdea()->count());
        self::assertSame(3, $this->target->getFavouriteIdea()->get(0)->getId());
        self::assertSame(2, $this->target->getFavouriteIdea()->get(1)->getId());
        self::assertSame(1, $this->target->getOrganisationLog()->count());
        self::assertSame(1, $this->target->getOrganisationLog()->first()->getId());
        self::assertSame(1, $this->target->getIdeaPartner()->count());
        self::assertSame(1, $this->target->getIdeaPartner()->first()->getId());
        self::assertSame(1, $this->target->getAffiliation()->count());
        self::assertSame(1, $this->target->getAffiliation()->first()->getId());
        self::assertSame(1, $this->target->getAffiliationLog()->count());
        self::assertSame(1, $this->target->getAffiliationLog()->first()->getId());
        self::assertSame(1, $this->target->getFinancial()->count());
        self::assertSame(1, $this->target->getFinancial()->first()->getId());
        self::assertSame(1, $this->target->getAffiliationDescription()->count());
        self::assertSame(1, $this->target->getAffiliationDescription()->first()->getId());
        self::assertSame(1, $this->target->getAffiliationVersion()->count());
        self::assertSame(1, $this->target->getAffiliationVersion()->first()->getId());
        self::assertSame(1, $this->target->getInvoice()->count());
        self::assertSame(1, $this->target->getInvoice()->first()->getId());
        self::assertSame(1, $this->target->getParent()->count());
        self::assertSame(1, $this->target->getParent()->first()->getId());
        self::assertSame(1, $this->target->getParentFinancial()->count());
        self::assertSame(1, $this->target->getParentFinancial()->first()->getId());
        self::assertSame(1, $this->target->getParentOrganisation()->count());
        self::assertSame(1, $this->target->getParentOrganisation()->first()->getId());
        self::assertSame(1, $this->target->getPublication()->count());
        self::assertSame(1, $this->target->getPublication()->first()->getId());
        self::assertSame(1, $this->target->getPublicationDownload()->count());
        self::assertSame(1, $this->target->getPublicationDownload()->first()->getId());
        self::assertSame(1, $this->target->getPhoto()->count());
        self::assertSame(1, $this->target->getPhoto()->first()->getId());
        self::assertSame(2, $this->target->getAssociate()->count());
        self::assertSame(3, $this->target->getAssociate()->get(0)->getId());
        self::assertSame(2, $this->target->getAssociate()->get(1)->getId());
        self::assertSame(1, $this->target->getFunder()->getId());
        self::assertSame(1, $this->target->getDeeplinkContact()->count());
        self::assertSame(1, $this->target->getDeeplinkContact()->first()->getId());
        self::assertSame(1, $this->target->getProfile()->getId());
        self::assertSame(1, $this->target->getRegistration()->count());
        self::assertSame(1, $this->target->getRegistration()->first()->getId());
        self::assertSame(1, $this->target->getBadge()->count());
        self::assertSame(1, $this->target->getBadge()->first()->getId());
        self::assertSame(2, $this->target->getBadgeContact()->count());
        self::assertSame(3, $this->target->getBadgeContact()->get(0)->getBadge()->getId());
        self::assertSame(2, $this->target->getBadgeContact()->get(1)->getBadge()->getId());
        self::assertSame(2, $this->target->getBadgeContact()->get(0)->getId());
        self::assertSame(1, $this->target->getBadgeContact()->get(1)->getId());
        self::assertSame(1, $this->target->getBoothContact()->count());
        self::assertSame(1, $this->target->getBoothContact()->first()->getId());
        self::assertSame(1, $this->target->getProjectBooth()->count());
        self::assertSame(1, $this->target->getProjectBooth()->first()->getId());
        self::assertSame(1, $this->target->getOrganisationBooth()->count());
        self::assertSame(1, $this->target->getOrganisationBooth()->first()->getId());
        self::assertSame(1, $this->target->getBoothFinancial()->count());
        self::assertSame(1, $this->target->getBoothFinancial()->first()->getId());
        self::assertSame(2, $this->target->getNote()->count());
        self::assertSame('Merged contact Test von Dummy (1) into Test von Dummy (2)', $this->target->getNote()->get(0)->getNote());
        self::assertSame(1, $this->target->getNote()->get(1)->getId());
        self::assertSame(1, $this->target->getSelection()->count());
        self::assertSame(1, $this->target->getSelection()->first()->getId());
        self::assertSame(2, $this->target->getSelectionContact()->count());
        self::assertSame(3, $this->target->getSelectionContact()->get(0)->getSelection()->getId());
        self::assertSame(2, $this->target->getSelectionContact()->get(1)->getSelection()->getId());
        self::assertSame(2, $this->target->getSelectionContact()->get(0)->getId());
        self::assertSame(1, $this->target->getSelectionContact()->get(1)->getId());
        self::assertSame(2, $this->target->getMailingContact()->count());
        self::assertSame(3, $this->target->getMailingContact()->get(0)->getMailing()->getId());
        self::assertSame(2, $this->target->getMailingContact()->get(1)->getMailing()->getId());
        self::assertSame(2, $this->target->getMailingContact()->get(0)->getId());
        self::assertSame(1, $this->target->getMailingContact()->get(1)->getId());
        self::assertSame(1, $this->target->getMailing()->count());
        self::assertSame(1, $this->target->getMailing()->first()->getId());
        self::assertSame(1, $this->target->getEmailMessage()->count());
        self::assertSame(1, $this->target->getEmailMessage()->first()->getId());
        self::assertSame(1, $this->target->getResult()->count());
        self::assertSame(1, $this->target->getResult()->first()->getId());
        self::assertSame(1, $this->target->getWorkpackage()->count());
        self::assertSame(1, $this->target->getWorkpackage()->first()->getId());
        self::assertSame(1, $this->target->getWorkpackageDocument()->count());
        self::assertSame(1, $this->target->getWorkpackageDocument()->first()->getId());
        self::assertSame(1, $this->target->getIdeaMessage()->count());
        self::assertSame(1, $this->target->getIdeaMessage()->first()->getId());
        self::assertSame(1, $this->target->getEvaluation()->count());
        self::assertSame(1, $this->target->getEvaluation()->first()->getId());
        self::assertSame(1, $this->target->getCalendar()->count());
        self::assertSame(1, $this->target->getCalendar()->first()->getId());
        self::assertSame(1, $this->target->getCalendarContact()->count());
        self::assertSame(1, $this->target->getCalendarContact()->first()->getId());
        self::assertSame(1, $this->target->getCalendarDocument()->count());
        self::assertSame(1, $this->target->getCalendarDocument()->first()->getId());
        self::assertSame(1, $this->target->getProjectReviewers()->count());
        self::assertSame(1, $this->target->getProjectReviewers()->first()->getId());
        self::assertSame(1, $this->target->getProjectReviewerContact()->getId());
        self::assertSame(1, $this->target->getProjectVersionReviewers()->count());
        self::assertSame(1, $this->target->getProjectVersionReviewers()->first()->getId());
        self::assertSame(1, $this->target->getProjectReport()->count());
        self::assertSame(1, $this->target->getProjectReport()->first()->getId());
        self::assertSame(1, $this->target->getProjectCalendarReviewers()->count());
        self::assertSame(1, $this->target->getProjectCalendarReviewers()->first()->getId());
        self::assertSame(1, $this->target->getProjectReportReviewers()->count());
        self::assertSame(1, $this->target->getProjectReportReviewers()->first()->getId());
        self::assertSame(1, $this->target->getInvite()->count());
        self::assertSame(1, $this->target->getInvite()->first()->getId());
        self::assertSame(2, $this->target->getInviteContact()->count());
        self::assertSame(3, $this->target->getInviteContact()->get(0)->getId());
        self::assertSame(2, $this->target->getInviteContact()->get(1)->getId());
        self::assertSame(1, $this->target->getIdeaInvite()->count());
        self::assertSame(1, $this->target->getIdeaInvite()->first()->getId());
        self::assertSame(2, $this->target->getIdeaInviteContact()->count());
        self::assertSame(3, $this->target->getIdeaInviteContact()->get(0)->getId());
        self::assertSame(2, $this->target->getIdeaInviteContact()->get(1)->getId());
        self::assertSame(2, $this->target->getLoi()->count());
        self::assertSame(1, $this->target->getLoi()->first()->getId());
        self::assertSame(2, $this->target->getLoi()->next()->getId());
        self::assertSame(1, $this->target->getLoiApprover()->count());
        self::assertSame(3, $this->target->getLoiApprover()->first()->getId());
        self::assertSame(1, $this->target->getAffiliationDoa()->count());
        self::assertSame(1, $this->target->getAffiliationDoa()->first()->getId());
        self::assertSame(1, $this->target->getPermitContact()->count());
        self::assertSame(1, $this->target->getPermitContact()->first()->getId());
        self::assertSame(1, $this->target->getSession()->count());
        self::assertSame(1, $this->target->getSession()->first()->getId());
        self::assertSame(1, $this->target->getPca()->count());
        self::assertSame(1, $this->target->getPca()->first()->getId());
        self::assertSame(2, $this->target->getVoter()->count());
        self::assertSame(2, $this->target->getVoter()->get(0)->getId());
        self::assertSame(1, $this->target->getVoter()->get(1)->getId());
        self::assertSame(1, $this->target->getTour()->count());
        self::assertSame(1, $this->target->getTour()->first()->getId());
        self::assertSame(2, $this->target->getTourContact()->count());
        self::assertSame(3, $this->target->getTourContact()->get(0)->getId());
        self::assertSame(2, $this->target->getTourContact()->get(1)->getId());
        self::assertSame(1, $this->target->getDoaReminderReceiver()->count());
        self::assertSame(1, $this->target->getDoaReminderReceiver()->first()->getId());
        self::assertSame(1, $this->target->getDoaReminderSender()->count());
        self::assertSame(2, $this->target->getDoaReminderSender()->first()->getId());
        self::assertSame(1, $this->target->getBlog()->count());
        self::assertSame(1, $this->target->getBlog()->first()->getId());
        self::assertSame(1, $this->target->getBlogMessage()->count());
        self::assertSame(1, $this->target->getBlogMessage()->first()->getId());
        self::assertSame(1, $this->target->getJournalEntry()->count());
        self::assertSame(1, $this->target->getJournalEntry()->first()->getId());
        self::assertSame(1, $this->target->getJournal()->count());
        self::assertSame(1, $this->target->getJournal()->first()->getId());
        self::assertSame(1, $this->target->getOrganisationJournal()->count());
        self::assertSame(2, $this->target->getOrganisationJournal()->first()->getId());
        self::assertSame(1, $this->target->getInvoiceLog()->count());
        self::assertSame(1, $this->target->getInvoiceLog()->first()->getId());
        self::assertSame(1, $this->target->getReminder()->count());
        self::assertSame(1, $this->target->getReminder()->first()->getId());
        self::assertSame(1, $this->target->getAchievement()->count());
        self::assertSame(1, $this->target->getAchievement()->first()->getId());
        self::assertSame(1, $this->target->getProjectLog()->count());
        self::assertSame(1, $this->target->getProjectLog()->first()->getId());
        self::assertSame(1, $this->target->getProjectChangelog()->count());
        self::assertSame(1, $this->target->getProjectChangelog()->first()->getId());
        self::assertSame(1, $this->target->getChangeRequestProcess()->count());
        self::assertSame(1, $this->target->getChangeRequestProcess()->first()->getId());
        self::assertSame(1, $this->target->getChangeRequestCostChange()->count());
        self::assertSame(1, $this->target->getChangeRequestCostChange()->first()->getId());
        self::assertSame(1, $this->target->getChangeRequestCountry()->count());
        self::assertSame(1, $this->target->getChangeRequestCountry()->first()->getId());
        self::assertSame(1, $this->target->getVersionContact()->count());
        self::assertSame(1, $this->target->getVersionContact()->first()->getId());
        self::assertSame(1, $this->target->getWorkpackageContact()->count());
        self::assertSame(1, $this->target->getWorkpackageContact()->first()->getId());
        self::assertSame(1, $this->target->getLogCreatedBy()->count());
        self::assertSame(1, $this->target->getLogCreatedBy()->first()->getId());
        self::assertSame(2, $this->target->getLog()->count());
        self::assertSame(2, $this->target->getLog()->first()->getId());
        self::assertSame(1, $this->target->getPageview()->count());
        self::assertSame(1, $this->target->getPageview()->first()->getId());
        self::assertSame('Merged contact Test von Dummy (1) into Test von Dummy (2)', $this->target->getLog()->get(1)->getLog());
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
        $controllerMock = $this->getMockBuilder(ContactAdminController::class)
            ->disableOriginalConstructor()
            ->addMethods(['identity'])
            ->getMock();
        $controllerMock->expects(self::once())
            ->method('identity')
            ->willReturn($contact);
        return $controllerMock;
    }

    /**
     * Set up the entity manager mock object with expectations depending on the chosen merge strategy.
     *
     * @param bool $throwException
     *
     * @return EntityManager|MockObject
     */
    private function setUpEntityManagerMock(bool $throwException = false)
    {
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['persist', 'remove', 'flush'])
            ->getMock();
// Short circuit when an exception should be thrown
        if ($throwException) {
            $exception = new ORMException('Oops!');
            $entityManagerMock->method('persist')->will(self::throwException($exception));
            $entityManagerMock->method('remove')->will(self::throwException($exception));
            $entityManagerMock->method('flush')->will(self::throwException($exception));
            return $entityManagerMock;
        }

        // Setup the parameters depending on merge strategy
        $params = [
            [self::isInstanceOf(Log::class)],
            [self::isInstanceOf(Note::class)],
        ];
        $entityManagerMock->expects(self::exactly(count($params)))->method('persist')->withConsecutive(...$params);
        $entityManagerMock->expects(self::once())->method('remove')->with($this->source);
        $entityManagerMock->expects(self::exactly(3))->method('flush');
        return $entityManagerMock;
    }

    /**
     * Test a failing merge
     *
     * @covers \Contact\Controller\Plugin\MergeContact::merge
     */
    public function testMergeFail(): void
    {
        $entityManagerMock = $this->setUpEntityManagerMock(true);
        $mergeContactNoLog = new MergeContact($entityManagerMock, $this->translator);
        $responseNoLog = $mergeContactNoLog->merge($this->source, $this->target);
        self::assertEquals(false, $responseNoLog['success']);
        self::assertEquals('Oops!', $responseNoLog['errorMessage']);
/** @var Logging|MockObject $errorLoggerMock */
        $errorLoggerMock = $this->getMockBuilder(Logging::class)
            ->disableOriginalConstructor()
            ->setMethods(['handleErrorException'])
            ->getMock();
        $errorLoggerMock->expects(self::once())
            ->method('handleErrorException')
            ->with(self::isInstanceOf('Exception'));
        $mergeContactLog = new MergeContact($entityManagerMock, $this->translator, $errorLoggerMock);
        $responseLog = $mergeContactLog()->merge($this->source, $this->target);
        self::assertEquals(false, $responseLog['success']);
    }

    /**
     * Free memory
     */
    public function tearDown(): void
    {
        $this->source = null;
        $this->target = null;
        $this->translator = null;
    }
}
