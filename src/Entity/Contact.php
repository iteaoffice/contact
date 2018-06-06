<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\EmailMessage;
use Zend\Form\Annotation;
use Zend\Math\Rand;
use ZfcUser\Entity\UserInterface;

/**
 * Entity for the Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="Contact\Repository\Contact")
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_contact")
 *
 * @category    Contact
 * @package     Entity
 */
class Contact extends AbstractEntity implements ProviderInterface, UserInterface
{
    /**
     * Key needed for the encryption and decryption of the Keys
     */
    public const HASH_KEY = 'rdkfj43es39f9xv8s9sf9sdwer0cv';

    /**
     * @ORM\Column(name="contact_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="firstname", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-first-name-label","help-block":"txt-contact-first-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-first-name-placeholder"})
     * @var string
     */
    private $firstName;
    /**
     * @ORM\Column(name="middlename", type="string",nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-middle-name-label","help-block":"txt-contact-middle-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-middle-name-placeholder"})
     * @var string
     */
    private $middleName;
    /**
     * @ORM\Column(name="lastname", type="string", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-last-name-label","help-block":"txt-contact-last-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-last-name-placeholder"})
     * @var string
     */
    private $lastName;
    /**
     * @ORM\Column(name="email",type="string",nullable=false, unique=true)
     * @Annotation\Type("\Zend\Form\Element\Email")
     * @Annotation\Options({"label":"txt-contact-email-address-label","help-block":"txt-contact-email-address-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-email-address-placeholder"})
     * @var string
     */
    private $email;
    /**
     * @ORM\Column(name="password", type="string", nullable=true)
     * @Annotation\Exclude()
     * @deprecated
     * @var string
     */
    private $password;
    /**
     * @ORM\Column(name="salted_password", type="string", nullable=true)
     * @Annotation\Exclude()
     * @var string
     */
    private $saltedPassword;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Gender", cascade={"persist"}, inversedBy="contacts")
     * @ORM\JoinColumn(name="gender_id", referencedColumnName="gender_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Gender"})
     * @Annotation\Options({"label":"txt-contact-gender-label","help-block":"txt-contact-gender-help-block"})
     * @var \General\Entity\Gender
     */
    private $gender;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Title", cascade={"persist"}, inversedBy="contacts")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="title_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Title"})
     * @Annotation\Options({"label":"txt-contact-title-label","help-block":"txt-contact-title-help-block"})
     * @var \General\Entity\Title
     */
    private $title;
    /**
     * @ORM\Column(name="position", type="string", length=60, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-position-label","help-block":"txt-contact-position-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-position-placeholder"})
     * @var string
     */
    private $position;
    /**
     * @ORM\Column(name="department", type="string", length=80, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-department-label","help-block":"txt-contact-department-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-department-placeholder"})
     * @var string
     */
    private $department;
    /**
     * @ORM\Column(name="date_birth", type="date", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-contact-date-of-birth-label","help-block":"txt-contact-date-of-birth-help-block"})
     * @var \DateTime
     */
    private $dateOfBirth;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $lastUpdate;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="date_anonymous", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateAnonymous;
    /**
     * @ORM\Column(name="date_activated", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateActivated;
    /**
     * @ORM\Column(name="hash", type="string", nullable=true)
     * @Annotation\Exclude()
     * @var string|null
     */
    private $hash;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Access", cascade={"persist"}, inversedBy="contact", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="contact_access",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="access_id", referencedColumnName="access_id")}
     * )
     * @ORM\OrderBy({"access"="ASC"})
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({
     *      "target_class":"Admin\Entity\Access",
     *      "find_method":{
     *          "name":"findWithoutSelection",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "access":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-contact-access-label","help-block":"txt-contact-access-help-block"})
     * @var \Admin\Entity\Access[]|Collections\ArrayCollection
     */
    private $access;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Email", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Email[]|Collections\ArrayCollection
     */
    private $emailAddress;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\Cv", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\CV
     */
    private $cv;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Address", cascade={"persist"}, mappedBy="contact", orphanRemoval=true)
     * @@Annotation\ComposedObject("\Contact\Entity\Address")
     * @var \Contact\Entity\Address[]|Collections\ArrayCollection
     */
    private $address;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Phone", cascade={"persist"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\ComposedObject("\Contact\Entity\Phone")
     * @var \Contact\Entity\Phone[]|Collections\ArrayCollection
     */
    private $phone;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\OptIn", cascade={"persist"},inversedBy="contact")
     * @ORM\JoinTable(name="contact_optin",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="optin_id", referencedColumnName="optin_id")}
     * )
     * @Annotation\Exclude()
     * @var \Contact\Entity\OptIn[]|Collections\ArrayCollection
     */
    private $optIn;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Project", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]|Collections\ArrayCollection
     */
    private $project;
    /**
     * @ORM\ManyToMany(targetEntity="\Project\Entity\Project", cascade={"persist"}, mappedBy="proxyContact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]|Collections\ArrayCollection
     */
    private $proxyProject;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Rationale", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Rationale[]|Collections\ArrayCollection
     */
    private $rationale;
    /**
     * @ORM\ManyToMany(targetEntity="\Project\Entity\Description\Description", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Description\Description[]|Collections\ArrayCollection
     */
    private $projectDescription;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Version\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Version\Version[]|Collections\ArrayCollection
     */
    private $projectVersion;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Report\Item", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Report\Item[]|Collections\ArrayCollection
     */
    private $projectReportItem;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Report\WorkpackageDescription", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Report\WorkpackageDescription[]|Collections\ArrayCollection
     */
    private $projectReportWorkpackageDescription;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Report\EffortSpent", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Report\Item[]|Collections\ArrayCollection
     */
    private $projectReportEffortSpent;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Document\Document", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Document\Document[]|Collections\ArrayCollection
     */
    private $projectDocument;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Dnd", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Dnd|Collections\ArrayCollection
     */
    private $dnd;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Contract", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Contract[]|Collections\ArrayCollection
     */
    private $contract;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Contract\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Contract[]|Collections\ArrayCollection
     */
    private $contractVersion;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Nda", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Nda[]|Collections\ArrayCollection
     */
    private $nda;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Nda", cascade={"persist"}, mappedBy="approver")
     * @Annotation\Exclude()
     * @var \Program\Entity\Nda[]|Collections\ArrayCollection
     */
    private $ndaApprover;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Doa|Collections\ArrayCollection
     */
    private $programDoa;
    /**
     * @ORM\OneToMany(targetEntity="\Organisation\Entity\Parent\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Parent\Doa[]|Collections\ArrayCollection
     */
    private $parentDoa;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\ContactOrganisation", cascade={"persist","remove"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     * @var \Contact\Entity\ContactOrganisation
     */
    private $contactOrganisation;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Domain", cascade={"persist"}, inversedBy="contact")
     * @ORM\JoinTable(name="contact_domain",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="domain_id")}
     * )
     * @Annotation\Exclude()
     * @var \Program\Entity\Domain[]|Collections\ArrayCollection
     */
    private $domain;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Action", cascade={"persist"}, mappedBy="contactClosed")
     * @Annotation\Exclude()
     * @var \Project\Entity\Action[]|Collections\ArrayCollection
     *
     * This is the user (typically someone of the office who has closed the action
     */
    private $actionClosed;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Action", cascade={"persist"}, mappedBy="contactStatus")
     * @Annotation\Exclude()
     * @var \Project\Entity\Action[]|Collections\ArrayCollection
     *
     * This is the user (typically the PL) which has updated the status of the action
     */
    private $actionStatus;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Action\Comment", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Action\Comment[]|Collections\ArrayCollection
     */
    private $actionComment;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Idea[]|Collections\ArrayCollection
     */
    private $idea;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="favourite")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Idea[]|Collections\ArrayCollection
     */
    private $favouriteIdea;
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Technology", cascade={"persist"}, inversedBy="contact")
     * @ORM\JoinTable(name="contact_technology",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")}
     * )
     * @Annotation\Exclude()
     * @var \Program\Entity\Technology[]|Collections\ArrayCollection
     */
    private $technology;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Log[]|Collections\ArrayCollection
     */
    private $organisationLog;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Partner", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Partner[]|Collections\ArrayCollection
     */
    private $ideaPartner;

    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection
     */
    private $affiliation;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Log[]|Collections\ArrayCollection
     */
    private $affiliationLog;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Financial|Collections\ArrayCollection
     */
    private $financial;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Description", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Description|Collections\ArrayCollection
     */
    private $affiliationDescription;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Version[]|Collections\ArrayCollection
     */
    private $affiliationVersion;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Invoice", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Invoice\Entity\Invoice[]|Collections\ArrayCollection
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\OParent", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\OParent[]|Collections\ArrayCollection
     */
    private $parent;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude();
     * @var \Organisation\Entity\Parent\Financial[]|Collections\ArrayCollection
     */
    private $parentFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Organisation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Parent\Organisation[]|Collections\ArrayCollection
     */
    private $parentOrganisation;
    /**
     * @ORM\OneToMany(targetEntity="Publication\Entity\Publication", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Publication\Entity\Publication[]|Collections\ArrayCollection
     */
    private $publication;
    /**
     * @ORM\OneToMany(targetEntity="Publication\Entity\Download", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Publication\Entity\Download[]|Collections\ArrayCollection
     */
    private $publicationDownload;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Photo", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Photo|Collections\ArrayCollection
     */
    private $photo;
    /**
     * @ORM\ManyToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="associate")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection
     */
    private $associate;
    /**
     * @ORM\OneToOne(targetEntity="\Program\Entity\Funder", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Funder
     */
    private $funder;
    /**
     * @ORM\OneToMany(targetEntity="Deeplink\Entity\Contact", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Deeplink\Entity\Contact|Collections\ArrayCollection
     */
    private $deeplinkContact;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\Profile", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var \Contact\Entity\Profile
     */
    private $profile;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Community", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var \Contact\Entity\Community|Collections\ArrayCollection
     */
    private $community;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Registration", cascade={"persist"}, mappedBy="contact")
     * @ORM\OrderBy({"id" = "DESC"})
     * @Annotation\Exclude()
     * @var \Event\Entity\Registration|Collections\ArrayCollection
     */
    private $registration;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Badge\Badge", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Badge\Badge[]|Collections\ArrayCollection
     */
    private $badge;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Badge\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Badge\Contact[]|Collections\ArrayCollection
     */
    private $badgeContact;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Booth\Contact[]|Collections\ArrayCollection
     */
    private $boothContact;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Booth", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Booth[]|Collections\ArrayCollection
     */
    private $projectBooth;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Booth", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Booth[]|Collections\ArrayCollection
     */
    private $organisationBooth;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Booth\Financial[]|Collections\ArrayCollection
     */
    private $boothFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Note", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Note[]|Collections\ArrayCollection
     */
    private $note;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Selection", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Selection[]|Collections\ArrayCollection
     */
    private $selection;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\SelectionContact[]|Collections\ArrayCollection
     */
    private $selectionContact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Mailing\Entity\Contact[]|Collections\ArrayCollection
     */
    private $mailingContact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Mailing\Entity\Mailing[]|Collections\ArrayCollection
     */
    private $mailing;
    /**
     * @ORM\OneToMany(targetEntity="General\Entity\EmailMessage", mappedBy="contact", cascade={"persist","remove"})
     * @Annotation\Exclude()
     */
    private $emailMessage;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Result\Result", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Result\Result[]|Collections\ArrayCollection
     */
    private $result;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Workpackage\Workpackage", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Workpackage\Workpackage[]|Collections\ArrayCollection
     */
    private $workpackage;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Workpackage\Document", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Workpackage\Document[]|Collections\ArrayCollection
     */
    private $workpackageDocument;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Message", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Message[]|Collections\ArrayCollection
     */
    private $ideaMessage;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Evaluation\Evaluation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Evaluation\Evaluation[]|Collections\ArrayCollection
     */
    private $evaluation;
    /**
     * @ORM\OneToMany(targetEntity="Calendar\Entity\Calendar", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Calendar\Entity\Calendar[]|Collections\ArrayCollection
     */
    private $calendar;
    /**
     * @ORM\OneToMany(targetEntity="Calendar\Entity\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Calendar\Entity\Contact[]|Collections\ArrayCollection
     */
    private $calendarContact;
    /**
     * @ORM\OneToMany(targetEntity="Calendar\Entity\Document", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Calendar\Entity\Document[]|Collections\ArrayCollection
     */
    private $calendarDocument;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Review\Review", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Review\Review[]|Collections\ArrayCollection
     */
    private $projectReview;
    /**
     * @ORM\OneToOne(targetEntity="Project\Entity\Review\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Review\Contact
     */
    private $projectReviewContact;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Version\Review", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Version\Review[]|Collections\ArrayCollection
     */
    private $projectVersionReview;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\Report", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Report\Report[]|Collections\ArrayCollection
     */
    private $projectReport;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Calendar\Review", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Calendar\Review[]|Collections\ArrayCollection
     */
    private $projectCalendarReview;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\Review", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Report\Review[]|Collections\ArrayCollection
     */
    private $projectReportReview;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Invite", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Invite[]|Collections\ArrayCollection
     */
    private $invite;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Pca", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Pca[]|Collections\ArrayCollection
     */
    private $pca;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Invite", cascade={"persist"}, mappedBy="inviteContact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Invite[]|Collections\ArrayCollection
     */
    private $inviteContact;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Invite", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Invite[]|Collections\ArrayCollection
     */
    private $ideaInvite;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\MessageBoard", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\MessageBoard[]|Collections\ArrayCollection
     */
    private $ideaMessageBoard;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Idea\Invite", cascade={"persist"}, mappedBy="inviteContact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Invite[]|Collections\ArrayCollection
     */
    private $ideaInviteContact;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Loi", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Loi[]|Collections\ArrayCollection
     */
    private $loi;
    /**
     * @ORM\OneToMany(targetEntity="\Affiliation\Entity\Loi", cascade={"persist"}, mappedBy="approver")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Loi[]|Collections\ArrayCollection
     */
    private $loiApprover;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Doa[]|Collections\ArrayCollection
     */
    private $affiliationDoa;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Permit\Contact", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Admin\Entity\Permit\Contact[]|Collections\ArrayCollection
     */
    private $permitContact;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Session", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Admin\Entity\Session[]|Collections\ArrayCollection
     */
    private $session;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Exhibition\Voter", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude();
     * @var \Event\Entity\Exhibition\Voter[]|Collections\ArrayCollection
     */
    private $voter;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Exhibition\Tour", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Exhibition\Tour|Collections\ArrayCollection
     */
    private $tour;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Exhibition\Tour", cascade={"persist"}, mappedBy="tourContact")
     * @Annotation\Exclude();
     * @var \Event\Entity\Exhibition\Tour[]|Collections\ArrayCollection
     */
    private $tourContact;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\DoaReminder", cascade={"persist"}, mappedBy="receiver")
     * @Annotation\Exclude();
     * @var \Affiliation\Entity\DoaReminder[]|Collections\ArrayCollection
     */
    private $doaReminderReceiver;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\DoaReminder", cascade={"persist"}, mappedBy="sender")
     * @Annotation\Exclude();
     * @var \Affiliation\Entity\DoaReminder[]|Collections\ArrayCollection
     */
    private $doaReminderSender;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\LoiReminder", cascade={"persist"}, mappedBy="receiver")
     * @Annotation\Exclude();
     * @var \Affiliation\Entity\LoiReminder[]|Collections\ArrayCollection
     */
    private $loiReminderReceiver;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\LoiReminder", cascade={"persist"}, mappedBy="sender")
     * @Annotation\Exclude();
     * @var \Affiliation\Entity\LoiReminder[]|Collections\ArrayCollection
     */
    private $loiReminderSender;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Blog", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \News\Entity\Blog[]|Collections\ArrayCollection
     */
    private $blog;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Message", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \News\Entity\Message[]|Collections\ArrayCollection
     */
    private $blogMessage;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal\Entry", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Journal\Entry[]|Collections\ArrayCollection
     */
    private $journalEntry;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Journal[]|Collections\ArrayCollection
     */
    private $journal;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal", cascade={"persist"}, mappedBy="organisationContact")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Journal[]|Collections\ArrayCollection
     */
    private $organisationJournal;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Log[]|Collections\ArrayCollection
     */
    private $invoiceLog;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Reminder", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Invoice\Entity\Reminder[]|Collections\ArrayCollection
     */
    private $reminder;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Achievement", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Achievement[]|Collections\ArrayCollection
     */
    private $achievement;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Log[]|Collections\ArrayCollection
     */
    private $projectLog;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Changelog", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Changelog[]|Collections\ArrayCollection
     */
    private $projectChangelog;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\Process", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\ChangeRequest\Process
     */
    private $changeRequestProcess;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\CostChange", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\ChangeRequest\CostChange
     */
    private $changeRequestCostChange;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\Country", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\ChangeRequest\Country
     */
    private $changeRequestCountry;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Version\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Version\Contact
     */
    private $versionContact;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Workpackage\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Workpackage\Contact
     */
    private $workpackageContact;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Log", cascade={"persist"}, mappedBy="createdBy")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Log[]|Collections\Collection
     */
    private $logCreatedBy;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Log", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Contact\Entity\Log[]|Collections\Collection
     */
    private $log;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Pageview", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Admin\Entity\Pageview[]|Collections\Collection
     */
    private $pageview;

    public function __construct()
    {
        $this->project = new Collections\ArrayCollection();
        $this->projectVersion = new Collections\ArrayCollection();
        $this->projectDescription = new Collections\ArrayCollection();
        $this->projectReportEffortSpent = new Collections\ArrayCollection();
        $this->projectDocument = new Collections\ArrayCollection();
        $this->address = new Collections\ArrayCollection();
        $this->phone = new Collections\ArrayCollection();
        $this->emailAddress = new Collections\ArrayCollection();
        $this->access = new Collections\ArrayCollection();
        $this->optIn = new Collections\ArrayCollection();
        $this->domain = new Collections\ArrayCollection();
        $this->technology = new Collections\ArrayCollection();
        $this->dnd = new Collections\ArrayCollection();
        $this->nda = new Collections\ArrayCollection();
        $this->pca = new Collections\ArrayCollection();
        $this->ndaApprover = new Collections\ArrayCollection();
        $this->programDoa = new Collections\ArrayCollection();
        $this->rationale = new Collections\ArrayCollection();
        $this->organisationLog = new Collections\ArrayCollection();
        $this->affiliationLog = new Collections\ArrayCollection();
        $this->affiliationDescription = new Collections\ArrayCollection();
        $this->projectLog = new Collections\ArrayCollection();
        $this->projectChangelog = new Collections\ArrayCollection();
        $this->affiliation = new Collections\ArrayCollection();
        $this->actionClosed = new Collections\ArrayCollection();
        $this->actionStatus = new Collections\ArrayCollection();
        $this->actionComment = new Collections\ArrayCollection();
        $this->parent = new Collections\ArrayCollection();
        $this->parentFinancial = new Collections\ArrayCollection();
        $this->parentOrganisation = new Collections\ArrayCollection();
        $this->financial = new Collections\ArrayCollection();
        $this->invoice = new Collections\ArrayCollection();
        $this->publication = new Collections\ArrayCollection();
        $this->publicationDownload = new Collections\ArrayCollection();
        $this->photo = new Collections\ArrayCollection();
        $this->associate = new Collections\ArrayCollection();
        $this->deeplinkContact = new Collections\ArrayCollection();
        $this->community = new Collections\ArrayCollection();
        $this->registration = new Collections\ArrayCollection();
        $this->badge = new Collections\ArrayCollection();
        $this->badgeContact = new Collections\ArrayCollection();
        $this->boothContact = new Collections\ArrayCollection();
        $this->boothFinancial = new Collections\ArrayCollection();
        $this->selection = new Collections\ArrayCollection();
        $this->selectionContact = new Collections\ArrayCollection();
        $this->mailingContact = new Collections\ArrayCollection();
        $this->mailing = new Collections\ArrayCollection();
        $this->emailMessage = new Collections\ArrayCollection();
        $this->result = new Collections\ArrayCollection();
        $this->workpackage = new Collections\ArrayCollection();
        $this->workpackageDocument = new Collections\ArrayCollection();
        $this->idea = new Collections\ArrayCollection();
        $this->favouriteIdea = new Collections\ArrayCollection();
        $this->ideaMessage = new Collections\ArrayCollection();
        $this->ideaPartner = new Collections\ArrayCollection();
        $this->ideaMessageBoard = new Collections\ArrayCollection();
        $this->blog = new Collections\ArrayCollection();
        $this->blogMessage = new Collections\ArrayCollection();
        $this->evaluation = new Collections\ArrayCollection();
        $this->calendarContact = new Collections\ArrayCollection();
        $this->calendarDocument = new Collections\ArrayCollection();
        $this->calendar = new Collections\ArrayCollection();
        $this->proxyProject = new Collections\ArrayCollection();
        $this->projectReview = new Collections\ArrayCollection();
        $this->projectVersionReview = new Collections\ArrayCollection();
        $this->projectReport = new Collections\ArrayCollection();
        $this->projectReportItem = new Collections\ArrayCollection();
        $this->projectReportWorkpackageDescription = new Collections\ArrayCollection();
        $this->projectCalendarReview = new Collections\ArrayCollection();
        $this->projectReportReview = new Collections\ArrayCollection();
        $this->contract = new Collections\ArrayCollection();
        $this->contractVersion = new Collections\ArrayCollection();
        $this->invite = new Collections\ArrayCollection();
        $this->inviteContact = new Collections\ArrayCollection();
        $this->ideaInvite = new Collections\ArrayCollection();
        $this->ideaInviteContact = new Collections\ArrayCollection();
        $this->loi = new Collections\ArrayCollection();
        $this->loiApprover = new Collections\ArrayCollection();
        $this->affiliationDoa = new Collections\ArrayCollection();
        $this->parentDoa = new Collections\ArrayCollection();
        $this->permitContact = new Collections\ArrayCollection();
        $this->session = new Collections\ArrayCollection();
        $this->voter = new Collections\ArrayCollection();
        $this->tour = new Collections\ArrayCollection();
        $this->projectBooth = new Collections\ArrayCollection();
        $this->organisationBooth = new Collections\ArrayCollection();
        $this->tourContact = new Collections\ArrayCollection();
        $this->doaReminderReceiver = new Collections\ArrayCollection();
        $this->doaReminderSender = new Collections\ArrayCollection();
        $this->loiReminderReceiver = new Collections\ArrayCollection();
        $this->loiReminderSender = new Collections\ArrayCollection();
        $this->journalEntry = new Collections\ArrayCollection();
        $this->journal = new Collections\ArrayCollection();
        $this->organisationJournal = new Collections\ArrayCollection();
        $this->invoiceLog = new Collections\ArrayCollection();
        $this->reminder = new Collections\ArrayCollection();
        $this->achievement = new Collections\ArrayCollection();
        $this->changeRequestProcess = new Collections\ArrayCollection();
        $this->changeRequestCostChange = new Collections\ArrayCollection();
        $this->changeRequestCountry = new Collections\ArrayCollection();
        $this->versionContact = new Collections\ArrayCollection();
        $this->workpackageContact = new Collections\ArrayCollection();
        $this->logCreatedBy = new Collections\ArrayCollection();
        $this->log = new Collections\ArrayCollection();
        $this->affiliationVersion = new Collections\ArrayCollection();
        $this->note = new Collections\ArrayCollection();
        $this->pageview = new Collections\ArrayCollection();

        $this->hash = hash('sha256', Rand::getString(100) . self::HASH_KEY);
    }


    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __isset($property)
    {
        return isset($this->$property);
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

    public function parseFullName(): string
    {
        return $this->getDisplayName();
    }

    public function getDisplayName(): string
    {
        $name = sprintf('%s %s', $this->firstName, trim(implode(' ', [$this->middleName, $this->lastName])));

        return (string)(!empty(trim($name)) ? $name : $this->email);
    }

    public function parseInitials(): string
    {
        return sprintf(
            '%s%s%s',
            \substr((string)$this->firstName, 0, 1),
            \substr((string)$this->middleName, 0, 1),
            \substr((string)$this->lastName, 0, 1)
        );
    }

    public function isAnonymised(): bool
    {
        return null !== $this->dateAnonymous;
    }

    public function isActivated(): bool
    {
        return null !== $this->dateActivated;
    }

    public function hasOrganisation(): bool
    {
        return null !== $this->contactOrganisation;
    }

    public function isVisibleInCommunity(): bool
    {
        return null !== $this->profile && $this->profile->getVisible() === Profile::VISIBLE_COMMUNITY;
    }


    public function getRoles(): array
    {
        $accessRoles = ['user'];
        foreach ($this->access as $access) {
            $accessRoles[] = strtolower($access->getAccess());
        }

        return $accessRoles;
    }

    public function addOptIn(Collections\Collection $optInCollection): void
    {
        foreach ($optInCollection as $optIn) {
            $this->optIn->add($optIn);
        }
    }

    public function removeOptIn(Collections\Collection $optInCollection): void
    {
        foreach ($optInCollection as $optIn) {
            $this->optIn->removeElement($optIn);
        }
    }

    public function addAccess(Collections\Collection $accessCollection): void
    {
        foreach ($accessCollection as $access) {
            $this->access->add($access);
        }
    }

    public function removeAccess(Collections\Collection $accessCollection): void
    {
        foreach ($accessCollection as $single) {
            $this->access->removeElement($single);
        }
    }

    public function addAddress(Collections\Collection $addressCollection): void
    {
        foreach ($addressCollection as $address) {
            $this->address->add($address);
        }
    }

    public function removeAddress(Collections\Collection $addressCollection): void
    {
        foreach ($addressCollection as $single) {
            $this->address->removeElement($single);
        }
    }

    public function addPhone(Collections\Collection $phoneCollection): void
    {
        foreach ($phoneCollection as $phone) {
            $this->phone->add($phone);
        }
    }

    public function removePhone(Collections\Collection $phoneCollection): void
    {
        foreach ($phoneCollection as $single) {
            $this->phone->removeElement($single);
        }
    }

    public function addPhoto(Collections\Collection $photoCollection): void
    {
        foreach ($photoCollection as $photo) {
            $this->photo->add($photo);
        }
    }

    public function removePhoto(Collections\Collection $photoCollection): void
    {
        foreach ($photoCollection as $single) {
            $this->photo->removeElement($single);
        }
    }

    public function addCommunity(Collections\Collection $communityCollection): void
    {
        foreach ($communityCollection as $community) {
            $this->community->add($community);
        }
    }

    public function removeCommunity(Collections\Collection $communityCollection): void
    {
        foreach ($communityCollection as $single) {
            $this->community->removeElement($single);
        }
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated): Contact
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateEnd(): ?\DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd($dateEnd): Contact
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDateAnonymous(): ?\DateTime
    {
        return $this->dateAnonymous;
    }

    public function setDateAnonymous(?\DateTime $dateAnonymous): Contact
    {
        $this->dateAnonymous = $dateAnonymous;
        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param $dateOfBirth
     *
     * @return Contact
     */
    public function setDateOfBirth($dateOfBirth): Contact
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Contact
     */
    public function setEmail($email): Contact
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName): Contact
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return \General\Entity\Gender
     */
    public function getGender(): ?\General\Entity\Gender
    {
        return $this->gender;
    }

    /**
     * @param \General\Entity\Gender $gender
     *
     * @return Contact
     */
    public function setGender($gender): Contact
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Contact
     */
    public function setId($id): Contact
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param $lastName
     *
     * @return Contact
     */
    public function setLastName($lastName): Contact
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastUpdate(): ?\DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param $lastUpdate
     *
     * @return Contact
     */
    public function setLastUpdate($lastUpdate): Contact
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @return int
     */
    public function getMessenger()
    {
        return $this->messenger;
    }

    /**
     * @param $messenger
     *
     * @return Contact
     */
    public function setMessenger($messenger): Contact
    {
        $this->messenger = $messenger;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param $middleName
     *
     * @return Contact
     */
    public function setMiddleName($middleName): Contact
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return Contact
     */
    public function setPassword($password): Contact
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaltedPassword(): ?string
    {
        return $this->saltedPassword;
    }

    /**
     * @param $saltedPassword
     *
     * @return Contact
     */
    public function setSaltedPassword($saltedPassword): Contact
    {
        $this->saltedPassword = $saltedPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param $position
     *
     * @return Contact
     */
    public function setPosition($position): Contact
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return \General\Entity\Title
     */
    public function getTitle(): ?\General\Entity\Title
    {
        return $this->title;
    }

    /**
     * @param \General\Entity\Title $title
     *
     * @return Contact
     */
    public function setTitle($title): Contact
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  int $state
     *
     * @return void
     */
    public function setState($state): void
    {
    }

    /**
     *
     */
    public function getState(): void
    {
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername($username): Contact
    {
        $this->email = $username;

        return $this;
    }

    public function getShortName(): string
    {
        $name = sprintf(
            '%s. %s',
            mb_substr($this->firstName, 0, 1),
            trim(implode(' ', [$this->middleName, $this->lastName]))
        );

        return !empty($name) ? $name : $this->email;
    }

    public function getFormName(): string
    {
        $name = sprintf('%s, %s', trim(implode(' ', [$this->middleName, $this->lastName])), $this->firstName);

        return !empty($name) ? $name : $this->email;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): Contact
    {
        $this->hash = $hash;
        return $this;
    }

    public function setDisplayName($displayName): bool
    {
        return false;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment($department): Contact
    {
        $this->department = $department;

        return $this;
    }

    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param  \Admin\Entity\Access[]|Collections\ArrayCollection $access
     *
     * @return Contact
     */
    public function setAccess($access): Contact
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return Email|Collections\ArrayCollection
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param  Email|Collections\ArrayCollection $emailAddress
     *
     * @return Contact
     */
    public function setEmailAddress($emailAddress): Contact
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return Cv
     */
    public function getCv(): ?Cv
    {
        return $this->cv;
    }

    /**
     * @param  Cv $cv
     *
     * @return Contact
     */
    public function setCv($cv): Contact
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * @return Address[]|Collections\ArrayCollection
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param  Address|Collections\ArrayCollection $address
     *
     * @return Contact
     */
    public function setAddress($address): Contact
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Phone[]|Collections\ArrayCollection
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param  Phone|Collections\ArrayCollection $phone
     *
     * @return Contact
     */
    public function setPhone($phone): Contact
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return OptIn[]|Collections\ArrayCollection
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * @param  OptIn[]|Collections\ArrayCollection $optIn
     *
     * @return Contact
     */
    public function setOptIn($optIn): Contact
    {
        $this->optIn = $optIn;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Project[]
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Project[] $project
     *
     * @return Contact
     */
    public function setProject($project): Contact
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Rationale[]
     */
    public function getRationale()
    {
        return $this->rationale;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Rationale[] $rationale
     *
     * @return Contact
     */
    public function setRationale($rationale): Contact
    {
        $this->rationale = $rationale;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Description\Description
     */
    public function getProjectDescription()
    {
        return $this->projectDescription;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Description\Description $projectDescription
     *
     * @return Contact
     */
    public function setProjectDescription($projectDescription): Contact
    {
        $this->projectDescription = $projectDescription;

        return $this;
    }

    /**
     * @return \Project\Entity\Version\Version[]
     */
    public function getProjectVersion()
    {
        return $this->projectVersion;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Version\Version $projectVersion
     *
     * @return Contact
     */
    public function setProjectVersion($projectVersion): Contact
    {
        $this->projectVersion = $projectVersion;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Document\Document
     */
    public function getProjectDocument()
    {
        return $this->projectDocument;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Document\Document $projectDocument
     *
     * @return Contact
     */
    public function setProjectDocument($projectDocument): Contact
    {
        $this->projectDocument = $projectDocument;

        return $this;
    }

    /**
     * @return Dnd|Collections\ArrayCollection
     */
    public function getDnd()
    {
        return $this->dnd;
    }

    /**
     * @param  Dnd|Collections\ArrayCollection $dnd
     *
     * @return Contact
     */
    public function setDnd($dnd): Contact
    {
        $this->dnd = $dnd;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Action[]
     */
    public function getActionClosed()
    {
        return $this->actionClosed;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Action[] $actionClosed
     *
     * @return Contact
     */
    public function setActionClosed($actionClosed): Contact
    {
        $this->actionClosed = $actionClosed;
        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Action[]
     */
    public function getActionStatus()
    {
        return $this->actionStatus;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Action[] $actionStatus
     *
     * @return Contact
     */
    public function setActionStatus($actionStatus): Contact
    {
        $this->actionStatus = $actionStatus;
        return $this;
    }


    /**
     * @return Collections\ArrayCollection|\Project\Entity\Action\Comment[]
     */
    public function getActionComment()
    {
        return $this->actionComment;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Action\Comment[] $actionComment
     *
     * @return Contact
     */
    public function setActionComment($actionComment)
    {
        $this->actionComment = $actionComment;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Contract[]
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Contract[] $contract
     *
     * @return Contact
     */
    public function setContract($contract): Contact
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Contract[]
     */
    public function getContractVersion()
    {
        return $this->contractVersion;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Contract[] $contractVersion
     *
     * @return Contact
     */
    public function setContractVersion($contractVersion): Contact
    {
        $this->contractVersion = $contractVersion;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Nda
     */
    public function getNda()
    {
        return $this->nda;
    }

    /**
     * @param  Collections\ArrayCollection|\Program\Entity\Nda $nda
     *
     * @return Contact
     */
    public function setNda($nda): Contact
    {
        $this->nda = $nda;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Nda[]
     */
    public function getNdaApprover()
    {
        return $this->ndaApprover;
    }

    /**
     * @param Collections\ArrayCollection|\Program\Entity\Nda[] $ndaApprover
     *
     * @return Contact
     */
    public function setNdaApprover($ndaApprover): Contact
    {
        $this->ndaApprover = $ndaApprover;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Doa
     */
    public function getProgramDoa()
    {
        return $this->programDoa;
    }

    /**
     * @param  Collections\ArrayCollection|\Program\Entity\Doa $programDoa
     *
     * @return Contact
     */
    public function setProgramDoa($programDoa): Contact
    {
        $this->programDoa = $programDoa;

        return $this;
    }

    /**
     * @return ContactOrganisation
     */
    public function getContactOrganisation()
    {
        return $this->contactOrganisation;
    }

    /**
     * @param  ContactOrganisation $contactOrganisation
     *
     * @return Contact
     */
    public function setContactOrganisation($contactOrganisation): Contact
    {
        $this->contactOrganisation = $contactOrganisation;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param  Collections\ArrayCollection|\Program\Entity\Domain $domain
     *
     * @return Contact
     */
    public function setDomain($domain): Contact
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Idea
     */
    public function getIdea()
    {
        return $this->idea;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\Idea $idea
     *
     * @return Contact
     */
    public function setIdea($idea): Contact
    {
        $this->idea = $idea;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Idea
     */
    public function getFavouriteIdea()
    {
        return $this->favouriteIdea;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\Idea $favouriteIdea
     *
     * @return Contact
     */
    public function setFavouriteIdea($favouriteIdea): Contact
    {
        $this->favouriteIdea = $favouriteIdea;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\Technology
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param  Collections\ArrayCollection|\Program\Entity\Technology $technology
     *
     * @return Contact
     */
    public function setTechnology($technology): Contact
    {
        $this->technology = $technology;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\Log
     */
    public function getOrganisationLog()
    {
        return $this->organisationLog;
    }

    /**
     * @param  Collections\ArrayCollection|\Organisation\Entity\Log $organisationLog
     *
     * @return Contact
     */
    public function setOrganisationLog($organisationLog): Contact
    {
        $this->organisationLog = $organisationLog;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param  \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection $affiliation
     *
     * @return Contact
     */
    public function setAffiliation($affiliation): Contact
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Log|Collections\ArrayCollection
     */
    public function getAffiliationLog()
    {
        return $this->affiliationLog;
    }

    /**
     * @param  \Affiliation\Entity\Log|Collections\ArrayCollection $affiliationLog
     *
     * @return Contact
     */
    public function setAffiliationLog($affiliationLog): Contact
    {
        $this->affiliationLog = $affiliationLog;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Financial[]|Collections\ArrayCollection
     */
    public function getFinancial()
    {
        return $this->financial;
    }

    /**
     * @param  \Affiliation\Entity\Financial[]|Collections\ArrayCollection $financial
     *
     * @return Contact
     */
    public function setFinancial($financial): Contact
    {
        $this->financial = $financial;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Description|Collections\ArrayCollection
     */
    public function getAffiliationDescription()
    {
        return $this->affiliationDescription;
    }

    /**
     * @param  \Affiliation\Entity\Description|Collections\ArrayCollection $affiliationDescription
     *
     * @return Contact
     */
    public function setAffiliationDescription($affiliationDescription): Contact
    {
        $this->affiliationDescription = $affiliationDescription;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Version|Collections\ArrayCollection
     */
    public function getAffiliationVersion()
    {
        return $this->affiliationVersion;
    }

    /**
     * @param  \Affiliation\Entity\Version|Collections\ArrayCollection $affiliationVersion
     *
     * @return Contact
     */
    public function setAffiliationVersion($affiliationVersion): Contact
    {
        $this->affiliationVersion = $affiliationVersion;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param  Collections\ArrayCollection|\Invoice\Entity\Invoice $invoice
     *
     * @return Contact
     */
    public function setInvoice($invoice): Contact
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Publication\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @param  Collections\ArrayCollection|\Publication\Entity\Publication $publication
     *
     * @return Contact
     */
    public function setPublication($publication): Contact
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Publication\Entity\Download
     */
    public function getPublicationDownload()
    {
        return $this->publicationDownload;
    }

    /**
     * @param  Collections\ArrayCollection|\Publication\Entity\Download $publicationDownload
     *
     * @return Contact
     */
    public function setPublicationDownload($publicationDownload): Contact
    {
        $this->publicationDownload = $publicationDownload;

        return $this;
    }

    /**
     * Find the photo. We need to apply a trick here since the photo has a 1:n relation in the entities to avoid
     * the eager loading of the BLOB but we know that we only have 1 photo
     *
     * @return \Contact\Entity\Photo[]|Collections\ArrayCollection
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param  Photo|Collections\ArrayCollection $photo
     *
     * @return Contact
     */
    public function setPhoto($photo): Contact
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection
     */
    public function getAssociate()
    {
        return $this->associate;
    }

    /**
     * @param  \Affiliation\Entity\Affiliation[]|Collections\ArrayCollection $associate
     *
     * @return Contact
     */
    public function setAssociate($associate): Contact
    {
        $this->associate = $associate;

        return $this;
    }

    /**
     * @return \Program\Entity\Funder
     */
    public function getFunder(): ?\Program\Entity\Funder
    {
        return $this->funder;
    }

    public function setFunder($funder): Contact
    {
        $this->funder = $funder;

        return $this;
    }

    public function getDeeplinkContact()
    {
        return $this->deeplinkContact;
    }

    public function setDeeplinkContact($deeplinkContact): Contact
    {
        $this->deeplinkContact = $deeplinkContact;

        return $this;
    }

    /**
     * @return Profile
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    /**
     * @param  Profile $profile
     *
     * @return Contact
     */
    public function setProfile($profile): Contact
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Community[]|Collections\ArrayCollection
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * @param  Community|Collections\ArrayCollection $community
     *
     * @return Contact
     */
    public function setCommunity($community): Contact
    {
        $this->community = $community;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Registration[]
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Registration $registration
     *
     * @return Contact
     */
    public function setRegistration($registration): Contact
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Badge\Badge[]
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Badge\Badge $badge
     *
     * @return Contact
     */
    public function setBadge($badge): Contact
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Badge\Contact[]
     */
    public function getBadgeContact()
    {
        return $this->badgeContact;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Badge\Contact $badgeContact
     *
     * @return Contact
     */
    public function setBadgeContact($badgeContact): Contact
    {
        $this->badgeContact = $badgeContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Booth\Contact[]
     */
    public function getBoothContact()
    {
        return $this->boothContact;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Booth\Contact[] $boothContact
     *
     * @return Contact
     */
    public function setBoothContact($boothContact): Contact
    {
        $this->boothContact = $boothContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Booth[]
     */
    public function getProjectBooth()
    {
        return $this->projectBooth;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Booth[] $projectBooth
     *
     * @return Contact
     */
    public function setProjectBooth($projectBooth): Contact
    {
        $this->projectBooth = $projectBooth;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\Booth[]
     */
    public function getOrganisationBooth()
    {
        return $this->organisationBooth;
    }

    /**
     * @param  Collections\ArrayCollection|\Organisation\Entity\Booth[] $organisationBooth
     *
     * @return Contact
     */
    public function setOrganisationBooth($organisationBooth): Contact
    {
        $this->organisationBooth = $organisationBooth;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Booth\Financial
     */
    public function getBoothFinancial()
    {
        return $this->boothFinancial;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Booth\Financial $boothFinancial
     *
     * @return Contact
     */
    public function setBoothFinancial($boothFinancial): Contact
    {
        $this->boothFinancial = $boothFinancial;

        return $this;
    }

    /**
     * @return Note[]|Collections\ArrayCollection
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param  Note[]|Collections\ArrayCollection $note
     *
     * @return Contact
     */
    public function setNote($note): Contact
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Selection[]|Collections\ArrayCollection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param  Selection[]|Collections\ArrayCollection $selection
     *
     * @return Contact
     */
    public function setSelection($selection): Contact
    {
        $this->selection = $selection;

        return $this;
    }

    /**
     * @return SelectionContact|Collections\ArrayCollection
     */
    public function getSelectionContact()
    {
        return $this->selectionContact;
    }

    /**
     * @param  SelectionContact|Collections\ArrayCollection $selectionContact
     *
     * @return Contact
     */
    public function setSelectionContact($selectionContact): Contact
    {
        $this->selectionContact = $selectionContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Mailing\Entity\Contact
     */
    public function getMailingContact()
    {
        return $this->mailingContact;
    }

    /**
     * @param  Collections\ArrayCollection|\Mailing\Entity\Contact $mailingContact
     *
     * @return Contact
     */
    public function setMailingContact($mailingContact): Contact
    {
        $this->mailingContact = $mailingContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Mailing\Entity\Mailing
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * @param  Collections\ArrayCollection|\Mailing\Entity\Mailing $mailing
     *
     * @return Contact
     */
    public function setMailing($mailing): Contact
    {
        $this->mailing = $mailing;

        return $this;
    }

    /**
     * @return Collections\Collection|EmailMessage[]
     */
    public function getEmailMessage()
    {
        return $this->emailMessage;
    }

    /**
     * @param Collections\Collection|EmailMessage[] $emailMessage
     *
     * @return Contact
     */
    public function setEmailMessage($emailMessage): Contact
    {
        $this->emailMessage = $emailMessage;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Result\Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Result\Result $result
     *
     * @return Contact
     */
    public function setResult($result): Contact
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Workpackage\Workpackage[]
     */
    public function getWorkpackage()
    {
        return $this->workpackage;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Workpackage\Workpackage[] $workpackage
     *
     * @return Contact
     */
    public function setWorkpackage($workpackage): Contact
    {
        $this->workpackage = $workpackage;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Workpackage\Document
     */
    public function getWorkpackageDocument()
    {
        return $this->workpackageDocument;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Workpackage\Document $workpackageDocument
     *
     * @return Contact
     */
    public function setWorkpackageDocument($workpackageDocument): Contact
    {
        $this->workpackageDocument = $workpackageDocument;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Message
     */
    public function getIdeaMessage()
    {
        return $this->ideaMessage;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\Message $ideaMessage
     *
     * @return Contact
     */
    public function setIdeaMessage($ideaMessage): Contact
    {
        $this->ideaMessage = $ideaMessage;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Evaluation\Evaluation
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Evaluation\Evaluation $evaluation
     *
     * @return Contact
     */
    public function setEvaluation($evaluation): Contact
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * @return \Calendar\Entity\Calendar|Collections\ArrayCollection
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param  \Calendar\Entity\Calendar|Collections\ArrayCollection $calendar
     *
     * @return Contact
     */
    public function setCalendar($calendar): Contact
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * @return \Calendar\Entity\Contact|Collections\ArrayCollection
     */
    public function getCalendarContact()
    {
        return $this->calendarContact;
    }

    /**
     * @param  \Calendar\Entity\Contact|Collections\ArrayCollection $calendarContact
     *
     * @return Contact
     */
    public function setCalendarContact($calendarContact): Contact
    {
        $this->calendarContact = $calendarContact;

        return $this;
    }

    /**
     * @return \Calendar\Entity\Document|Collections\ArrayCollection
     */
    public function getCalendarDocument()
    {
        return $this->calendarDocument;
    }

    /**
     * @param  \Calendar\Entity\Document|Collections\ArrayCollection $calendarDocument
     *
     * @return Contact
     */
    public function setCalendarDocument($calendarDocument): Contact
    {
        $this->calendarDocument = $calendarDocument;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Review\Review
     */
    public function getProjectReview()
    {
        return $this->projectReview;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Review\Review $projectReview
     *
     * @return Contact
     */
    public function setProjectReview($projectReview): Contact
    {
        $this->projectReview = $projectReview;

        return $this;
    }

    /**
     * @return \Project\Entity\Review\Contact
     */
    public function getProjectReviewContact()
    {
        return $this->projectReviewContact;
    }

    /**
     * @param \Project\Entity\Review\Contact $projectReviewContact
     *
     * @return Contact
     */
    public function setProjectReviewContact($projectReviewContact): Contact
    {
        $this->projectReviewContact = $projectReviewContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Review\Review
     */
    public function getProjectVersionReview()
    {
        return $this->projectVersionReview;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Review\Review $projectVersionReview
     *
     * @return Contact
     */
    public function setProjectVersionReview($projectVersionReview): Contact
    {
        $this->projectVersionReview = $projectVersionReview;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\Report
     */
    public function getProjectReport()
    {
        return $this->projectReport;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Report\Report $projectReport
     *
     * @return Contact
     */
    public function setProjectReport($projectReport): Contact
    {
        $this->projectReport = $projectReport;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Calendar\Review
     */
    public function getProjectCalendarReview()
    {
        return $this->projectCalendarReview;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Calendar\Review $projectCalendarReview
     *
     * @return Contact
     */
    public function setProjectCalendarReview($projectCalendarReview): Contact
    {
        $this->projectCalendarReview = $projectCalendarReview;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\Review[]
     */
    public function getProjectReportReview()
    {
        return $this->projectReportReview;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Report\Review[] $projectReportReview
     *
     * @return Contact
     */
    public function setProjectReportReview($projectReportReview): Contact
    {
        $this->projectReportReview = $projectReportReview;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Invite[]
     */
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Invite[] $invite
     *
     * @return Contact
     */
    public function setInvite($invite): Contact
    {
        $this->invite = $invite;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Invite[]
     */
    public function getInviteContact()
    {
        return $this->inviteContact;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Invite[] $inviteContact
     *
     * @return Contact
     */
    public function setInviteContact($inviteContact): Contact
    {
        $this->inviteContact = $inviteContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Invite[]
     */
    public function getIdeaInvite()
    {
        return $this->ideaInvite;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\Invite[] $ideaInvite
     *
     * @return Contact
     */
    public function setIdeaInvite($ideaInvite): Contact
    {
        $this->ideaInvite = $ideaInvite;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Invite[]
     */
    public function getIdeaInviteContact()
    {
        return $this->ideaInviteContact;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\Invite[] $ideaInviteContact
     *
     * @return Contact
     */
    public function setIdeaInviteContact($ideaInviteContact): Contact
    {
        $this->ideaInviteContact = $ideaInviteContact;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Loi[]|Collections\ArrayCollection
     */
    public function getLoi()
    {
        return $this->loi;
    }

    /**
     * @param  \Affiliation\Entity\Loi[]|Collections\ArrayCollection $loi
     *
     * @return Contact
     */
    public function setLoi($loi): Contact
    {
        $this->loi = $loi;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Loi[]|Collections\ArrayCollection
     */
    public function getLoiApprover()
    {
        return $this->loiApprover;
    }

    /**
     * @param \Affiliation\Entity\Loi[]|Collections\ArrayCollection $loiApprover
     *
     * @return Contact
     */
    public function setLoiApprover($loiApprover): Contact
    {
        $this->loiApprover = $loiApprover;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\Doa[]|Collections\ArrayCollection
     */
    public function getAffiliationDoa()
    {
        return $this->affiliationDoa;
    }

    /**
     * @param  \Affiliation\Entity\Doa[]|Collections\ArrayCollection $affiliationDoa
     *
     * @return Contact
     */
    public function setAffiliationDoa($affiliationDoa): Contact
    {
        $this->affiliationDoa = $affiliationDoa;

        return $this;
    }

    /**
     * @return \Admin\Entity\Permit\Contact[]|Collections\ArrayCollection
     */
    public function getPermitContact()
    {
        return $this->permitContact;
    }

    /**
     * @param  \Admin\Entity\Permit\Contact[]|Collections\Collection $permitContact
     *
     * @return Contact
     */
    public function setPermitContact($permitContact): Contact
    {
        $this->permitContact = $permitContact;

        return $this;
    }

    /**
     * @return \Admin\Entity\Session[]|Collections\Collection
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param  \Admin\Entity\Session[]|Collections\Collection $session
     *
     * @return Contact
     */
    public function setSession($session): Contact
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Exhibition\Voter[]
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Exhibition\Voter[] $voter
     *
     * @return Contact
     */
    public function setVoter($voter): Contact
    {
        $this->voter = $voter;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Exhibition\Tour
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Exhibition\Tour $tour
     *
     * @return Contact
     */
    public function setTour($tour): Contact
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Exhibition\Tour[]
     */
    public function getTourContact()
    {
        return $this->tourContact;
    }

    /**
     * @param  Collections\ArrayCollection|\Event\Entity\Exhibition\Tour[] $tourContact
     *
     * @return Contact
     */
    public function setTourContact($tourContact): Contact
    {
        $this->tourContact = $tourContact;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\DoaReminder[]|Collections\ArrayCollection
     */
    public function getDoaReminderReceiver()
    {
        return $this->doaReminderReceiver;
    }

    /**
     * @param  \Affiliation\Entity\DoaReminder[]|Collections\ArrayCollection $doaReminderReceiver
     *
     * @return Contact
     */
    public function setDoaReminderReceiver($doaReminderReceiver): Contact
    {
        $this->doaReminderReceiver = $doaReminderReceiver;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\DoaReminder[]|Collections\ArrayCollection
     */
    public function getDoaReminderSender()
    {
        return $this->doaReminderSender;
    }

    /**
     * @param  \Affiliation\Entity\DoaReminder[]|Collections\ArrayCollection $doaReminderSender
     *
     * @return Contact
     */
    public function setDoaReminderSender($doaReminderSender): Contact
    {
        $this->doaReminderSender = $doaReminderSender;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\LoiReminder[]|Collections\ArrayCollection
     */
    public function getLoiReminderReceiver()
    {
        return $this->loiReminderReceiver;
    }

    /**
     * @param  \Affiliation\Entity\LoiReminder[]|Collections\ArrayCollection $loiReminderReceiver
     *
     * @return Contact
     */
    public function setLoiReminderReceiver($loiReminderReceiver): Contact
    {
        $this->loiReminderReceiver = $loiReminderReceiver;

        return $this;
    }

    /**
     * @return \Affiliation\Entity\LoiReminder[]|Collections\ArrayCollection
     */
    public function getLoiReminderSender()
    {
        return $this->loiReminderSender;
    }

    /**
     * @param  \Affiliation\Entity\LoiReminder[]|Collections\ArrayCollection $loiReminderSender
     *
     * @return Contact
     */
    public function setLoiReminderSender($loiReminderSender): Contact
    {
        $this->loiReminderSender = $loiReminderSender;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\News\Entity\Blog
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @param  Collections\ArrayCollection|\News\Entity\Blog $blog
     *
     * @return Contact
     */
    public function setBlog($blog): Contact
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\News\Entity\Message
     */
    public function getBlogMessage()
    {
        return $this->blogMessage;
    }

    /**
     * @param  Collections\ArrayCollection|\News\Entity\Message $blogMessage
     *
     * @return Contact
     */
    public function setBlogMessage($blogMessage): Contact
    {
        $this->blogMessage = $blogMessage;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Journal\Entry[]
     */
    public function getJournalEntry()
    {
        return $this->journalEntry;
    }

    /**
     * @param  Collections\ArrayCollection|\Invoice\Entity\Journal\Entry[] $journalEntry
     *
     * @return Contact
     */
    public function setJournalEntry($journalEntry): Contact
    {
        $this->journalEntry = $journalEntry;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Journal[]
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param  Collections\ArrayCollection|\Invoice\Entity\Journal[] $journal
     *
     * @return Contact
     */
    public function setJournal($journal): Contact
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Journal[]
     */
    public function getOrganisationJournal()
    {
        return $this->organisationJournal;
    }

    /**
     * @param  Collections\ArrayCollection|\Invoice\Entity\Journal[] $organisationJournal
     *
     * @return Contact
     */
    public function setOrganisationJournal($organisationJournal): Contact
    {
        $this->organisationJournal = $organisationJournal;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Log[]
     */
    public function getInvoiceLog()
    {
        return $this->invoiceLog;
    }

    /**
     * @param  Collections\ArrayCollection|\Invoice\Entity\Log[] $invoiceLog
     *
     * @return Contact
     */
    public function setInvoiceLog($invoiceLog): Contact
    {
        $this->invoiceLog = $invoiceLog;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Invoice\Entity\Reminder[]
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * @param  Collections\ArrayCollection|\Invoice\Entity\Reminder[] $reminder
     *
     * @return Contact
     */
    public function setReminder($reminder): Contact
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Achievement[]
     */
    public function getAchievement()
    {
        return $this->achievement;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Achievement[] $achievement
     *
     * @return Contact
     */
    public function setAchievement($achievement): Contact
    {
        $this->achievement = $achievement;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\Partner
     */
    public function getIdeaPartner()
    {
        return $this->ideaPartner;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\Partner $ideaPartner
     *
     * @return Contact
     */
    public function setIdeaPartner($ideaPartner): Contact
    {
        $this->ideaPartner = $ideaPartner;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Idea\MessageBoard[]
     */
    public function getIdeaMessageBoard()
    {
        return $this->ideaMessageBoard;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Idea\MessageBoard[] $ideaMessageBoard
     *
     * @return Contact
     */
    public function setIdeaMessageBoard($ideaMessageBoard): Contact
    {
        $this->ideaMessageBoard = $ideaMessageBoard;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Log[]
     */
    public function getProjectLog()
    {
        return $this->projectLog;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Log[] $projectLog
     *
     * @return Contact
     */
    public function setProjectLog($projectLog): Contact
    {
        $this->projectLog = $projectLog;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection
     */
    public function getProjectChangelog()
    {
        return $this->projectChangelog;
    }

    /**
     * @param Collections\ArrayCollection $projectChangelog
     *
     * @return Contact
     */
    public function setProjectChangelog(Collections\ArrayCollection $projectChangelog): Contact
    {
        $this->projectChangelog = $projectChangelog;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\Item[]
     */
    public function getProjectReportItem()
    {
        return $this->projectReportItem;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Report\Item[] $projectReportItem
     *
     * @return Contact
     */
    public function setProjectReportItem($projectReportItem): Contact
    {
        $this->projectReportItem = $projectReportItem;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\EffortSpent[]
     */
    public function getProjectReportEffortSpent()
    {
        return $this->projectReportEffortSpent;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Report\EffortSpent[] $projectReportEffortSpent
     *
     * @return Contact
     */
    public function setProjectReportEffortSpent($projectReportEffortSpent): Contact
    {
        $this->projectReportEffortSpent = $projectReportEffortSpent;

        return $this;
    }

    /**
     * @return \Project\Entity\ChangeRequest\Process[]|Collections\Collection
     */
    public function getChangeRequestProcess()
    {
        return $this->changeRequestProcess;
    }

    /**
     * @param \Project\Entity\ChangeRequest\Process[]|Collections\Collection $changerequestProcess
     *
     * @return Contact
     */
    public function setChangeRequestProcess($changerequestProcess): Contact
    {
        $this->changeRequestProcess = $changerequestProcess;

        return $this;
    }

    /**
     * @return \Project\Entity\ChangeRequest\CostChange[]|Collections\Collection
     */
    public function getChangeRequestCostChange()
    {
        return $this->changeRequestCostChange;
    }

    /**
     * @param \Project\Entity\ChangeRequest\CostChange[]|Collections\Collection $changerequestCostChange
     *
     * @return Contact
     */
    public function setChangeRequestCostChange($changerequestCostChange): Contact
    {
        $this->changeRequestCostChange = $changerequestCostChange;

        return $this;
    }

    /**
     * @return \Project\Entity\ChangeRequest\Country[]|Collections\Collection
     */
    public function getChangeRequestCountry()
    {
        return $this->changeRequestCountry;
    }

    /**
     * @param $changerequestCountry
     *
     * @return Contact
     */
    public function setChangeRequestCountry($changerequestCountry): Contact
    {
        $this->changeRequestCountry = $changerequestCountry;

        return $this;
    }

    /**
     * @return \Project\Entity\Version\Contact[]|Collections\Collection
     */
    public function getVersionContact()
    {
        return $this->versionContact;
    }

    /**
     * @param \Project\Entity\Version\Contact[]|Collections\Collection $versionContact
     *
     * @return Contact
     */
    public function setVersionContact($versionContact): Contact
    {
        $this->versionContact = $versionContact;

        return $this;
    }

    /**
     * @return \Project\Entity\Workpackage\Contact[]|Collections\Collection
     */
    public function getWorkpackageContact()
    {
        return $this->workpackageContact;
    }

    /**
     * @param \Project\Entity\Workpackage\Contact[]|Collections\Collection $workpackageContact
     *
     * @return Contact
     */
    public function setWorkpackageContact($workpackageContact): Contact
    {
        $this->workpackageContact = $workpackageContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\WorkpackageDescription[]|Collections\Collection
     */
    public function getProjectReportWorkpackageDescription()
    {
        return $this->projectReportWorkpackageDescription;
    }

    /**
     * @param Collections\Collection|\Project\Entity\Report\WorkpackageDescription[] $projectReportWorkpackageDescription
     *
     * @return Contact
     */
    public function setProjectReportWorkpackageDescription($projectReportWorkpackageDescription): Contact
    {
        $this->projectReportWorkpackageDescription = $projectReportWorkpackageDescription;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\Parent\Doa[]|Collections\Collection
     */
    public function getParentDoa()
    {
        return $this->parentDoa;
    }

    /**
     * @param Collections\ArrayCollection|\Organisation\Entity\Parent\Doa[]|Collections\Collection $parentDoa
     *
     * @return Contact
     */
    public function setParentDoa($parentDoa): Contact
    {
        $this->parentDoa = $parentDoa;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\OParent[]|Collections\Collection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collections\ArrayCollection|\Organisation\Entity\OParent[]|Collections\Collection $parent
     *
     * @return Contact
     */
    public function setParent($parent): Contact
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\Parent\Financial[]
     */
    public function getParentFinancial()
    {
        return $this->parentFinancial;
    }

    /**
     * @param Collections\ArrayCollection|\Organisation\Entity\Parent\Financial[] $parentFinancial
     *
     * @return Contact
     */
    public function setParentFinancial($parentFinancial): Contact
    {
        $this->parentFinancial = $parentFinancial;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Organisation\Entity\Parent\Organisation[]
     */
    public function getParentOrganisation()
    {
        return $this->parentOrganisation;
    }

    /**
     * @param Collections\ArrayCollection|\Organisation\Entity\Parent\Organisation[] $parentOrganisation
     *
     * @return Contact
     */
    public function setParentOrganisation($parentOrganisation): Contact
    {
        $this->parentOrganisation = $parentOrganisation;

        return $this;
    }

    /**
     * @return Log[]|Collections\Collection
     */
    public function getLogCreatedBy()
    {
        return $this->logCreatedBy;
    }

    /**
     * @param Collections\Collection $logCreatedBy
     *
     * @return Contact
     */
    public function setLogCreatedBy($logCreatedBy): Contact
    {
        $this->logCreatedBy = $logCreatedBy;

        return $this;
    }

    /**
     * @return Log[]|Collections\Collection
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param Collections\Collection $log
     *
     * @return Contact
     */
    public function setLog($log): Contact
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Pca[]
     */
    public function getPca()
    {
        return $this->pca;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Pca[] $pca
     *
     * @return Contact
     */
    public function setPca($pca): Contact
    {
        $this->pca = $pca;

        return $this;
    }

    /**
     * @return \Admin\Entity\Pageview[]|Collections\Collection
     */
    public function getPageview()
    {
        return $this->pageview;
    }

    /**
     * @param \Admin\Entity\Pageview[]|Collections\Collection $pageView
     *
     * @return Contact
     */
    public function setPageview($pageview): Contact
    {
        $this->pageview = $pageview;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Project[]
     */
    public function getProxyProject()
    {
        return $this->proxyProject;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Project[] $proxyProject
     *
     * @return Contact
     */
    public function setProxyProject($proxyProject): Contact
    {
        $this->proxyProject = $proxyProject;
        return $this;
    }

    public function getDateActivated(): ?\DateTime
    {
        return $this->dateActivated;
    }

    public function setDateActivated(?\DateTime $dateActivated): Contact
    {
        $this->dateActivated = $dateActivated;
        return $this;
    }
}
