<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/affiliation for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Contact\Controller\ContactAbstractController;
use Contact\Entity\Contact;
use Contact\Entity\Email;
use Contact\Entity\Log;
use Contact\Entity\Note;
use Contact\Entity\OpenId;
use Contact\Entity\OptIn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Event\Entity\Exhibition\Tour;
use Program\Entity\Domain;
use Program\Entity\Technology;
use Project\Entity\Idea\Idea;
use Project\Entity\Invite;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class MergeContact
 * @package Contact\Controller\Plugin
 */
class MergeContact extends AbstractPlugin
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MergeOrganisation constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @return MergeContact
     */
    public function __invoke(): MergeContact
    {
        return $this;
    }

    /**
     * @param Contact $source
     * @param Contact $target
     * @return array
     */
    public function checkMerge(Contact $source, Contact $target): array
    {
        $errors = [];

        // Checks here

        return $errors;
    }

    /**
     * @param Contact $source
     * @param Contact $target
     * @param LoggerInterface|null $logger
     * @return array
     */
    public function merge(Contact $source, Contact $target, LoggerInterface $logger = null): array
    {
        $response = ['success' => true, 'errorMessage' => ''];

        try {
            // Update contact properties
            if ($target->getFirstName() === null) {
                $target->setFirstName($source->getFirstName());
            }
            if ($target->getMiddleName() === null) {
                $target->setMiddleName($source->getMiddleName());
            }
            if ($target->getLastName() === null) {
                $target->setLastName($source->getLastName());
            }
            if ($target->getEmail() === null) {
                $target->setEmail($source->getEmail());
            }
            if ($target->getGender() === null) {
                $target->setGender($source->getGender());
            }
            if ($target->getTitle() === null) {
                $target->setTitle($source->getTitle());
            }
            if ($target->getPosition() === null) {
                $target->setPosition($source->getPosition());
            }
            if ($target->getDepartment() === null) {
                $target->setDepartment($source->getDepartment());
            }
            if ($target->getDateOfBirth() === null) {
                $target->setDateOfBirth($source->getDateOfBirth());
            }
            if ($source->getDateCreated() < $target->getDateCreated()) {
                $target->setDateCreated($source->getDateCreated());
            }
            if ($source->getLastUpdate() > $target->getLastUpdate()) {
                $target->setLastUpdate($source->getLastUpdate());
            }
            if ($source->getMessenger() === 1) {
                $target->setMessenger($source->getMessenger());
            }

            // Transfer access
            $newAccess = new ArrayCollection();
            foreach ($source->getAccess() as $access) {
                if (!$target->getAccess()->contains($access)) {
                    $newAccess->add($access);
                }
            }
            $source->setAccess(new ArrayCollection());
            $target->addAccess($newAccess);

            // Transfer e-mail addresses (with matching)
            $targetEmailAddresses = [];
            /** @var Email $emailTarget */
            foreach ($target->getEmailAddress() as $emailTarget) {
                $targetEmailAddresses[] = $emailTarget->getEmail();
            }
            /** @var Email $emailSource */
            foreach ($source->getEmailAddress() as $emailSource) {
                if (!\in_array($emailSource->getEmail(), $targetEmailAddresses)) {
                    $emailSource->setContact($target);
                    $target->getEmailAddress()->add($emailSource);
                }
            }

            // Transfer CV
            if (($source->getCv() !== null) && ($target->getCv() === null)) {
                $target->setCv($source->getCv());
                $target->getCv()->setContact($target);
            }
            $source->setCv(null);

            // Transfer addresses (no matching)
            foreach ($source->getAddress() as $key => $address) {
                $address->setContact($target);
                $target->getAddress()->add($address);
                $source->getAddress()->remove($key);
            }

            // Transfer phone numbers (no matching)
            foreach ($source->getPhone() as $key => $phone) {
                $phone->setContact($target);
                $target->getPhone()->add($phone);
                $source->getPhone()->remove($key);
            }

            // Transfer web addresses (no matching)
            foreach ($source->getWeb() as $key => $web) {
                $web->setContact($target);
                $target->getWeb()->add($web);
                $source->getWeb()->remove($key);
            }

            // Transfer opt-in (many-to-many, with matching)
            $targetOptIns = [];
            /** @var OptIn $optInTarget */
            foreach ($target->getOptIn() as $optInTarget) {
                $targetOptIns[] = $optInTarget->getId();
            }
            /** @var OptIn $optInSource */
            foreach ($source->getOptIn() as $optInSource) {
                if (!\in_array($optInSource->getId(), $targetOptIns)) {
                    $target->getOptIn()->add($optInSource);
                }
            }
            $source->setOptIn(new ArrayCollection());

            // Transfer projects (no matching)
            foreach ($source->getProject() as $key => $project) {
                $project->setContact($target);
                $target->getProject()->add($project);
                $source->getProject()->remove($key);
            }

            // Transfer rationale (no matching)
            foreach ($source->getRationale() as $key => $rationale) {
                $rationale->setContact($target);
                $target->getRationale()->add($rationale);
                $source->getRationale()->remove($key);
            }

            // Transfer project descriptions (many-to-many, no matching)
            foreach ($source->getProjectDescription() as $key => $description) {
                $description->getContact()->removeElement($source);
                $description->getContact()->add($target);
                $target->getProjectDescription()->add($description);
                $source->getProjectDescription()->remove($key);
            }

            // Transfer project versions (no matching)
            foreach ($source->getProjectVersion() as $key => $version) {
                $version->setContact($target);
                $target->getProjectVersion()->add($version);
                $source->getProjectVersion()->remove($key);
            }

            // Transfer project report items (no matching)
            foreach ($source->getProjectReportItem() as $key => $item) {
                $item->setContact($target);
                $target->getProjectReportItem()->add($item);
                $source->getProjectReportItem()->remove($key);
            }

            // Transfer project report workpackage descriptions (no matching)
            foreach ($source->getProjectReportWorkpackageDescription() as $key => $description) {
                $description->setContact($target);
                $target->getProjectReportWorkpackageDescription()->add($description);
                $source->getProjectReportWorkpackageDescription()->remove($key);
            }

            // Transfer project report effort spent (no matching)
            foreach ($source->getProjectReportEffortSpent() as $key => $effortSpent) {
                $effortSpent->setContact($target);
                $target->getProjectReportEffortSpent()->add($effortSpent);
                $source->getProjectReportEffortSpent()->remove($key);
            }

            // Transfer project documents (no matching)
            foreach ($source->getProjectDocument() as $key => $document) {
                $document->setContact($target);
                $target->getProjectDocument()->add($document);
                $source->getProjectDocument()->remove($key);
            }

            // Transfer dnd (no matching)
            foreach ($source->getDnd() as $key => $dnd) {
                $dnd->setContact($target);
                $target->getDnd()->add($dnd);
                $source->getDnd()->remove($key);
            }

            // Transfer contract (no matching)
            foreach ($source->getContract() as $key => $contract) {
                $contract->setContact($target);
                $target->getContract()->add($contract);
                $source->getContract()->remove($key);
            }

            // Transfer contract (no matching)
            foreach ($source->getContractVersion() as $key => $contractVersion) {
                $contractVersion->setContact($target);
                $target->getContractVersion()->add($contractVersion);
                $source->getContractVersion()->remove($key);
            }

            // Transfer nda (no matching)
            foreach ($source->getNda() as $key => $nda) {
                $nda->setContact($target);
                $target->getNda()->add($nda);
                $source->getNda()->remove($key);
            }

            // Transfer nda approver (no matching)
            foreach ($source->getNdaApprover() as $key => $ndaApprover) {
                $ndaApprover->setContact($target);
                $target->getNdaApprover()->add($ndaApprover);
                $source->getNdaApprover()->remove($key);
            }

            // Transfer roadmap log (no matching)
            foreach ($source->getRoadmapLog() as $key => $log) {
                $log->setContact($target);
                $target->getRoadmapLog()->add($log);
                $source->getRoadmapLog()->remove($key);
            }

            // Transfer program doa (no matching)
            foreach ($source->getProgramDoa() as $key => $doa) {
                $doa->setContact($target);
                $target->getProgramDoa()->add($doa);
                $source->getProgramDoa()->remove($key);
            }

            // Transfer parent doa (no matching)
            foreach ($source->getParentDoa() as $key => $doa) {
                $doa->setContact($target);
                $target->getParentDoa()->add($doa);
                $source->getParentDoa()->remove($key);
            }

            // Transfer open ID (with matching)
            $targetOpenIds = [];
            /** @var OpenId $openIdTarget */
            foreach ($target->getOpenId() as $openIdTarget) {
                $targetOpenIds[] = $openIdTarget->getIdentity();
            }
            /** @var OpenId $openIdSource */
            foreach ($source->getOpenId() as $openIdSource) {
                if (!\in_array($openIdSource->getIdentity(), $targetOpenIds)) {
                    $openIdSource->setContact($target);
                    $target->getOpenId()->add($openIdSource);
                }
            }

            // Transfer contact organisation
            if ($target->getContactOrganisation() === null) {
                $contactorganisation = $source->getContactOrganisation();
                $contactorganisation->setContact($target);
                $target->setContactOrganisation($contactorganisation);
            }
            $source->setContactOrganisation(null);

            // Transfer domain (many-to-many, with matching)
            $targetDomains = [];
            /** @var Domain $domainTarget */
            foreach ($target->getDomain() as $domainTarget) {
                $targetDomains[] = $domainTarget->getId();
            }
            /** @var Domain $domainSource */
            foreach ($source->getDomain() as $domainSource) {
                if (!\in_array($domainSource->getId(), $targetDomains)) {
                    $target->getDomain()->add($domainSource);
                }
            }
            $source->setDomain(new ArrayCollection());

            // Transfer ideas (no matching)
            foreach ($source->getIdea() as $key => $idea) {
                $idea->setContact($target);
                $target->getIdea()->add($idea);
                $source->getIdea()->remove($key);
            }

            // Transfer favourite ideas (many-to-many, with matching)
            $targetIdeas = [];
            /** @var Idea $ideaTarget */
            foreach ($target->getFavouriteIdea() as $ideaTarget) {
                $targetIdeas[$ideaTarget->getId()] = $ideaTarget->getId();
            }
            /** @var Idea $ideaSource */
            foreach ($source->getFavouriteIdea() as $ideaSource) {
                if (!isset($targetIdeas[$ideaSource->getId()])) {
                    $target->getFavouriteIdea()->add($ideaSource);
                }
            }
            $source->setFavouriteIdea(new ArrayCollection());

            // Transfer technologies (many-to-many, with matching)
            $targetTechnologies = [];
            /** @var Technology $technologyTarget */
            foreach ($target->getTechnology() as $technologyTarget) {
                $targetTechnologies[$technologyTarget->getId()] = $technologyTarget->getId();
            }
            /** @var Technology $technologySource */
            foreach ($source->getTechnology() as $technologySource) {
                if (!isset($targetTechnologies[$technologySource->getId()])) {
                    $target->getTechnology()->add($technologySource);
                }
            }
            $source->setTechnology(new ArrayCollection());

            // Transfer organisation logs (no matching)
            foreach ($source->getOrganisationLog() as $key => $log) {
                $log->setContact($target);
                $target->getOrganisationLog()->add($log);
                $source->getOrganisationLog()->remove($key);
            }

            // Transfer idea partners (no matching)
            foreach ($source->getIdeaPartner() as $key => $partner) {
                $partner->setContact($target);
                $target->getIdeaPartner()->add($partner);
                $source->getIdeaPartner()->remove($key);
            }

            // Transfer affiliations (no matching)
            foreach ($source->getAffiliation() as $key => $affiliation) {
                $affiliation->setContact($target);
                $target->getAffiliation()->add($affiliation);
                $source->getAffiliation()->remove($key);
            }

            // Transfer affiliations log (no matching)
            foreach ($source->getAffiliationLog() as $key => $affiliationLog) {
                $affiliationLog->setContact($target);
                $target->getAffiliationLog()->add($affiliationLog);
                $source->getAffiliationLog()->remove($key);
            }

            // Transfer financial data (no matching)
            foreach ($source->getFinancial() as $key => $financial) {
                $financial->setContact($target);
                $target->getFinancial()->add($financial);
                $source->getFinancial()->remove($key);
            }

            // Transfer affiliation descriptions (no matching)
            foreach ($source->getAffiliationDescription() as $key => $affiliationDescription) {
                $affiliationDescription->setContact($target);
                $target->getAffiliationDescription()->add($affiliationDescription);
                $source->getAffiliationDescription()->remove($key);
            }

            // Transfer affiliation versions (no matching)
            foreach ($source->getAffiliationVersion() as $key => $affiliationVersion) {
                $affiliationVersion->setContact($target);
                $target->getAffiliationVersion()->add($affiliationVersion);
                $source->getAffiliationVersion()->remove($key);
            }

            // Transfer invoices (no matching)
            foreach ($source->getInvoice() as $key => $invoice) {
                $invoice->setContact($target);
                $target->getInvoice()->add($invoice);
                $source->getInvoice()->remove($key);
            }

            // Transfer parents (no matching)
            foreach ($source->getParent() as $key => $parent) {
                $parent->setContact($target);
                $target->getParent()->add($parent);
                $source->getParent()->remove($key);
            }

            // Transfer parent financial data (no matching)
            foreach ($source->getParentFinancial() as $key => $parentFinancial) {
                $parentFinancial->setContact($target);
                $target->getParentFinancial()->add($parentFinancial);
                $source->getParentFinancial()->remove($key);
            }

            // Transfer parent organisations (no matching)
            foreach ($source->getParentOrganisation() as $key => $parentOrganisation) {
                $parentOrganisation->setContact($target);
                $target->getParentOrganisation()->add($parentOrganisation);
                $source->getParentOrganisation()->remove($key);
            }

            // Transfer publications (no matching)
            foreach ($source->getPublication() as $key => $publication) {
                $publication->setContact($target);
                $target->getPublication()->add($publication);
                $source->getPublication()->remove($key);
            }

            // Transfer publication downloads (no matching)
            foreach ($source->getPublicationDownload() as $key => $publicationDownload) {
                $publicationDownload->setContact($target);
                $target->getPublicationDownload()->add($publicationDownload);
                $source->getPublicationDownload()->remove($key);
            }

            // Transfer photos (no matching)
            foreach ($source->getPhoto() as $key => $photo) {
                $photo->setContact($target);
                $target->getPhoto()->add($photo);
                $source->getPhoto()->remove($key);
            }

            // Transfer associates (many-to-many, with matching)
            $targetAssociates = [];
            /** @var Affiliation $associateTarget */
            foreach ($target->getAssociate() as $affiliationTarget) {
                $targetAssociates[$affiliationTarget->getId()] = $affiliationTarget->getId();
            }
            /** @var Affiliation $affiliationSource */
            foreach ($source->getAssociate() as $affiliationSource) {
                if (!isset($targetAssociates[$affiliationSource->getId()])) {
                    $target->getAssociate()->add($affiliationSource);
                }
            }
            $source->setAssociate(new ArrayCollection());

            // Transfer funder (one-to-one)
            if (($target->getFunder() === null) && ($source->getFunder() !== null)) {
                $funder = $source->getFunder();
                $funder->setContact($target);
                $target->setFunder($funder);
            }
            $source->setFunder(null);

            // Transfer deeplink contact (no matching)
            foreach ($source->getDeeplinkContact() as $key => $deeplinkContact) {
                $deeplinkContact->setContact($target);
                $target->getDeeplinkContact()->add($deeplinkContact);
                $source->getDeeplinkContact()->remove($key);
            }

            // Transfer profile (one-to-one)
            if (($target->getProfile() === null) && ($source->getProfile() !== null)) {
                $profile = $source->getProfile();
                $profile->setContact($target);
                $target->setProfile($profile);
            }
            $source->setProfile(null);

            // Transfer community (no matching)
            foreach ($source->getCommunity() as $key => $community) {
                $community->setContact($target);
                $target->getCommunity()->add($community);
                $source->getCommunity()->remove($key);
            }

            // Transfer registrations (no matching)
            foreach ($source->getRegistration() as $key => $registration) {
                $registration->setContact($target);
                $target->getRegistration()->add($registration);
                $source->getRegistration()->remove($key);
            }

            // Transfer badges (no matching)
            foreach ($source->getBadge() as $key => $badge) {
                $badge->setContact($target);
                $target->getBadge()->add($badge);
                $source->getBadge()->remove($key);
            }

            // Transfer badge contacts (no matching)
            foreach ($source->getBadgeContact() as $key => $badgeContact) {
                $badgeContact->setContact($target);
                $target->getBadgeContact()->add($badgeContact);
                $source->getBadgeContact()->remove($key);
            }

            // Transfer booth contacts (no matching)
            foreach ($source->getBoothContact() as $key => $boothContact) {
                $boothContact->setContact($target);
                $target->getBoothContact()->add($boothContact);
                $source->getBoothContact()->remove($key);
            }

            // Transfer project booths (no matching)
            foreach ($source->getProjectBooth() as $key => $projectBooth) {
                $projectBooth->setContact($target);
                $target->getProjectBooth()->add($projectBooth);
                $source->getProjectBooth()->remove($key);
            }

            // Transfer organisation booths (no matching)
            foreach ($source->getOrganisationBooth() as $key => $organisationBooth) {
                $organisationBooth->setContact($target);
                $target->getOrganisationBooth()->add($organisationBooth);
                $source->getOrganisationBooth()->remove($key);
            }

            // Transfer booth financial (no matching)
            foreach ($source->getBoothFinancial() as $key => $boothFinancial) {
                $boothFinancial->setContact($target);
                $target->getBoothFinancial()->add($boothFinancial);
                $source->getBoothFinancial()->remove($key);
            }

            // Transfer notes (no matching)
            foreach ($source->getNote() as $key => $note) {
                $note->setContact($target);
                $target->getNote()->add($note);
                $source->getNote()->remove($key);
            }

            // Transfer selections (no matching)
            foreach ($source->getSelection() as $key => $selection) {
                $selection->setContact($target);
                $target->getSelection()->add($selection);
                $source->getSelection()->remove($key);
            }

            // Transfer selection contacts (no matching)
            foreach ($source->getSelectionContact() as $key => $selectionContact) {
                $selectionContact->setContact($target);
                $target->getSelectionContact()->add($selectionContact);
                $source->getSelectionContact()->remove($key);
            }

            // Transfer mailing contacts (no matching)
            foreach ($source->getMailingContact() as $key => $mailingContact) {
                $mailingContact->setContact($target);
                $target->getMailingContact()->add($mailingContact);
                $source->getMailingContact()->remove($key);
            }

            // Transfer mailing contacts (no matching)
            foreach ($source->getMailing() as $key => $mailing) {
                $mailing->setContact($target);
                $target->getMailing()->add($mailing);
                $source->getMailing()->remove($key);
            }

            // Transfer email messages (no matching)
            foreach ($source->getEmailMessage() as $key => $emailMessage) {
                $emailMessage->setContact($target);
                $target->getEmailMessage()->add($emailMessage);
                $source->getEmailMessage()->remove($key);
            }

            // Transfer results (no matching)
            foreach ($source->getResult() as $key => $result) {
                $result->setContact($target);
                $target->getResult()->add($result);
                $source->getResult()->remove($key);
            }

            // Transfer workpackages (no matching)
            foreach ($source->getWorkpackage() as $key => $workpackage) {
                $workpackage->setContact($target);
                $target->getWorkpackage()->add($workpackage);
                $source->getWorkpackage()->remove($key);
            }

            // Transfer workpackage documents (no matching)
            foreach ($source->getWorkpackageDocument() as $key => $workpackageDocument) {
                $workpackageDocument->setContact($target);
                $target->getWorkpackageDocument()->add($workpackageDocument);
                $source->getWorkpackageDocument()->remove($key);
            }

            // Transfer idea messages (no matching)
            foreach ($source->getIdeaMessage() as $key => $ideaMessage) {
                $ideaMessage->setContact($target);
                $target->getIdeaMessage()->add($ideaMessage);
                $source->getIdeaMessage()->remove($key);
            }

            // Transfer evaluations (no matching)
            foreach ($source->getEvaluation() as $key => $evaluation) {
                $evaluation->setContact($target);
                $target->getEvaluation()->add($evaluation);
                $source->getEvaluation()->remove($key);
            }

            // Transfer calendars (no matching)
            foreach ($source->getCalendar() as $key => $calendar) {
                $calendar->setContact($target);
                $target->getCalendar()->add($calendar);
                $source->getCalendar()->remove($key);
            }

            // Transfer calendar contacts (no matching)
            foreach ($source->getCalendarContact() as $key => $calendarContact) {
                $calendarContact->setContact($target);
                $target->getCalendarContact()->add($calendarContact);
                $source->getCalendarContact()->remove($key);
            }

            // Transfer calendar documents (no matching)
            foreach ($source->getCalendarDocument() as $key => $calendarDocument) {
                $calendarDocument->setContact($target);
                $target->getCalendarDocument()->add($calendarDocument);
                $source->getCalendarDocument()->remove($key);
            }

            // Transfer schedule contacts (no matching)
            foreach ($source->getScheduleContact() as $key => $scheduleContact) {
                $scheduleContact->setContact($target);
                $target->getScheduleContact()->add($scheduleContact);
                $source->getScheduleContact()->remove($key);
            }

            // Transfer project reviewers (no matching)
            foreach ($source->getProjectReview() as $key => $projectReview) {
                $projectReview->setContact($target);
                $target->getProjectReview()->add($projectReview);
                $source->getProjectReview()->remove($key);
            }

            // Transfer review contact (one-to-one)
            if (($target->getProjectReviewContact() === null) && ($source->getProjectReviewContact() !== null)) {
                $reviewContact = $source->getProjectReviewContact();
                $reviewContact->setContact($target);
                $target->setProjectReviewContact($reviewContact);
            }
            $source->setProjectReviewContact(null);

            // Transfer project version reviewers (no matching)
            foreach ($source->getProjectVersionReview() as $key => $projectVersionReview) {
                $projectVersionReview->setContact($target);
                $target->getProjectVersionReview()->add($projectVersionReview);
                $source->getProjectVersionReview()->remove($key);
            }

            // Transfer project reports (no matching)
            foreach ($source->getProjectReport() as $key => $projectReport) {
                $projectReport->setContact($target);
                $target->getProjectReport()->add($projectReport);
                $source->getProjectReport()->remove($key);
            }

            // Transfer project calendar reviewers (no matching)
            foreach ($source->getProjectCalendarReview() as $key => $projectCalendarReview) {
                $projectCalendarReview->setContact($target);
                $target->getProjectCalendarReview()->add($projectCalendarReview);
                $source->getProjectCalendarReview()->remove($key);
            }

            // Transfer project report reviewers (no matching)
            foreach ($source->getProjectReportReview() as $key => $projectReportReview) {
                $projectReportReview->setContact($target);
                $target->getProjectReportReview()->add($projectReportReview);
                $source->getProjectReportReview()->remove($key);
            }

            // Transfer project invites (no matching)
            foreach ($source->getInvite() as $key => $invite) {
                $invite->setContact($target);
                $target->getInvite()->add($invite);
                $source->getInvite()->remove($key);
            }

            // Transfer pca (no matching)
//            foreach ($source->getPca() as $key => $pca) {
//                $pca->setContact($target);
//                $target->getPca()->add($pca);
//                $source->getPca()->remove($key);
//            }

            // Transfer invite contacts (many-to-many, with matching)
            $targetInviteContacts = [];
            /** @var Invite $inviteContactTarget */
            foreach ($target->getInviteContact() as $inviteContactTarget) {
                $targetInviteContacts[] = $inviteContactTarget->getId();
            }
            /** @var Invite $inviteContactSource */
            foreach ($source->getInviteContact() as $inviteContactSource) {
                if (!\in_array($inviteContactSource->getId(), $targetInviteContacts)) {
                    $target->getInviteContact()->add($inviteContactSource);
                }
            }
            $source->setInviteContact(new ArrayCollection());

            // Transfer idea invites (no matching)
            foreach ($source->getIdeaInvite() as $key => $ideaInvite) {
                $ideaInvite->setContact($target);
                $target->getIdeaInvite()->add($ideaInvite);
                $source->getIdeaInvite()->remove($key);
            }

            // Transfer idea messageboard (no matching)
            foreach ($source->getIdeaMessageBoard() as $key => $ideaMessageboard) {
                $ideaMessageboard->setContact($target);
                $target->getIdeaMessageBoard()->add($ideaMessageboard);
                $source->getIdeaMessageBoard()->remove($key);
            }

            // Transfer idea invite contacts (many-to-many, with matching)
            $targetIdeaInviteContacts = [];
            /** @var Invite $ideaInviteContactTarget */
            foreach ($target->getIdeaInviteContact() as $ideaInviteContactTarget) {
                $targetIdeaInviteContacts[] = $ideaInviteContactTarget->getId();
            }
            /** @var Invite $inviteContactSource */
            foreach ($source->getIdeaInviteContact() as $ideaInviteContactSource) {
                if (!\in_array($ideaInviteContactSource->getId(), $targetIdeaInviteContacts)) {
                    $target->getIdeaInviteContact()->add($ideaInviteContactSource);
                }
            }
            $source->setIdeaInviteContact(new ArrayCollection());

            // Transfer loi (no matching)
            foreach ($source->getLoi() as $key => $loi) {
                $loi->setContact($target);
                $target->getLoi()->add($loi);
                $source->getLoi()->remove($key);
            }

            // Transfer approved loi (no matching)
            foreach ($source->getLoiApprover() as $key => $loiApprover) {
                $loiApprover->setContact($target);
                $target->getLoiApprover()->add($loiApprover);
                $source->getLoiApprover()->remove($key);
            }

            // Transfer affiliation doa (no matching)
            foreach ($source->getAffiliationDoa() as $key => $affiliationDoa) {
                $affiliationDoa->setContact($target);
                $target->getAffiliationDoa()->add($affiliationDoa);
                $source->getAffiliationDoa()->remove($key);
            }

            // Transfer permits (no matching)
            foreach ($source->getPermitContact() as $key => $permitContact) {
                $permitContact->setContact($target);
                $target->getPermitContact()->add($permitContact);
                $source->getPermitContact()->remove($key);
            }

            // Transfer sessions (no matching)
            foreach ($source->getSession() as $key => $session) {
                $session->setContact($target);
                $target->getSession()->add($session);
                $source->getSession()->remove($key);
            }

            // Transfer voters (many-to-many, with matching)
            $targetVoters = [];
            /** @var Invite $ideaInviteContactTarget */
            foreach ($target->getVoter() as $voterTarget) {
                $targetVoters[] = $voterTarget->getId();
            }
            /** @var Invite $inviteContactSource */
            foreach ($source->getVoter() as $voterSource) {
                if (!\in_array($voterSource->getId(), $targetVoters)) {
                    $target->getVoter()->add($voterSource);
                }
            }
            $source->setVoter(new ArrayCollection());

            // Transfer tours (no matching)
            foreach ($source->getTour() as $key => $tour) {
                $tour->setContact($target);
                $target->getTour()->add($tour);
                $source->getTour()->remove($key);
            }

            // Transfer tour contacts (many-to-many, with matching)
            $targetTourContacts = [];
            /** @var Tour $tourContactTarget */
            foreach ($target->getTourContact() as $tourContactTarget) {
                $targetTourContacts[] = $tourContactTarget->getId();
            }
            /** @var Invite $inviteContactSource */
            foreach ($source->getTourContact() as $tourContactSource) {
                if (!\in_array($tourContactSource->getId(), $targetTourContacts)) {
                    $target->getTourContact()->add($tourContactSource);
                }
            }
            $source->setTourContact(new ArrayCollection());


            // Save main contact, remove the other + flush and update permissions
            $this->entityManager->remove($source);
            $this->entityManager->flush();

            // Prepare for logging
            $message = sprintf(
                'Merged contact %s (%d) into %s (%d)',
                $source->parseFullName(),
                $source->getId(),
                $target->parseFullName(),
                $target->getId()
            );
            /** @var ContactAbstractController $controller */
            $controller = $this->getController();
            $contact = $controller->zfcUserAuthentication()->getIdentity();

            // Log the merge in the target organisation
            $contactLog = new Log();
            $contactLog->setContact($target);
            $contactLog->setCreatedBy($contact);
            $contactLog->setLog($message);
            $this->entityManager->persist($contactLog);
            // Add a note to the target contact about the merge
            $contactNote = new Note();
            $contactNote->setContact($target);
            $contactNote->setSource('merge');
            $contactNote->setNote($message);
            $notes = $target->getNote()->toArray();
            array_unshift($notes, $contactNote);
            $target->setNote(new ArrayCollection($notes));
            $this->entityManager->persist($contactNote);

            $this->entityManager->flush();
        } catch (ORMException $exception) {
            $response = ['success' => false, 'errorMessage' => $exception->getMessage()];
            if ($logger instanceof LoggerInterface) {
                $logger->err(sprintf(
                    '%s: %d %s',
                    $exception->getFile(),
                    $exception->getLine(),
                    $exception->getMessage()
                ));
            }
        }

        return $response;
    }
}
