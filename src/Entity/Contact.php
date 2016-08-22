<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
namespace Contact\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use ZfcUser\Entity\UserInterface;

/**
 * Entity for the Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="Contact\Repository\Contact")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_contact")
 *
 * @category    Contact
 * @package     Entity
 */
class Contact extends EntityAbstract implements ResourceInterface, ProviderInterface, UserInterface
{
    /**
     * Key needed for the encryption and decryption of the Keys
     */
    const HASH_KEY = 'rdkfj43es39f9xv8s9sf9sdwer0cv';
    /**
     * Constant for messenger;
     */
    const MESSENGER_ACTIVE = 1;
    /**
     * Value for messenger
     */
    const MESSENGER_ACTIVE_VALUE = "txt-messenger-active";
    /**
     * Templates for the constant status
     *
     * @var array
     */
    protected $messengerTemplates
        = [
            self::MESSENGER_ACTIVE => self::MESSENGER_ACTIVE_VALUE,
        ];
    /**
     * @ORM\Column(name="contact_id", length=10, type="integer", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Zend\Form\Element\Hidden")
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="firstname", type="string", length=40, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-first-name"})
     * @var string
     */
    private $firstName;
    /**
     * @ORM\Column(name="middlename", type="string", length=20, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-middle-name"})
     * @var string
     */
    private $middleName;
    /**
     * @ORM\Column(name="lastname", type="string", length=40, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-last-name"})
     * @var string
     */
    private $lastName;
    /**
     * @ORM\Column(name="email",type="string",length=60,nullable=false, unique=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-email"})
     * @var string
     */
    private $email;
    /**
     * @ORM\Column(name="password", type="string", length=40, nullable=true)
     * @Annotation\Exclude()
     * @var string
     */
    private $password;
    /**
     * @ORM\Column(name="salted_password", type="string", length=100, nullable=true)
     * @Annotation\Exclude()
     * @var string
     */
    private $saltedPassword;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Gender", cascade={"persist"}, inversedBy="contacts")
     * @ORM\JoinColumn(name="gender_id", referencedColumnName="gender_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Gender"})
     * @Annotation\Attributes({"label":"txt-attention"})
     * @var \General\Entity\Gender
     */
    private $gender;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Title", cascade={"persist"}, inversedBy="contacts")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="title_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Title"})
     * @Annotation\Attributes({"label":"txt-title"})
     * @var \General\Entity\Title
     */
    private $title;
    /**
     * @ORM\Column(name="position", type="string", length=60, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-position"})
     * @var string
     */
    private $position;
    /**
     * @ORM\Column(name="department", type="string", length=80, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-department"})
     * @var string
     */
    private $department;
    /**
     * @ORM\Column(name="date_birth", type="date", nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-date-of-birth"})
     * @var \DateTime
     */
    private $dateOfBirth;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     * @var \datetime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \datetime
     */
    private $lastUpdate;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var \datetime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="messenger", type="smallint", nullable=false)
     * @Annotation\Exclude()
     * @var int
     */
    private $messenger;
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
     *          "name":"findBy",
     *          "params": {
     *              "criteria":{},
     *              "orderBy":{
     *                  "access":"ASC"}
     *              }
     *          }
     *      }
     * )
     * @Annotation\Attributes({"label":"txt-access","help-block":"txt-access-help-block"})
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
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Web", cascade={"persist"}, mappedBy="contact")
     * Annotation\ComposedObject("\Contact\Entity\Web")
     *
     * @var \Contact\Entity\Web[]|Collections\ArrayCollection
     */
    private $web;
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
     * @var \Project\Entity\Document\Document[]\Document|Collections\ArrayCollection
     */
    private $projectDocument;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Dnd", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Dnd|Collections\ArrayCollection
     */
    private $dnd;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Nda", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Nda|Collections\ArrayCollection
     */
    private $nda;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\RoadmapLog", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\RoadmapLog|Collections\ArrayCollection
     */
    private $roadmapLog;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Doa|Collections\ArrayCollection
     */
    private $programDoa;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\OpenId", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\OpenId|Collections\ArrayCollection
     */
    private $openId;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\ContactOrganisation", cascade={"persist"}, mappedBy="contact", fetch="EXTRA_LAZY")
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
     * @ORM\OneToMany(targetEntity="Partner\Entity\Partner", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Partner\Entity\Partner[]|Collections\ArrayCollection
     */
    private $partner;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude();
     * @var \Partner\Entity\Financial[]|Collections\ArrayCollection
     */
    private $partnerFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Organisation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Partner\Entity\Organisation[]|Collections\ArrayCollection
     */
    private $partnerOrganisation;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Affiliation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Partner\Entity\Affiliation[]|Collections\ArrayCollection
     */
    private $partnerAffiliation;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Affiliation\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Partner\Entity\Affiliation\Log[]|Collections\ArrayCollection
     */
    private $partnerAffiliationLog;
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
     * @ORM\OneToMany(targetEntity="Deeplink\Entity\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Deeplink\Entity\Contact|Collections\ArrayCollection
     */
    private $deeplinkContact;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\Profile", cascade={"persist"}, mappedBy="contact",  orphanRemoval=true)
     * @Annotation\Exclude()
     * @var \Contact\Entity\Profile
     */
    private $profile;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Community", cascade={"persist"}, mappedBy="contact", orphanRemoval=true)
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
     * @ORM\OneToMany(targetEntity="Calendar\Entity\ScheduleContact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Calendar\Entity\ScheduleContact[]|Collections\ArrayCollection
     */
    private $scheduleContact;
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
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Doa[]|Collections\ArrayCollection
     */
    private $affiliationDoa;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Permit\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Admin\Entity\Permit\Contact[]|Collections\ArrayCollection
     */
    private $permitContact;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Session", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Admin\Entity\Session[]|Collections\ArrayCollection
     */
    private $session;
    /**
     * @ORM\OneToMany(targetEntity="Partner\Entity\Applicant", cascade={"persist"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     * @var \Partner\Entity\Applicant[]|Collections\ArrayCollection
     */
    private $applicant;
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
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\Process", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\ChangeRequest\Process
     */
    private $changerequestProcess;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\CostChange", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\ChangeRequest\CostChange
     */
    private $changerequestCostChange;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\Country", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\ChangeRequest\Country
     */
    private $changerequestCountry;
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
     * Class constructor
     */
    public function __construct()
    {
        $this->project = new Collections\ArrayCollection();
        $this->projectVersion = new Collections\ArrayCollection();
        $this->projectDescription = new Collections\ArrayCollection();
        $this->projectDocument = new Collections\ArrayCollection();
        $this->web = new Collections\ArrayCollection();
        $this->roadmapLog = new Collections\ArrayCollection();
        $this->address = new Collections\ArrayCollection();
        $this->phone = new Collections\ArrayCollection();
        $this->emailAddress = new Collections\ArrayCollection();
        $this->access = new Collections\ArrayCollection();
        $this->optIn = new Collections\ArrayCollection();
        $this->domain = new Collections\ArrayCollection();
        $this->technology = new Collections\ArrayCollection();
        $this->dnd = new Collections\ArrayCollection();
        $this->nda = new Collections\ArrayCollection();
        $this->programDoa = new Collections\ArrayCollection();
        $this->domain = new Collections\ArrayCollection();
        $this->technology = new Collections\ArrayCollection();
        $this->openId = new Collections\ArrayCollection();
        $this->rationale = new Collections\ArrayCollection();
        $this->organisationLog = new Collections\ArrayCollection();
        $this->affiliationLog = new Collections\ArrayCollection();
        $this->affiliationDescription = new Collections\ArrayCollection();
        $this->projectLog = new Collections\ArrayCollection();
        $this->affiliation = new Collections\ArrayCollection();
        $this->partner = new Collections\ArrayCollection();
        $this->partnerFinancial = new Collections\ArrayCollection();
        $this->partnerAffiliationLog = new Collections\ArrayCollection();
        $this->partnerAffiliation = new Collections\ArrayCollection();
        $this->partnerOrganisation = new Collections\ArrayCollection();
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
        $this->boothFinancial = new Collections\ArrayCollection();
        $this->selection = new Collections\ArrayCollection();
        $this->selectionContact = new Collections\ArrayCollection();
        $this->mailingContact = new Collections\ArrayCollection();
        $this->mailing = new Collections\ArrayCollection();
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
        $this->scheduleContact = new Collections\ArrayCollection();
        $this->projectReview = new Collections\ArrayCollection();
        $this->projectVersionReview = new Collections\ArrayCollection();
        $this->projectReport = new Collections\ArrayCollection();
        $this->projectReportItem = new Collections\ArrayCollection();
        $this->projectReportWorkpackageDescription = new Collections\ArrayCollection();
        $this->projectCalendarReview = new Collections\ArrayCollection();
        $this->projectReportReview = new Collections\ArrayCollection();
        $this->invite = new Collections\ArrayCollection();
        $this->inviteContact = new Collections\ArrayCollection();
        $this->loi = new Collections\ArrayCollection();
        $this->affiliationDoa = new Collections\ArrayCollection();
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
        $this->changerequestProcess = new  Collections\ArrayCollection();
        $this->changerequestCostChange = new  Collections\ArrayCollection();
        $this->changerequestCountry = new  Collections\ArrayCollection();
        $this->versionContact = new  Collections\ArrayCollection();
        $this->workpackageContact = new  Collections\ArrayCollection();
        /**
         * Set these values for legacy reasons
         */
        $this->messenger = self::MESSENGER_ACTIVE;
    }

    /**
     * Although an alternative does not have a clear hash, we can create one based on the id;
     * Don't use the elements from underlying objects since this gives confusion
     *
     * @return string
     */
    public function parseHash()
    {
        return hash('sha1', $this->id . self::HASH_KEY);
    }

    /**
     * Magic Getter
     *
     * @param $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic Setter
     *
     * @param $property
     * @param $value
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * toString returns the id (for form population)
     * Revert to the contactService to have the full parsed name
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * Proxythe full name of a project.
     *
     * @return string
     */
    public function parseFullName()
    {
        return $this->getDisplayName();
    }

    /**
     * Returns the string identifier of the Role.
     * We return the access here since that entity keeps the access roles
     *
     * We return only the name of the roles as this is sufficient
     *
     * @return array
     */
    public function getRoles()
    {
        $accessRoles = ['user'];
        foreach ($this->access as $access) {
            $accessRoles[] = strtolower($access->getAccess());
        }

        return $accessRoles;
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $optInCollection
     */
    public function addOptIn(Collections\Collection $optInCollection)
    {
        foreach ($optInCollection as $optIn) {
            $this->optIn->add($optIn);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $optInCollection
     */
    public function removeOptIn(Collections\Collection $optInCollection)
    {
        foreach ($optInCollection as $optIn) {
            $this->optIn->removeElement($optIn);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $accessCollection
     */
    public function addAccess(Collections\Collection $accessCollection)
    {
        foreach ($accessCollection as $access) {
            $this->access->add($access);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $accessCollection
     */
    public function removeAccess(Collections\Collection $accessCollection)
    {
        foreach ($accessCollection as $single) {
            $this->access->removeElement($single);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $addressCollection
     */
    public function addAddress(Collections\Collection $addressCollection)
    {
        foreach ($addressCollection as $address) {
            $this->address->add($address);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $addressCollection
     */
    public function removeAddress(Collections\Collection $addressCollection)
    {
        foreach ($addressCollection as $single) {
            $this->address->removeElement($single);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $phoneCollection
     */
    public function addPhone(Collections\Collection $phoneCollection)
    {
        foreach ($phoneCollection as $phone) {
            $this->phone->add($phone);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $phoneCollection
     */
    public function removePhone(Collections\Collection $phoneCollection)
    {
        foreach ($phoneCollection as $single) {
            $this->phone->removeElement($single);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $photoCollection
     */
    public function addPhoto(Collections\Collection $photoCollection)
    {
        foreach ($photoCollection as $photo) {
            $this->photo->add($photo);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $photoCollection
     */
    public function removePhoto(Collections\Collection $photoCollection)
    {
        foreach ($photoCollection as $single) {
            $this->photo->removeElement($single);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $communityCollection
     */
    public function addCommunity(Collections\Collection $communityCollection)
    {
        foreach ($communityCollection as $community) {
            $this->community->add($community);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $communityCollection
     */
    public function removeCommunity(Collections\Collection $communityCollection)
    {
        foreach ($communityCollection as $single) {
            $this->community->removeElement($single);
        }
    }

    /**
     * @param \datetime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \datetime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $email
     *
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return \General\Entity\Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param \General\Entity\Gender $gender
     *
     * @return Contact
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }


    /**
     * @param  int $id
     *
     * @return void|UserInterface
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param \datetime $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return \datetime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param int $messenger
     */
    public function setMessenger($messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * @return int
     */
    public function getMessenger()
    {
        return $this->messenger;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $password
     *
     * @return Contact
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $saltedPassword
     */
    public function setSaltedPassword($saltedPassword)
    {
        $this->saltedPassword = $saltedPassword;
    }

    /**
     * @return string
     */
    public function getSaltedPassword()
    {
        return $this->saltedPassword;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return \General\Entity\Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param \General\Entity\Title $title
     *
     * @return Contact
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }


    /**
     * @param  int $state
     *
     * @return null|UserInterface
     */
    public function setState($state)
    {
        return;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->email = $username;
    }

    /**
     * Get displayName, or the emailaddress
     *
     * @return string
     */
    public function getDisplayName()
    {
        $name = sprintf("%s %s", $this->firstName, trim(implode(' ', [$this->middleName, $this->lastName])));

        return !empty(trim($name)) ? $name : $this->email;
    }

    /**
     * Get displayName, or the emailaddress
     *
     * @return string
     */
    public function getShortName()
    {
        $name = sprintf(
            "%s. %s",
            substr($this->firstName, 0, 1),
            trim(implode(' ', [$this->middleName, $this->lastName]))
        );

        return !empty($name) ? $name : $this->email;
    }

    /**
     * Get displayName, or the email address
     *
     * @return string
     */
    public function getFormName()
    {
        $name = sprintf("%s, %s", trim(implode(' ', [$this->middleName, $this->lastName])), $this->firstName);

        return !empty($name) ? $name : $this->email;
    }

    /**
     * Although an alternative does not have a clear hash, we can create one based on the id;
     * Don't use the elements from underlying objects since this gives confusion.
     *
     * @return string
     */
    public function getHash()
    {
        return hash('sha512', $this->id . self::HASH_KEY);
    }

    /**
     * @param string $displayName
     *
     * @return boolean
     */
    public function setDisplayName($displayName)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param  string $department
     *
     * @return Contact
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }


    /**
     * @return \Admin\Entity\Access[]|Collections\ArrayCollection
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param  \Admin\Entity\Access[]|Collections\ArrayCollection $access
     *
     * @return Contact
     */
    public function setAccess($access)
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
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return CV
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param  CV $cv
     *
     * @return Contact
     */
    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * @return Address|Collections\ArrayCollection
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
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Phone|Collections\ArrayCollection
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
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Web|Collections\ArrayCollection
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param  Web|Collections\ArrayCollection $web
     *
     * @return Contact
     */
    public function setWeb($web)
    {
        $this->web = $web;

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
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Project $project
     *
     * @return Contact
     */
    public function setProject($project)
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
    public function setRationale($rationale)
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
    public function setProjectDescription($projectDescription)
    {
        $this->projectDescription = $projectDescription;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Version\Version
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
    public function setProjectVersion($projectVersion)
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
    public function setProjectDocument($projectDocument)
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
    public function setDnd($dnd)
    {
        $this->dnd = $dnd;

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
    public function setNda($nda)
    {
        $this->nda = $nda;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Program\Entity\RoadmapLog
     */
    public function getRoadmapLog()
    {
        return $this->roadmapLog;
    }

    /**
     * @param  Collections\ArrayCollection|\Program\Entity\RoadmapLog $roadmapLog
     *
     * @return Contact
     */
    public function setRoadmapLog($roadmapLog)
    {
        $this->roadmapLog = $roadmapLog;

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
    public function setProgramDoa($programDoa)
    {
        $this->programDoa = $programDoa;

        return $this;
    }

    /**
     * @return OpenId|Collections\ArrayCollection
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * @param  OpenId|Collections\ArrayCollection $openId
     *
     * @return Contact
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;

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
    public function setContactOrganisation($contactOrganisation)
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
    public function setDomain($domain)
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
    public function setIdea($idea)
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
    public function setFavouriteIdea($favouriteIdea)
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
    public function setTechnology($technology)
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
    public function setOrganisationLog($organisationLog)
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
    public function setAffiliation($affiliation)
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
    public function setAffiliationLog($affiliationLog)
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
    public function setFinancial($financial)
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
    public function setAffiliationDescription($affiliationDescription)
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
    public function setAffiliationVersion($affiliationVersion)
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
    public function setInvoice($invoice)
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
    public function setPublication($publication)
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
    public function setPublicationDownload($publicationDownload)
    {
        $this->publicationDownload = $publicationDownload;

        return $this;
    }

    /**
     * Find the photo. We need to apply a trick here since the photo has a 1:n relation in the entities to avoid
     * the eager loading of the BLOB but we know that we only have 1 photo
     *
     * @return \Contact\Entity\Photo|Collections\ArrayCollection
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
    public function setPhoto($photo)
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
    public function setAssociate($associate)
    {
        $this->associate = $associate;

        return $this;
    }

    /**
     * @return \Program\Entity\Funder
     */
    public function getFunder()
    {
        return $this->funder;
    }

    /**
     * @param  \Program\Entity\Funder $funder
     *
     * @return Contact
     */
    public function setFunder($funder)
    {
        $this->funder = $funder;

        return $this;
    }

    /**
     * @return \Deeplink\Entity\Contact|Collections\ArrayCollection
     */
    public function getDeeplinkContact()
    {
        return $this->deeplinkContact;
    }

    /**
     * @param  \Deeplink\Entity\Contact|Collections\ArrayCollection $deeplinkContact
     *
     * @return Contact
     */
    public function setDeeplinkContact($deeplinkContact)
    {
        $this->deeplinkContact = $deeplinkContact;

        return $this;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param  Profile $profile
     *
     * @return Contact
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Community|Collections\ArrayCollection
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
    public function setCommunity($community)
    {
        $this->community = $community;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Registration
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
    public function setRegistration($registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Badge\Badge
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
    public function setBadge($badge)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Event\Entity\Badge\Contact
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
    public function setBadgeContact($badgeContact)
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
    public function setBoothContact($boothContact)
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
    public function setProjectBooth($projectBooth)
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
    public function setOrganisationBooth($organisationBooth)
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
    public function setBoothFinancial($boothFinancial)
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
    public function setNote($note)
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
    public function setSelection($selection)
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
    public function setSelectionContact($selectionContact)
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
    public function setMailingContact($mailingContact)
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
    public function setMailing($mailing)
    {
        $this->mailing = $mailing;

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
    public function setResult($result)
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
    public function setWorkpackage($workpackage)
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
    public function setWorkpackageDocument($workpackageDocument)
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
    public function setIdeaMessage($ideaMessage)
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
    public function setEvaluation($evaluation)
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
    public function setCalendar($calendar)
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
    public function setCalendarContact($calendarContact)
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
    public function setCalendarDocument($calendarDocument)
    {
        $this->calendarDocument = $calendarDocument;

        return $this;
    }

    /**
     * @return \Calendar\Entity\ScheduleContact|Collections\ArrayCollection
     */
    public function getScheduleContact()
    {
        return $this->scheduleContact;
    }

    /**
     * @param  \Calendar\Entity\ScheduleContact|Collections\ArrayCollection $scheduleContact
     *
     * @return Contact
     */
    public function setScheduleContact($scheduleContact)
    {
        $this->scheduleContact = $scheduleContact;

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
    public function setProjectReview($projectReview)
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
     * @return Contact
     */
    public function setProjectReviewContact($projectReviewContact)
    {
        $this->projectReviewContact = $projectReviewContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Review\VersionReview
     */
    public function getProjectVersionReview()
    {
        return $this->projectVersionReview;
    }

    /**
     * @param  Collections\ArrayCollection|\Project\Entity\Review\VersionReview $projectVersionReview
     *
     * @return Contact
     */
    public function setProjectVersionReview($projectVersionReview)
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
    public function setProjectReport($projectReport)
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
    public function setProjectCalendarReview($projectCalendarReview)
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
     * @return Contact
     */
    public function setProjectReportReview($projectReportReview)
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
    public function setInvite($invite)
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
    public function setInviteContact($inviteContact)
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
    public function setIdeaInvite($ideaInvite)
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
    public function setIdeaInviteContact($ideaInviteContact)
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
    public function setLoi($loi)
    {
        $this->loi = $loi;

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
    public function setAffiliationDoa($affiliationDoa)
    {
        $this->affiliationDoa = $affiliationDoa;

        return $this;
    }

    /**
     * @return \Admin\Entity\Permit\Contact
     */
    public function getPermitContact()
    {
        return $this->permitContact;
    }

    /**
     * @param  \Admin\Entity\Permit\Contact $permitContact
     *
     * @return Contact
     */
    public function setPermitContact($permitContact)
    {
        $this->permitContact = $permitContact;

        return $this;
    }

    /**
     * @return \Admin\Entity\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param  \Admin\Entity\Session $session
     *
     * @return Contact
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return \Partner\Entity\Applicant|Collections\ArrayCollection
     */
    public function getApplicant()
    {
        return $this->applicant;
    }

    /**
     * @param  \Partner\Entity\Applicant $applicant
     *
     * @return Contact
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;

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
    public function setVoter($voter)
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
    public function setTour($tour)
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
    public function setTourContact($tourContact)
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
    public function setDoaReminderReceiver($doaReminderReceiver)
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
    public function setDoaReminderSender($doaReminderSender)
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
    public function setLoiReminderReceiver($loiReminderReceiver)
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
    public function setLoiReminderSender($loiReminderSender)
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
    public function setBlog($blog)
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
    public function setBlogMessage($blogMessage)
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
    public function setJournalEntry($journalEntry)
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
    public function setJournal($journal)
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
    public function setOrganisationJournal($organisationJournal)
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
    public function setInvoiceLog($invoiceLog)
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
    public function setReminder($reminder)
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
    public function setAchievement($achievement)
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
    public function setIdeaPartner($ideaPartner)
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
    public function setIdeaMessageBoard($ideaMessageBoard)
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
    public function setProjectLog($projectLog)
    {
        $this->projectLog = $projectLog;

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
    public function setProjectReportItem($projectReportItem)
    {
        $this->projectReportItem = $projectReportItem;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\Item[]
     */
    public function getProjectReportEffortSpent()
    {
        return $this->projectReportEffortSpent;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Report\Item[] $projectReportEffortSpent
     *
     * @return Contact
     */
    public function setProjectReportEffortSpent($projectReportEffortSpent)
    {
        $this->projectReportEffortSpent = $projectReportEffortSpent;

        return $this;
    }

    /**
     * @param  Collections\ArrayCollection|\Partner\Entity\Financial[] $partnerFinancial
     *
     * @return Contact
     */
    public function setPartnerFinancial($partnerFinancial)
    {
        $this->partnerFinancial = $partnerFinancial;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Financial[] $partnerFinancial
     */
    public function getPartnerFinancial()
    {
        return $this->partnerFinancial;
    }

    /**
     * @return \Project\Entity\ChangeRequest\Process
     */
    public function getChangeRequestProcess()
    {
        return $this->changerequestProcess;
    }

    /**
     * @param \Project\Entity\ChangeRequest\Process $changerequestProcess
     *
     * @return Contact
     */
    public function setChangeRequestProcess($changerequestProcess)
    {
        $this->changerequestProcess = $changerequestProcess;

        return $this;
    }

    /**
     * @return \Project\Entity\ChangeRequest\CostChange
     */
    public function getChangeRequestCostChange()
    {
        return $this->changerequestCostChange;
    }

    /**
     * @param \Project\Entity\ChangeRequest\CostChange $changerequestCostChange
     *
     * @return Contact
     */
    public function setChangeRequestCostChange($changerequestCostChange)
    {
        $this->changerequestCostChange = $changerequestCostChange;

        return $this;
    }

    /**
     * @return \Project\Entity\ChangeRequest\Country
     */
    public function getChangeRequestCountry()
    {
        return $this->changerequestCountry;
    }

    /**
     * @param \Project\Entity\ChangeRequest\Country $changerequestCountry
     */
    public function setChangeRequestCountry($changerequestCountry)
    {
        $this->changerequestCountry = $changerequestCountry;
    }

    /**
     * @return \Project\Entity\Version\Contact
     */
    public function getVersionContact()
    {
        return $this->versionContact;
    }

    /**
     * @param \Project\Entity\Version\Contact $versionContact
     *
     * @return Contact
     */
    public function setVersionContact($versionContact)
    {
        $this->versionContact = $versionContact;

        return $this;
    }

    /**
     * @return \Project\Entity\Workpackage\Contact
     */
    public function getWorkpackageContact()
    {
        return $this->workpackageContact;
    }

    /**
     * @param \Project\Entity\Workpackage\Contact $workpackageContact
     *
     * @return Contact
     */
    public function setWorkpackageContact($workpackageContact)
    {
        $this->workpackageContact = $workpackageContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Affiliation[]
     */
    public function getPartnerAffiliation()
    {
        return $this->partnerAffiliation;
    }

    /**
     * @param Collections\ArrayCollection|\Partner\Entity\Affiliation[] $partnerAffiliation
     *
     * @return Contact
     */
    public function setPartnerAffiliation($partnerAffiliation)
    {
        $this->partnerAffiliation = $partnerAffiliation;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Affiliation\Log[]
     */
    public function getPartnerAffiliationLog()
    {
        return $this->partnerAffiliationLog;
    }

    /**
     * @param Collections\ArrayCollection|\Partner\Entity\Affiliation\Log[] $partnerAffiliationLog
     *
     * @return Contact
     */
    public function setPartnerAffiliationLog($partnerAffiliationLog)
    {
        $this->partnerAffiliationLog = $partnerAffiliationLog;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Organisation[]
     */
    public function getPartnerOrganisation()
    {
        return $this->partnerOrganisation;
    }

    /**
     * @param Collections\ArrayCollection|\Partner\Entity\Organisation[] $partnerOrganisation
     *
     * @return Contact
     */
    public function setPartnerOrganisation($partnerOrganisation)
    {
        $this->partnerOrganisation = $partnerOrganisation;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Partner\Entity\Partner[]
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Collections\ArrayCollection|\Partner\Entity\Partner[] $partner
     *
     * @return Contact
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|\Project\Entity\Report\WorkpackageDescription[]
     */
    public function getProjectReportWorkpackageDescription()
    {
        return $this->projectReportWorkpackageDescription;
    }

    /**
     * @param Collections\ArrayCollection|\Project\Entity\Report\WorkpackageDescription[] $projectReportWorkpackageDescription
     * @return Contact
     */
    public function setProjectReportWorkpackageDescription($projectReportWorkpackageDescription)
    {
        $this->projectReportWorkpackageDescription = $projectReportWorkpackageDescription;

        return $this;
    }
}
