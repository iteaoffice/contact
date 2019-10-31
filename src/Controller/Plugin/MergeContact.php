<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Affiliation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/affiliation for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Affiliation\Entity\Affiliation;
use Affiliation\Entity\Loi;
use Contact\Controller\ContactAbstractController;
use Contact\Entity\Contact;
use Contact\Entity\Email;
use Contact\Entity\Log;
use Contact\Entity\Note;
use Contact\Entity\OptIn;
use Contact\Entity\Photo;
use Contact\Entity\SelectionContact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ErrorHeroModule\Handler\Logging;
use Event\Entity\Exhibition\Tour;
use Exception;
use Program\Entity\Domain;
use Program\Entity\Nda;
use Program\Entity\Technology;
use Project\Entity\Idea\Idea;
use Project\Entity\Invite;
use Zend\Http\PhpEnvironment\Request;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use function array_unshift;
use function in_array;
use function sprintf;

/**
 * Class MergeContact
 *
 * @package Contact\Controller\Plugin
 */
final class MergeContact extends AbstractPlugin
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
     * @var Logging
     */
    private $errorLogger;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Logging $errorLogger = null
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->errorLogger = $errorLogger;
    }

    public function __invoke(): MergeContact
    {
        return $this;
    }

    public function checkMerge(Contact $source, Contact $target): array
    {
        $errors = [];

        // Can't merge the same contact
        if ($source->getId() === $target->getId()) {
            $errors[] = $this->translator->translate('txt-cant-merge-the-same-contact');
        }

        return $errors;
    }

    public function merge(Contact $source, Contact $target): array
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
                if (!in_array($emailSource->getEmail(), $targetEmailAddresses, false)) {
                    $emailSource->setContact($target);
                    $target->getEmailAddress()->add($emailSource);
                }
            }

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

            // Transfer opt-in (many-to-many, with matching)
            $targetOptIns = [];
            /** @var OptIn $optInTarget */
            foreach ($target->getOptIn() as $optInTarget) {
                $targetOptIns[] = $optInTarget->getId();
            }
            /** @var OptIn $optInSource */
            foreach ($source->getOptIn() as $optInSource) {
                if (!in_array($optInSource->getId(), $targetOptIns, false)) {
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

            // Transfer nda approver (no matching). Do this before the real NDA otherwise the
            //NDA will not be found
            foreach ($source->getNdaApprover() as $key => $ndaApprover) {
                $ndaApprover->setContact($target);
                $target->getNdaApprover()->add($ndaApprover);
                $source->getNdaApprover()->remove($key);
            }

            // Transfer nda (no matching)
            /**
             * @var int $key
             * @var Nda $nda
             */
            foreach ($source->getNda() as $key => $nda) {
                $nda->setContact($target);

                //When the NDA is self-approved, already switch the target here
                if ($nda->getApprover() === $source) {
                    $nda->setApprover($target);
                }

                $target->getNda()->add($nda);
                $source->getNda()->remove($key);
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

            // Transfer contact organisation
            if ($target->getContactOrganisation() === null) {
                $contactOrganisation = $source->getContactOrganisation();
                $contactOrganisation->setContact($target);
                $target->setContactOrganisation($contactOrganisation);
            }
            $source->setContactOrganisation(null);


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

            // Transfer photo (database unique constraint on 1 photo per contact!)
            if (!$target->hasPhoto() && $source->hasPhoto()) {
                $photo = $source->getPhoto()->first();
                $photo->setContact($target);
                $target->getPhoto()->add($photo);
                $source->setPhoto(new ArrayCollection());
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
            if ($target->getFunder() === null && $source->getFunder() !== null) {
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
            if ($target->getProfile() === null && $source->getProfile() !== null) {
                $profile = $source->getProfile();
                $profile->setContact($target);
                $target->setProfile($profile);
            }
            $source->setProfile(null);

            // Transfer photo (many-to-one)
            if ($target->getPhoto()->isEmpty() === null && !$source->getPhoto()->isEmpty()) {
                /** @var Photo $photo */
                $photo = $source->getPhoto()->first();
                $photo->setContact($target);
                $target->getPhoto()->add($photo);
            }
            $source->setPhoto([]);

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

            // Transfer badge contacts (one-to-many, with matching)
            $targetBadges = [];
            /** @var \Event\Entity\Badge\Contact $badgeContactTarget */
            foreach ($target->getBadgeContact() as $badgeContactTarget) {
                $targetBadges[] = $badgeContactTarget->getBadge()->getId();
            }
            /** @var \Event\Entity\Badge\Contact $badgeContactSource */
            foreach ($source->getBadgeContact() as $badgeContactSource) {
                if (!in_array($badgeContactSource->getBadge()->getId(), $targetBadges, true)) {
                    $badgeContactSource->setContact($target);
                    $target->getBadgeContact()->add($badgeContactSource);
                }
            }
            $source->setBadgeContact(new ArrayCollection());

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

            // Transfer selection contacts (one-to-many, with matching)
            $targetSelections = [];
            /** @var SelectionContact $selectionContactTarget */
            foreach ($target->getSelectionContact() as $selectionContactTarget) {
                $targetSelections[] = $selectionContactTarget->getSelection()->getId();
            }
            /** @var SelectionContact $selectionContactSource */
            foreach ($source->getSelectionContact() as $key => $selectionContactSource) {
                if (!in_array($selectionContactSource->getSelection()->getId(), $targetSelections, false)) {
                    $selectionContactSource->setContact($target);
                    $target->getSelectionContact()->add($selectionContactSource);
                }

                // Explicitly remove the key
                $source->getSelectionContact()->remove($key);
            }
            $source->setSelectionContact(new ArrayCollection());

            // Transfer mailing contacts (one-to-many, with matching)
            $targetMailings = [];
            /** @var \Mailing\Entity\Contact $mailingContactTarget */
            foreach ($target->getMailingContact() as $mailingContactTarget) {
                $targetMailings[] = $mailingContactTarget->getMailing()->getId();
            }
            /** @var \Mailing\Entity\Contact $mailingContactSource */
            foreach ($source->getMailingContact() as $key => $mailingContactSource) {
                if (!in_array($mailingContactSource->getMailing()->getId(), $targetMailings, false)) {
                    $mailingContactSource->setContact($target);
                    $target->getMailingContact()->add($mailingContactSource);
                }

                // Explicitly remove the key
                $source->getMailingContact()->remove($key);
            }

            // Transfer mailings (no matching)
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

            //Transfer actions (closed statements)
            foreach ($source->getActionClosed() as $key => $actionClosed) {
                $actionClosed->setContactClosed($target);
                $target->getActionClosed()->add($actionClosed);
                $source->getActionClosed()->remove($key);
            }

            //Transfer actions (status updates)
            foreach ($source->getActionStatus() as $key => $actionStatus) {
                $actionStatus->setContactStatus($target);
                $target->getActionStatus()->add($actionStatus);
                $source->getActionStatus()->remove($key);
            }

            //Transfer actions (closed comments)
            foreach ($source->getActionComment() as $key => $actionComment) {
                $actionComment->setContact($target);
                $target->getActionComment()->add($actionComment);
                $source->getActionComment()->remove($key);
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

            // Transfer project reviewers (no matching)
            foreach ($source->getProjectReviewers() as $key => $projectReviewer) {
                $projectReviewer->setContact($target);
                $target->getProjectReviewers()->add($projectReviewer);
                $source->getProjectReviewers()->remove($key);
            }

            // Transfer review contact (one-to-one)
            if (($target->getProjectReviewerContact() === null) && ($source->getProjectReviewerContact() !== null)) {
                $reviewContact = $source->getProjectReviewerContact();
                $reviewContact->setContact($target);
                $target->setProjectReviewerContact($reviewContact);
            }
            $source->setProjectReviewerContact(null);

            // Transfer project version reviewers (no matching)
            foreach ($source->getProjectVersionReviewers() as $key => $projectVersionReview) {
                $projectVersionReview->setContact($target);
                $target->getProjectVersionReviewers()->add($projectVersionReview);
                $source->getProjectVersionReviewers()->remove($key);
            }

            // Transfer project reports (no matching)
            foreach ($source->getProjectReport() as $key => $projectReport) {
                $projectReport->setContact($target);
                $target->getProjectReport()->add($projectReport);
                $source->getProjectReport()->remove($key);
            }

            // Transfer project calendar reviewers (no matching)
            foreach ($source->getProjectCalendarReviewers() as $key => $projectCalendarReviewer) {
                $projectCalendarReviewer->setContact($target);
                $target->getProjectCalendarReviewers()->add($projectCalendarReviewer);
                $source->getProjectCalendarReviewers()->remove($key);
            }

            // Transfer project report reviewers (no matching)
            foreach ($source->getProjectReportReviewers() as $key => $projectReportReviewer) {
                $projectReportReviewer->setContact($target);
                $target->getProjectReportReviewers()->add($projectReportReviewer);
                $source->getProjectReportReviewers()->remove($key);
            }

            // Transfer project invites (no matching)
            foreach ($source->getInvite() as $key => $invite) {
                $invite->setContact($target);
                $target->getInvite()->add($invite);
                $source->getInvite()->remove($key);
            }

            // Transfer pca (no matching)
            foreach ($source->getPca() as $key => $pca) {
                $pca->setContact($target);
                $target->getPca()->add($pca);
                $source->getPca()->remove($key);
            }

            // Transfer invite contacts (many-to-many, with matching)
            $targetInviteContacts = [];
            /** @var Invite $inviteContactTarget */
            foreach ($target->getInviteContact() as $inviteContactTarget) {
                $targetInviteContacts[] = $inviteContactTarget->getId();
            }
            /** @var Invite $inviteContactSource */
            foreach ($source->getInviteContact() as $inviteContactSource) {
                if (!in_array($inviteContactSource->getId(), $targetInviteContacts, false)) {
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

            // Transfer idea invite contacts (many-to-many, with matching)
            $targetIdeaInviteContacts = [];
            /** @var Invite $ideaInviteContactTarget */
            foreach ($target->getIdeaInviteContact() as $ideaInviteContactTarget) {
                $targetIdeaInviteContacts[] = $ideaInviteContactTarget->getId();
            }
            /** @var Invite $inviteContactSource */
            foreach ($source->getIdeaInviteContact() as $ideaInviteContactSource) {
                if (!in_array($ideaInviteContactSource->getId(), $targetIdeaInviteContacts, true)) {
                    $target->getIdeaInviteContact()->add($ideaInviteContactSource);
                }
            }
            $source->setIdeaInviteContact(new ArrayCollection());

            // Transfer loi (no matching)
            /**
             * @var int $key
             * @var Loi $loi
             */
            foreach ($source->getLoi() as $key => $loi) {
                $loi->setContact($target);

                //When the NDA is self-approved, already switch the target here
                if ($loi->getApprover() === $source) {
                    $loi->setApprover($target);
                }

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

            // Transfer permits, check unique index contact/role/key
            $targetPermits = [];
            $keyFormat = '%d-%d';
            foreach ($target->getPermitContact() as $targetPermitContact) {
                $key = sprintf(
                    $keyFormat,
                    $targetPermitContact->getRole()->getId(),
                    $targetPermitContact->getKeyId()
                );
                $targetPermits[$key] = true;
            }
            foreach ($source->getPermitContact() as $key => $sourcePermitContact) {
                $key = sprintf(
                    $keyFormat,
                    $sourcePermitContact->getRole()->getId(),
                    $sourcePermitContact->getKeyId()
                );
                // Prevent duplicates
                if (!isset($targetPermits[$key])) {
                    $sourcePermitContact->setContact($target);
                    $target->getPermitContact()->add($sourcePermitContact);
                }
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
                if (!in_array($voterSource->getId(), $targetVoters)) {
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
                if (!in_array($tourContactSource->getId(), $targetTourContacts, false)) {
                    $target->getTourContact()->add($tourContactSource);
                }
            }
            $source->setTourContact(new ArrayCollection());

            // Transfer doa reminder receivers (no matching)
            foreach ($source->getDoaReminderReceiver() as $key => $doaReminder) {
                $doaReminder->setReceiver($target);
                $target->getDoaReminderReceiver()->add($doaReminder);
                $source->getDoaReminderReceiver()->remove($key);
            }

            // Transfer doa reminder senders (no matching)
            foreach ($source->getDoaReminderSender() as $key => $doaReminder) {
                $doaReminder->setSender($target);
                $target->getDoaReminderSender()->add($doaReminder);
                $source->getDoaReminderSender()->remove($key);
            }

            // Transfer blogs (no matching)
            foreach ($source->getBlog() as $key => $blog) {
                $blog->setContact($target);
                $target->getBlog()->add($blog);
                $source->getBlog()->remove($key);
            }

            // Transfer blog messages (no matching)
            foreach ($source->getBlogMessage() as $key => $blogMessage) {
                $blogMessage->setContact($target);
                $target->getBlogMessage()->add($blogMessage);
                $source->getBlogMessage()->remove($key);
            }

            // Transfer journal entries (no matching)
            foreach ($source->getJournalEntry() as $key => $journalEntry) {
                $journalEntry->setContact($target);
                $target->getJournalEntry()->add($journalEntry);
                $source->getJournalEntry()->remove($key);
            }

            // Transfer invoice journals (no matching)
            foreach ($source->getJournal() as $key => $journal) {
                $journal->setContact($target);
                $target->getJournal()->add($journal);
                $source->getJournal()->remove($key);
            }

            // Transfer invoice organisation journals (no matching)
            foreach ($source->getOrganisationJournal() as $key => $organisationJournal) {
                $organisationJournal->setOrganisationContact($target);
                $target->getOrganisationJournal()->add($organisationJournal);
                $source->getOrganisationJournal()->remove($key);
            }

            // Transfer invoice logs (no matching)
            foreach ($source->getInvoiceLog() as $key => $invoiceLog) {
                $invoiceLog->setContact($target);
                $target->getInvoiceLog()->add($invoiceLog);
                $source->getInvoiceLog()->remove($key);
            }

            // Transfer invoice reminders (no matching)
            foreach ($source->getReminder() as $key => $invoiceReminder) {
                $invoiceReminder->setContact($target);
                $target->getReminder()->add($invoiceReminder);
                $source->getReminder()->remove($key);
            }

            // Transfer achievements (no matching)
            foreach ($source->getAchievement() as $key => $achievement) {
                $achievement->setContact($target);
                $target->getAchievement()->add($achievement);
                $source->getAchievement()->remove($key);
            }

            // Transfer project logs (no matching)
            foreach ($source->getProjectLog() as $key => $projectLog) {
                $projectLog->setContact($target);
                $target->getProjectLog()->add($projectLog);
                $source->getProjectLog()->remove($key);
            }

            // Transfer project change logs (no matching)
            foreach ($source->getProjectChangelog() as $key => $projectChangeLog) {
                $projectChangeLog->setContact($target);
                $target->getProjectChangelog()->add($projectChangeLog);
                $source->getProjectChangelog()->remove($key);
            }

            // Transfer change request processes (no matching)
            foreach ($source->getChangeRequestProcess() as $key => $changeRequestProcess) {
                $changeRequestProcess->setContact($target);
                $target->getChangeRequestProcess()->add($changeRequestProcess);
                $source->getChangeRequestProcess()->remove($key);
            }

            // Transfer change request cost changes (no matching)
            foreach ($source->getChangeRequestCostChange() as $key => $changeRequestCostChange) {
                $changeRequestCostChange->setContact($target);
                $target->getChangeRequestCostChange()->add($changeRequestCostChange);
                $source->getChangeRequestCostChange()->remove($key);
            }

            // Transfer change request countries (no matching)
            foreach ($source->getChangeRequestCountry() as $key => $changeRequestCountry) {
                $changeRequestCountry->setContact($target);
                $target->getChangeRequestCountry()->add($changeRequestCountry);
                $source->getChangeRequestCountry()->remove($key);
            }

            // Transfer version contacts (no matching)
            foreach ($source->getVersionContact() as $key => $versionContact) {
                $versionContact->setContact($target);
                $target->getVersionContact()->add($versionContact);
                $source->getVersionContact()->remove($key);
            }

            // Transfer workpackage contacts (no matching)
            foreach ($source->getWorkpackageContact() as $key => $workpackageContact) {
                $workpackageContact->setContact($target);
                $target->getWorkpackageContact()->add($workpackageContact);
                $source->getWorkpackageContact()->remove($key);
            }

            // Transfer log created by (no matching)
            foreach ($source->getLogCreatedBy() as $key => $logCreatedBy) {
                $logCreatedBy->setCreatedBy($target);
                $target->getLogCreatedBy()->add($logCreatedBy);
                $source->getLogCreatedBy()->remove($key);
            }

            // Transfer logs (no matching)
            foreach ($source->getLog() as $key => $log) {
                $log->setContact($target);
                $target->getLog()->add($log);
                $source->getLog()->remove($key);
            }

            // Transfer pageviews
            foreach ($source->getPageview() as $key => $pageView) {
                $pageView->setContact($target);
                $target->getPageview()->add($pageView);
                $source->getPageview()->remove($key);
            }

            // This flush removes all references to $source that have orphanRemoval=true
            $this->entityManager->flush();

            // Prepare for logging
            $message = sprintf(
                'Merged contact %s (%d) into %s (%d)',
                $source->parseFullName(),
                $source->getId(),
                $target->parseFullName(),
                $target->getId()
            );

            // Save main contact, remove the other + flush and update permissions

            $this->entityManager->remove($source);
            $this->entityManager->flush();

            /** @var ContactAbstractController $controller */
            $controller = $this->getController();
            $contact = $controller->identity();

            // Log the merge in the target contact
            $contactLog = new Log();
            $contactLog->setContact($target);
            $contactLog->setCreatedBy($contact);
            $contactLog->setLog($message);
            $target->getLog()->add($contactLog);
            $this->entityManager->persist($contactLog);

            // Add a note to the target contact about the merge
            $contactNote = new Note();
            $contactNote->setContact($target);
            $contactNote->setSource('Account merge');
            $contactNote->setNote($message);
            $notes = $target->getNote()->toArray();
            array_unshift($notes, $contactNote);
            $target->setNote(new ArrayCollection($notes));
            $this->entityManager->persist($contactNote);

            $this->entityManager->flush();
        } catch (Exception $exception) {
            $response = ['success' => false, 'errorMessage' => $exception->getMessage()];
            if ($this->errorLogger instanceof Logging) {
                $request = new Request();
                $this->errorLogger->handleErrorException($exception, $request);
            }
        }

        return $response;
    }
}
