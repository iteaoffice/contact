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
use Program\Entity\Domain;
use Program\Entity\Technology;
use Project\Entity\Idea\Idea;
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
            if (is_null($target->getFirstName())) {
                $target->setFirstName($source->getFirstName());
            }
            if (is_null($target->getMiddleName())) {
                $target->setMiddleName($source->getMiddleName());
            }
            if (is_null($target->getLastName())) {
                $target->setLastName($source->getLastName());
            }
            if (is_null($target->getEmail())) {
                $target->setEmail($source->getEmail());
            }
            if (is_null($target->getGender())) {
                $target->setGender($source->getGender());
            }
            if (is_null($target->getTitle())) {
                $target->setTitle($source->getTitle());
            }
            if (is_null($target->getPosition())) {
                $target->setPosition($source->getPosition());
            }
            if (is_null($target->getDepartment())) {
                $target->setDepartment($source->getDepartment());
            }
            if (is_null($target->getDateOfBirth())) {
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
                if (!in_array($emailSource->getEmail(), $targetEmailAddresses)) {
                    $emailSource->setContact($target);
                    $target->getEmailAddress()->add($emailSource);
                }
            }

            // Transfer CV
            if (!is_null($source->getCv()) && is_null($target->getCv())) {
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
                if (!in_array($optInSource->getId(), $targetOptIns)) {
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
                if (!in_array($openIdSource->getIdentity(), $targetOpenIds)) {
                    $openIdSource->setContact($target);
                    $target->getOpenId()->add($openIdSource);
                }
            }

            // Transfer contact organisation
            if (is_null($target->getContactOrganisation())) {
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
                if (!in_array($domainSource->getId(), $targetDomains)) {
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
            if (is_null($target->getFunder()) && !is_null($source->getFunder())) {
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


//            // Transfer log
//            foreach ($source->getLog() as $key => $log) {
//                $log->setOrganisation($target);
//                $this->persist($log);
//                $target->getLog()->add($log);
//                $source->getLog()->remove($key);
//            }
//
//            // Transfer technology (many-to-many)
//            foreach ($source->getTechnology() as $key => $technology) {
//                $technology->getOrganisation()->removeElement($source);
//                $technology->getOrganisation()->add($target);
//                $this->persist($technology);
//                $target->getTechnology()->add($technology);
//                $source->getTechnology()->remove($key);
//            }
//
//            // Transfer websites
//            foreach ($source->getWeb() as $key => $website) {
//                $website->setOrganisation($target);
//                $this->persist($website);
//                $target->getWeb()->add($website);
//                $source->getWeb()->remove($key);
//            }
//
//            // Transfer notes
//            foreach ($source->getNote() as $key => $note) {
//                $note->setOrganisation($target);
//                $this->persist($note);
//                $target->getNote()->add($note);
//                $source->getNote()->remove($key);
//            }
//
//            // Transfer names
//            foreach ($source->getNames() as $key => $name) {
//                $name->setOrganisation($target);
//                $this->persist($name);
//                $target->getNames()->add($name);
//                $source->getNames()->remove($key);
//            }
//
//            // Transfer affiliations
//            foreach ($source->getAffiliation() as $key => $affiliation) {
//                $affiliation->setOrganisation($target);
//                $this->persist($affiliation);
//                $target->getAffiliation()->add($affiliation);
//                $source->getAffiliation()->remove($key);
//            }
//
//            // Transfer affiliation financial data
//            foreach ($source->getAffiliationFinancial() as $key => $affiliationFinancial) {
//                $affiliationFinancial->setOrganisation($target);
//                $this->persist($affiliationFinancial);
//                $target->getAffiliationFinancial()->add($affiliationFinancial);
//                $source->getAffiliationFinancial()->remove($key);
//            }
//
//            // Transfer ICT organisations
//            foreach ($source->getIctOrganisation() as $key => $ictOrganisation) {
//                $ictOrganisation->setOrganisation($target);
//                $this->persist($ictOrganisation);
//                $target->getIctOrganisation()->add($ictOrganisation);
//                $source->getIctOrganisation()->remove($key);
//            }
//
//            // Transfer cluster head
//            foreach ($source->getCluster() as $key => $cluster) {
//                $cluster->setOrganisation($target);
//                $this->persist($cluster);
//                $target->getCluster()->add($cluster);
//                $source->getCluster()->remove($key);
//            }
//
//            // Transfer cluster memberships
//            foreach ($source->getClusterMember() as $key => $clusterMember) {
//                $clusterMember->setOrganisation($target);
//                $this->persist($clusterMember);
//                $target->getClusterMember()->add($clusterMember);
//                $source->getClusterMember()->remove($key);
//            }
//
//            // Transfer contacts
//            foreach ($source->getContactOrganisation() as $key => $contactOrganisation) {
//                $contactOrganisation->setOrganisation($target);
//                $this->persist($contactOrganisation);
//                $target->getContactOrganisation()->add($contactOrganisation);
//                $source->getContactOrganisation()->remove($key);
//            }
//
//            // Transfer booths
//            foreach ($source->getOrganisationBooth() as $key => $organisationBooth) {
//                $organisationBooth->setOrganisation($target);
//                $this->persist($organisationBooth);
//                $target->getOrganisationBooth()->add($organisationBooth);
//                $source->getOrganisationBooth()->remove($key);
//            }
//
//            // Transfer booth financial data
//            foreach ($source->getBoothFinancial() as $key => $boothFinancial) {
//                $boothFinancial->setOrganisation($target);
//                $this->persist($boothFinancial);
//                $target->getBoothFinancial()->add($boothFinancial);
//                $source->getBoothFinancial()->remove($key);
//            }
//
//            // Transfer idea partners
//            foreach ($source->getIdeaPartner() as $key => $ideaPartner) {
//                $ideaPartner->setOrganisation($target);
//                $this->persist($ideaPartner);
//                $target->getIdeaPartner()->add($ideaPartner);
//                $source->getIdeaPartner()->remove($key);
//            }
//
//            // Transfer invoices
//            foreach ($source->getInvoice() as $key => $invoice) {
//                $invoice->setOrganisation($target);
//                $this->persist($invoice);
//                $target->getInvoice()->add($invoice);
//                $source->getInvoice()->remove($key);
//            }
//
//            // Transfer invoice journal
//            foreach ($source->getJournal() as $key => $journal) {
//                $journal->setOrganisation($target);
//                $this->persist($journal);
//                $target->getJournal()->add($journal);
//                $source->getJournal()->remove($key);
//            }
//
//            // Transfer program doa
//            foreach ($source->getProgramDoa() as $key => $programDoa) {
//                $programDoa->setOrganisation($target);
//                $this->persist($programDoa);
//                $target->getProgramDoa()->add($programDoa);
//                $source->getProgramDoa()->remove($key);
//            }
//
//            // Transfer program call doa
//            foreach ($source->getDoa() as $key => $callDoa) {
//                $callDoa->setOrganisation($target);
//                $this->persist($callDoa);
//                $target->getDoa()->add($callDoa);
//                $source->getDoa()->remove($key);
//            }
//
//            // Transfer reminders
//            foreach ($source->getReminder() as $key => $reminder) {
//                $reminder->setOrganisation($target);
//                $this->persist($reminder);
//                $target->getReminder()->add($reminder);
//                $source->getReminder()->remove($key);
//            }
//
//            // Transfer results (many-to-many)
//            foreach ($source->getResult() as $key => $result) {
//                $result->getOrganisation()->removeElement($source);
//                $result->getOrganisation()->add($target);
//                $this->persist($result);
//                $target->getResult()->add($result);
//                $source->getResult()->remove($key);
//            }
//
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
