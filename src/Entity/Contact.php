<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use Admin\Entity\Access;
use Admin\Entity\Pageview;
use Admin\Entity\Session;
use Affiliation\Entity\Affiliation;
use Affiliation\Entity\Description;
use Affiliation\Entity\DoaReminder;
use Affiliation\Entity\Loi;
use Affiliation\Entity\Version;
use BjyAuthorize\Provider\Role\ProviderInterface;
use Calendar\Entity\Calendar;
use Calendar\Entity\Document;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Evaluation\Entity\Evaluation;
use Event\Entity\Badge\Badge;
use Event\Entity\Exhibition\Tour;
use Event\Entity\Exhibition\Voter;
use Event\Entity\Registration;
use Gedmo\Mapping\Annotation as Gedmo;
use General\Entity\EmailMessage;
use General\Entity\Gender;
use General\Entity\Title;
use Invoice\Entity\Invoice;
use Invoice\Entity\Journal;
use Invoice\Entity\Journal\Entry;
use Invoice\Entity\Reminder;
use Laminas\Form\Annotation;
use Laminas\Math\Rand;
use Mailing\Entity\Mailing;
use News\Entity\Blog;
use News\Entity\Magazine\Article;
use News\Entity\Message;
use Organisation\Entity\Booth;
use Organisation\Entity\OParent;
use Organisation\Entity\Parent\Doa;
use Organisation\Entity\Parent\Financial;
use Organisation\Entity\Parent\Organisation;
use Program\Entity\Funder;
use Program\Entity\Nda;
use Project\Entity\Achievement;
use Project\Entity\Action;
use Project\Entity\Action\Comment;
use Project\Entity\Changelog;
use Project\Entity\ChangeRequest\CostChange;
use Project\Entity\ChangeRequest\Country;
use Project\Entity\ChangeRequest\Process;
use Project\Entity\Contract;
use Project\Entity\Idea\Idea;
use Project\Entity\Idea\Invite;
use Project\Entity\Idea\Meeting\Participant;
use Project\Entity\Idea\Partner;
use Project\Entity\Pca;
use Project\Entity\Project;
use Project\Entity\Rationale;
use Project\Entity\Report\EffortSpent;
use Project\Entity\Report\Item;
use Project\Entity\Report\Report;
use Project\Entity\Report\Reviewer;
use Project\Entity\Report\WorkpackageDescription;
use Project\Entity\Result\Result;
use Project\Entity\Workpackage\Workpackage;
use Publication\Entity\Download;
use Publication\Entity\Publication;
use ZfcUser\Entity\UserInterface;

use function explode;
use function in_array;
use function strtolower;
use function substr;

/**
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="Contact\Repository\Contact")
 * @Annotation\Name("contact_contact")
 */
class Contact extends AbstractEntity implements ProviderInterface, UserInterface
{
    /**
     * Key needed for the encryption and decryption of the Keys
     */
    public const HASH_KEY = 'rdkfj43es39f9xv8s9sf9sdwer0cv';

    /**
     * @ORM\Column(name="contact_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Type("\Laminas\Form\Element\Hidden")
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="firstname", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-first-name-label","help-block":"txt-contact-first-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-first-name-placeholder"})
     * @var string
     */
    private $firstName;
    /**
     * @ORM\Column(name="middlename", type="string",nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-middle-name-label","help-block":"txt-contact-middle-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-middle-name-placeholder"})
     * @var string
     */
    private $middleName;
    /**
     * @ORM\Column(name="lastname", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-last-name-label","help-block":"txt-contact-last-name-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-last-name-placeholder"})
     * @var string
     */
    private $lastName;
    /**
     * @ORM\Column(name="email",type="string",nullable=false, unique=true)
     * @Annotation\Type("\Laminas\Form\Element\Email")
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
     * @var Gender
     */
    private $gender;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Title", cascade={"persist"}, inversedBy="contacts")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="title_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Title"})
     * @Annotation\Options({"label":"txt-contact-title-label","help-block":"txt-contact-title-help-block"})
     * @var Title
     */
    private $title;
    /**
     * @ORM\Column(name="position", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-position-label","help-block":"txt-contact-position-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-position-placeholder"})
     * @var string
     */
    private $position;
    /**
     * @ORM\Column(name="department", type="string", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-contact-department-label","help-block":"txt-contact-department-help-block"})
     * @Annotation\Attributes({"placeholder":"txt-contact-department-placeholder"})
     * @var string
     */
    private $department;
    /**
     * @ORM\Column(name="date_birth", type="date", nullable=true)
     * @Annotation\Type("\Laminas\Form\Element\Date")
     * @Annotation\Options({"label":"txt-contact-date-of-birth-label","help-block":"txt-contact-date-of-birth-help-block"})
     * @var DateTime
     */
    private $dateOfBirth;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Exclude()
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var DateTime
     */
    private $lastUpdate;
    /**
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var DateTime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="date_anonymous", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var DateTime
     */
    private $dateAnonymous;
    /**
     * @ORM\Column(name="date_activated", type="datetime", nullable=true)
     * @Annotation\Exclude()
     * @var DateTime
     */
    private $dateActivated;
    /**
     * @ORM\Column(name="hash", type="string", nullable=true)
     * @Annotation\Exclude()
     * @var string|null
     */
    private $hash;
    /**
     * @ORM\Column(name="trigger_update",type="boolean")
     * @Annotation\Exclude()
     * @var bool
     */
    private $triggerUpdate;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Access", cascade={"persist"}, inversedBy="contact", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="contact_access",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="access_id", referencedColumnName="access_id")}
     * )
     * @ORM\OrderBy({"access"="ASC"})
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
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
     * @Annotation\Options({"help-block":"txt-contact-access-help-block"})
     * @Annotation\Attributes({"label":"txt-contact-access-label"})
     * @Annotation\Attributes({"multiple":true,"data-actions-box":true})
     * @var Access[]|Collections\ArrayCollection
     */
    private $access;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Email", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Email[]|Collections\ArrayCollection
     */
    private $emailAddress;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Address", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @var Address[]|Collections\ArrayCollection
     */
    private $address;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Phone", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @var Phone[]|Collections\ArrayCollection
     */
    private $phone;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\OptIn", cascade={"persist"},inversedBy="contact")
     * @ORM\JoinTable(name="contact_optin",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="optin_id", referencedColumnName="optin_id")}
     * )
     * @Annotation\Exclude()
     * @var OptIn[]|Collections\ArrayCollection
     */
    private $optIn;
    /**
     * @ORM\OneToMany(targetEntity="Quality\Entity\Action", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Quality\Entity\Action[]|Collections\ArrayCollection
     */
    private $qualityActions;
    /**
     * @ORM\OneToMany(targetEntity="Quality\Entity\Result", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Quality\Entity\Result[]|Collections\ArrayCollection
     */
    private $qualityResults;
    /**
     * @ORM\OneToMany(targetEntity="Quality\Entity\Result", cascade={"persist"}, mappedBy="contactComment")
     * @Annotation\Exclude()
     * @var \Quality\Entity\Result[]|Collections\ArrayCollection
     */
    private $qualityResultComments;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Project", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Project[]|Collections\ArrayCollection
     */
    private $project;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Project", cascade={"persist"}, mappedBy="proxyContact")
     * @Annotation\Exclude()
     * @var Project[]|Collections\ArrayCollection
     */
    private $proxyProject;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Rationale", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Rationale[]|Collections\ArrayCollection
     */
    private $rationale;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Description\Description", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Description\Description[]|Collections\ArrayCollection
     */
    private $projectDescription;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Version\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Version\Version[]|Collections\ArrayCollection
     */
    private $projectVersion;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\Item", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Item[]|Collections\ArrayCollection
     */
    private $projectReportItem;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\WorkpackageDescription", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var WorkpackageDescription[]|Collections\ArrayCollection
     */
    private $projectReportWorkpackageDescription;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\EffortSpent", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Item[]|Collections\ArrayCollection
     */
    private $projectReportEffortSpent;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Document\Document", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Document\Document[]|Collections\ArrayCollection
     */
    private $projectDocument;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Dnd", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Dnd|Collections\ArrayCollection
     */
    private $dnd;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Contract", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Contract[]|Collections\ArrayCollection
     */
    private $contract;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Contract\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Contract[]|Collections\ArrayCollection
     */
    private $contractVersion;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Nda", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Nda[]|Collections\ArrayCollection
     */
    private $nda;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Nda", cascade={"persist"}, mappedBy="approver")
     * @Annotation\Exclude()
     * @var Nda[]|Collections\ArrayCollection
     */
    private $ndaApprover;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Doa|Collections\ArrayCollection
     */
    private $programDoa;
    /**
     * @ORM\OneToMany(targetEntity="\Organisation\Entity\Parent\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Doa[]|Collections\ArrayCollection
     */
    private $parentDoa;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\ContactOrganisation", cascade={"persist","remove"}, orphanRemoval=true, mappedBy="contact")
     * @var ContactOrganisation
     */
    private $contactOrganisation;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Action", cascade={"persist"}, mappedBy="contactClosed")
     * @Annotation\Exclude()
     * @var Action[]|Collections\ArrayCollection
     */
    private $actionClosed;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Action", cascade={"persist"}, mappedBy="contactStatus")
     * @Annotation\Exclude()
     * @var Action[]|Collections\ArrayCollection
     */
    private $actionStatus;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Action\Comment", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Comment[]|Collections\ArrayCollection
     */
    private $actionComment;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Idea[]|Collections\ArrayCollection
     */
    private $idea;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Idea\Idea", cascade={"persist"}, mappedBy="favourite")
     * @Annotation\Exclude()
     * @var Idea[]|Collections\ArrayCollection
     */
    private $favouriteIdea;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Log[]|Collections\ArrayCollection
     */
    private $organisationLog;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Partner", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Partner[]|Collections\ArrayCollection
     */
    private $ideaPartner;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Meeting\Participant", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Participant[]|Collections\ArrayCollection
     */
    private $ideaMeetingParticipant;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Affiliation[]|Collections\ArrayCollection
     */
    private $affiliation;
    /**
     * @ORM\ManyToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="proxyContact")
     * @Annotation\Exclude()
     * @var Affiliation[]|Collections\ArrayCollection
     */
    private $proxyAffiliation;
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
     * @var Description|Collections\ArrayCollection
     */
    private $affiliationDescription;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Version[]|Collections\ArrayCollection
     */
    private $affiliationVersion;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Invoice", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Invoice[]|Collections\ArrayCollection
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\OParent", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var OParent[]|Collections\ArrayCollection
     */
    private $parent;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude();
     * @var Financial[]|Collections\ArrayCollection
     */
    private $parentFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Parent\Organisation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Organisation[]|Collections\ArrayCollection
     */
    private $parentOrganisation;
    /**
     * @ORM\OneToMany(targetEntity="Publication\Entity\Publication", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Publication[]|Collections\ArrayCollection
     */
    private $publication;
    /**
     * @ORM\OneToMany(targetEntity="Publication\Entity\Download", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Download[]|Collections\ArrayCollection
     */
    private $publicationDownload;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Photo", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var Photo|Collections\ArrayCollection
     */
    private $photo;
    /**
     * @ORM\ManyToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="associate")
     * @Annotation\Exclude()
     * @var Affiliation[]|Collections\ArrayCollection
     */
    private $associate;
    /**
     * @ORM\OneToOne(targetEntity="Program\Entity\Funder", cascade={"persist"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var Funder
     */
    private $funder;
    /**
     * @ORM\OneToMany(targetEntity="Deeplink\Entity\Contact", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Deeplink\Entity\Contact|Collections\ArrayCollection
     */
    private $deeplinkContact;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Profile", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var Profile
     */
    private $profile;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Registration", cascade={"persist"}, mappedBy="contact")
     * @ORM\OrderBy({"id" = "DESC"})
     * @Annotation\Exclude()
     * @var Registration|Collections\ArrayCollection
     */
    private $registration;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Badge\Badge", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Badge[]|Collections\ArrayCollection
     */
    private $badge;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Badge\Contact", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Badge\Contact[]|Collections\ArrayCollection
     */
    private $badgeContact;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Contact", cascade={"persist","remove"}, mappedBy="contact")
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
     * @var Booth[]|Collections\ArrayCollection
     */
    private $organisationBooth;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Booth\Financial[]|Collections\ArrayCollection
     */
    private $boothFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Note", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Note[]|Collections\ArrayCollection
     */
    private $note;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Selection", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Selection[]|Collections\ArrayCollection
     */
    private $selection;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var SelectionContact[]|Collections\ArrayCollection
     */
    private $selectionContact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Contact", cascade={"persist","remove"}, mappedBy="contact", orphanRemoval=true)
     * @Annotation\Exclude()
     * @var \Mailing\Entity\Contact[]|Collections\ArrayCollection
     */
    private $mailingContact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Mailing[]|Collections\ArrayCollection
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
     * @var Result[]|Collections\ArrayCollection
     */
    private $result;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Workpackage\Workpackage", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Workpackage[]|Collections\ArrayCollection
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
     * @ORM\OneToMany(targetEntity="Evaluation\Entity\Evaluation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Evaluation[]|Collections\ArrayCollection
     */
    private $evaluation;
    /**
     * @ORM\OneToMany(targetEntity="Calendar\Entity\Calendar", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Calendar[]|Collections\ArrayCollection
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
     * @var Document[]|Collections\ArrayCollection
     */
    private $calendarDocument;
    /**
     * @ORM\OneToMany(targetEntity="Evaluation\Entity\Reviewer", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Evaluation\Entity\Reviewer[]|Collections\ArrayCollection
     */
    private $projectReviewers;
    /**
     * @ORM\OneToOne(targetEntity="Evaluation\Entity\Reviewer\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var \Evaluation\Entity\Reviewer\Contact
     */
    private $projectReviewerContact;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Version\Reviewer", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Version\Reviewer[]|Collections\Collection
     */
    private $projectVersionReviewers;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\Report", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Report[]|Collections\ArrayCollection
     */
    private $projectReport;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Calendar\Reviewer", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Calendar\Reviewer[]|Collections\ArrayCollection
     */
    private $projectCalendarReviewers;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Report\Reviewer", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Reviewer[]|Collections\ArrayCollection
     */
    private $projectReportReviewers;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Invite", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Invite[]|Collections\ArrayCollection
     */
    private $invite;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Pca", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Pca[]|Collections\ArrayCollection
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
     * @var Invite[]|Collections\ArrayCollection
     */
    private $ideaInvite;
    /**
     * @ORM\ManyToMany(targetEntity="Project\Entity\Idea\Invite", cascade={"persist"}, mappedBy="inviteContact")
     * @Annotation\Exclude()
     * @var Invite[]|Collections\ArrayCollection
     */
    private $ideaInviteContact;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Idea\Meeting\Invite", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Idea\Meeting\Invite[]|Collections\ArrayCollection
     */
    private $ideaMeetingInvite;
    /**
     * @ORM\OneToMany(targetEntity="Program\Entity\Call\Session\Participant", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\Call\Session\Participant[]|Collections\ArrayCollection
     */
    private $callSessionParticipant;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Loi", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Loi[]|Collections\ArrayCollection
     */
    private $loi;
    /**
     * @ORM\OneToMany(targetEntity="\Affiliation\Entity\Loi", cascade={"persist"}, mappedBy="approver")
     * @Annotation\Exclude()
     * @var Loi[]|Collections\ArrayCollection
     */
    private $loiApprover;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Doa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Doa[]|Collections\ArrayCollection
     */
    private $affiliationDoa;
    /**
     * @ORM\OneToMany(targetEntity="\Affiliation\Entity\Doa", cascade={"persist"}, mappedBy="approver")
     * @Annotation\Exclude()
     * @var Loi[]|Collections\ArrayCollection
     */
    private $affiliationDoaApprover;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Permit\Contact", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Admin\Entity\Permit\Contact[]|Collections\ArrayCollection
     */
    private $permitContact;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Session", cascade={"persist","remove"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     * @var Session[]|Collections\ArrayCollection
     */
    private $session;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Exhibition\Voter", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude();
     * @var Voter[]|Collections\ArrayCollection
     */
    private $voter;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Exhibition\Tour", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Tour|Collections\ArrayCollection
     */
    private $tour;
    /**
     * @ORM\ManyToMany(targetEntity="Event\Entity\Exhibition\Tour", cascade={"persist","remove"}, mappedBy="tourContact")
     * @Annotation\Exclude();
     * @var Tour[]|Collections\ArrayCollection
     */
    private $tourContact;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\DoaReminder", cascade={"persist"}, mappedBy="receiver")
     * @Annotation\Exclude();
     * @var DoaReminder[]|Collections\ArrayCollection
     */
    private $doaReminderReceiver;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\DoaReminder", cascade={"persist"}, mappedBy="sender")
     * @Annotation\Exclude();
     * @var DoaReminder[]|Collections\ArrayCollection
     */
    private $doaReminderSender;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Blog", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Blog[]|Collections\ArrayCollection
     */
    private $blog;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Message", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Message[]|Collections\ArrayCollection
     */
    private $blogMessage;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Magazine\Article", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Article[]|Collections\ArrayCollection
     */
    private $magazineArticle;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Magazine\Download", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \News\Entity\Magazine\Download[]|Collections\ArrayCollection
     */
    private $magazineDownload;
    /**
     * @ORM\OneToMany(targetEntity="News\Entity\Magazine\Article\Download", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var Article\Download[]|Collections\ArrayCollection
     */
    private $magazineArticleDownload;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal\Entry", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Entry[]|Collections\ArrayCollection
     */
    private $journalEntry;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Journal[]|Collections\ArrayCollection
     */
    private $journal;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Journal", cascade={"persist"}, mappedBy="organisationContact")
     * @Annotation\Exclude()
     *
     * @var Journal[]|Collections\ArrayCollection
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
     * @var Reminder[]|Collections\ArrayCollection
     */
    private $reminder;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Achievement", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Achievement[]|Collections\ArrayCollection
     */
    private $achievement;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Log", cascade={"persist"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var \Project\Entity\Log[]|Collections\ArrayCollection
     */
    private $projectLog;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\Changelog", cascade={"persist"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var Changelog[]|Collections\ArrayCollection
     */
    private $projectChangelog;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\Process", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Process
     */
    private $changeRequestProcess;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\CostChange", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var CostChange
     */
    private $changeRequestCostChange;
    /**
     * @ORM\OneToMany(targetEntity="Project\Entity\ChangeRequest\Country", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Country
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
     * @var Log[]|Collections\Collection
     */
    private $logCreatedBy;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Log", cascade={"persist","remove"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var Log[]|Collections\Collection
     */
    private $log;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Pageview", cascade={"persist","remove"}, mappedBy="contact", fetch="EXTRA_LAZY")
     * @Annotation\Exclude()
     *
     * @var Pageview[]|Collections\Collection
     */
    private $pageview;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Office\Contact", cascade={"persist","remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Office\Contact
     */
    private $officeContact;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Update", cascade={"persist", "remove"}, mappedBy="contact")
     * @Annotation\Exclude()
     *
     * @var Collections\Collection
     */
    private $organisationUpdates;

    public function __construct()
    {
        $this->project                             = new Collections\ArrayCollection();
        $this->projectVersion                      = new Collections\ArrayCollection();
        $this->projectDescription                  = new Collections\ArrayCollection();
        $this->projectReportEffortSpent            = new Collections\ArrayCollection();
        $this->projectDocument                     = new Collections\ArrayCollection();
        $this->address                             = new Collections\ArrayCollection();
        $this->magazineArticle                     = new Collections\ArrayCollection();
        $this->magazineDownload                    = new Collections\ArrayCollection();
        $this->magazineArticleDownload             = new Collections\ArrayCollection();
        $this->phone                               = new Collections\ArrayCollection();
        $this->emailAddress                        = new Collections\ArrayCollection();
        $this->access                              = new Collections\ArrayCollection();
        $this->optIn                               = new Collections\ArrayCollection();
        $this->dnd                                 = new Collections\ArrayCollection();
        $this->nda                                 = new Collections\ArrayCollection();
        $this->pca                                 = new Collections\ArrayCollection();
        $this->ndaApprover                         = new Collections\ArrayCollection();
        $this->qualityActions                      = new Collections\ArrayCollection();
        $this->qualityResults                      = new Collections\ArrayCollection();
        $this->qualityResultComments               = new Collections\ArrayCollection();
        $this->programDoa                          = new Collections\ArrayCollection();
        $this->rationale                           = new Collections\ArrayCollection();
        $this->organisationLog                     = new Collections\ArrayCollection();
        $this->affiliationLog                      = new Collections\ArrayCollection();
        $this->affiliationDescription              = new Collections\ArrayCollection();
        $this->projectLog                          = new Collections\ArrayCollection();
        $this->projectChangelog                    = new Collections\ArrayCollection();
        $this->affiliation                         = new Collections\ArrayCollection();
        $this->proxyAffiliation                    = new Collections\ArrayCollection();
        $this->actionClosed                        = new Collections\ArrayCollection();
        $this->actionStatus                        = new Collections\ArrayCollection();
        $this->actionComment                       = new Collections\ArrayCollection();
        $this->parent                              = new Collections\ArrayCollection();
        $this->parentFinancial                     = new Collections\ArrayCollection();
        $this->parentOrganisation                  = new Collections\ArrayCollection();
        $this->financial                           = new Collections\ArrayCollection();
        $this->invoice                             = new Collections\ArrayCollection();
        $this->publication                         = new Collections\ArrayCollection();
        $this->publicationDownload                 = new Collections\ArrayCollection();
        $this->photo                               = new Collections\ArrayCollection();
        $this->associate                           = new Collections\ArrayCollection();
        $this->deeplinkContact                     = new Collections\ArrayCollection();
        $this->registration                        = new Collections\ArrayCollection();
        $this->badge                               = new Collections\ArrayCollection();
        $this->badgeContact                        = new Collections\ArrayCollection();
        $this->boothContact                        = new Collections\ArrayCollection();
        $this->boothFinancial                      = new Collections\ArrayCollection();
        $this->selection                           = new Collections\ArrayCollection();
        $this->selectionContact                    = new Collections\ArrayCollection();
        $this->mailingContact                      = new Collections\ArrayCollection();
        $this->mailing                             = new Collections\ArrayCollection();
        $this->emailMessage                        = new Collections\ArrayCollection();
        $this->result                              = new Collections\ArrayCollection();
        $this->workpackage                         = new Collections\ArrayCollection();
        $this->workpackageDocument                 = new Collections\ArrayCollection();
        $this->idea                                = new Collections\ArrayCollection();
        $this->favouriteIdea                       = new Collections\ArrayCollection();
        $this->ideaMessage                         = new Collections\ArrayCollection();
        $this->ideaPartner                         = new Collections\ArrayCollection();
        $this->ideaMeetingParticipant              = new Collections\ArrayCollection();
        $this->blog                                = new Collections\ArrayCollection();
        $this->blogMessage                         = new Collections\ArrayCollection();
        $this->evaluation                          = new Collections\ArrayCollection();
        $this->calendarContact                     = new Collections\ArrayCollection();
        $this->calendarDocument                    = new Collections\ArrayCollection();
        $this->calendar                            = new Collections\ArrayCollection();
        $this->proxyProject                        = new Collections\ArrayCollection();
        $this->projectReviewers                    = new Collections\ArrayCollection();
        $this->projectVersionReviewers             = new Collections\ArrayCollection();
        $this->projectReport                       = new Collections\ArrayCollection();
        $this->projectReportItem                   = new Collections\ArrayCollection();
        $this->projectReportWorkpackageDescription = new Collections\ArrayCollection();
        $this->projectCalendarReviewers            = new Collections\ArrayCollection();
        $this->projectReportReviewers              = new Collections\ArrayCollection();
        $this->contract                            = new Collections\ArrayCollection();
        $this->contractVersion                     = new Collections\ArrayCollection();
        $this->invite                              = new Collections\ArrayCollection();
        $this->inviteContact                       = new Collections\ArrayCollection();
        $this->ideaInvite                          = new Collections\ArrayCollection();
        $this->ideaInviteContact                   = new Collections\ArrayCollection();
        $this->ideaMeetingInvite                   = new Collections\ArrayCollection();
        $this->callSessionParticipant              = new Collections\ArrayCollection();
        $this->loi                                 = new Collections\ArrayCollection();
        $this->loiApprover                         = new Collections\ArrayCollection();
        $this->affiliationDoa                      = new Collections\ArrayCollection();
        $this->affiliationDoaApprover              = new Collections\ArrayCollection();
        $this->parentDoa                           = new Collections\ArrayCollection();
        $this->permitContact                       = new Collections\ArrayCollection();
        $this->session                             = new Collections\ArrayCollection();
        $this->voter                               = new Collections\ArrayCollection();
        $this->tour                                = new Collections\ArrayCollection();
        $this->projectBooth                        = new Collections\ArrayCollection();
        $this->organisationBooth                   = new Collections\ArrayCollection();
        $this->tourContact                         = new Collections\ArrayCollection();
        $this->doaReminderReceiver                 = new Collections\ArrayCollection();
        $this->doaReminderSender                   = new Collections\ArrayCollection();
        $this->journalEntry                        = new Collections\ArrayCollection();
        $this->journal                             = new Collections\ArrayCollection();
        $this->organisationJournal                 = new Collections\ArrayCollection();
        $this->invoiceLog                          = new Collections\ArrayCollection();
        $this->reminder                            = new Collections\ArrayCollection();
        $this->achievement                         = new Collections\ArrayCollection();
        $this->changeRequestProcess                = new Collections\ArrayCollection();
        $this->changeRequestCostChange             = new Collections\ArrayCollection();
        $this->changeRequestCountry                = new Collections\ArrayCollection();
        $this->versionContact                      = new Collections\ArrayCollection();
        $this->workpackageContact                  = new Collections\ArrayCollection();
        $this->logCreatedBy                        = new Collections\ArrayCollection();
        $this->log                                 = new Collections\ArrayCollection();
        $this->affiliationVersion                  = new Collections\ArrayCollection();
        $this->note                                = new Collections\ArrayCollection();
        $this->pageview                            = new Collections\ArrayCollection();
        $this->organisationUpdates                 = new Collections\ArrayCollection();

        $this->hash          = hash('sha256', Rand::getString(100) . self::HASH_KEY);
        $this->triggerUpdate = false;
    }


    public function __toString(): string
    {
        return (string)$this->getDisplayName();
    }

    public function getDisplayName(): string
    {
        $name = sprintf('%s %s', $this->firstName, trim(implode(' ', [$this->middleName, $this->lastName])));

        return (string)(!empty(trim($name)) ? $name : $this->email);
    }

    public function parseFullName(): string
    {
        return $this->getDisplayName();
    }

    public function parseInitials(): string
    {
        $initials = explode('-', (string)$this->firstName);
        if ('' !== $this->middleName && null !== $this->middleName) {
            $initials[] = $this->middleName;
        }
        $initials[] = (string)$this->lastName;

        $initialString = '';
        foreach ($initials as $initial) {
            $initialString .= substr($initial, 0, 1);
        }

        return $initialString;
    }

    public function parseFirstName(): string
    {
        if (null === $this->firstName) {
            return '<empty>';
        }

        return $this->firstName;
    }

    public function isOffice(): bool
    {
        return in_array(strtolower(Access::ACCESS_OFFICE), $this->getRoles(), true);
    }

    public function getRoles(): array
    {
        $accessRoles = [];
        foreach ($this->access as $access) {
            $accessRoles[] = strtolower($access->getAccess());
        }

        return $accessRoles;
    }

    public function isAnonymised(): bool
    {
        return null !== $this->dateAnonymous;
    }

    public function isActivated(): bool
    {
        return null !== $this->dateActivated;
    }

    public function isActive(): bool
    {
        return null === $this->dateEnd;
    }

    public function isFunder(): bool
    {
        return null !== $this->funder;
    }

    public function hasOrganisation(): bool
    {
        return null !== $this->contactOrganisation;
    }

    public function hasPhoto(): bool
    {
        return !$this->photo->isEmpty();
    }

    public function isVisibleInCommunity(): bool
    {
        return $this->hasProfile() && $this->profile->getVisible() === Profile::VISIBLE_COMMUNITY;
    }

    public function hasProfile(): bool
    {
        return null !== $this->profile;
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

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated): Contact
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd($dateEnd): Contact
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDateAnonymous(): ?DateTime
    {
        return $this->dateAnonymous;
    }

    public function setDateAnonymous(?DateTime $dateAnonymous): Contact
    {
        $this->dateAnonymous = $dateAnonymous;
        return $this;
    }

    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth): Contact
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): Contact
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName($firstName): Contact
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender($gender): Contact
    {
        $this->gender = $gender;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Contact
    {
        $this->id = $id;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName($lastName): Contact
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate($lastUpdate): Contact
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName): Contact
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password): Contact
    {
        $this->password = $password;

        return $this;
    }

    public function getSaltedPassword(): ?string
    {
        return $this->saltedPassword;
    }

    public function setSaltedPassword($saltedPassword): Contact
    {
        $this->saltedPassword = $saltedPassword;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition($position): Contact
    {
        $this->position = $position;

        return $this;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function setTitle($title): Contact
    {
        $this->title = $title;

        return $this;
    }

    public function setState($state): void
    {
    }

    public function getState(): void
    {
    }

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

    public function setAccess($access): Contact
    {
        $this->access = $access;

        return $this;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function setEmailAddress($emailAddress): Contact
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address): Contact
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): Contact
    {
        $this->phone = $phone;

        return $this;
    }

    public function getOptIn(bool $onlyActive = false)
    {
        if (!$onlyActive) {
            return $this->optIn;
        }

        return $this->optIn->filter(
            static function (OptIn $optIn) {
                return $optIn->isActive();
            }
        );
    }

    public function setOptIn($optIn): Contact
    {
        $this->optIn = $optIn;

        return $this;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject($project): Contact
    {
        $this->project = $project;

        return $this;
    }

    public function getRationale()
    {
        return $this->rationale;
    }

    public function setRationale($rationale): Contact
    {
        $this->rationale = $rationale;

        return $this;
    }

    public function getProjectDescription()
    {
        return $this->projectDescription;
    }

    public function setProjectDescription($projectDescription): Contact
    {
        $this->projectDescription = $projectDescription;

        return $this;
    }

    public function getProjectVersion()
    {
        return $this->projectVersion;
    }

    public function setProjectVersion($projectVersion): Contact
    {
        $this->projectVersion = $projectVersion;

        return $this;
    }

    public function getProjectDocument()
    {
        return $this->projectDocument;
    }

    public function setProjectDocument($projectDocument): Contact
    {
        $this->projectDocument = $projectDocument;

        return $this;
    }

    public function getDnd()
    {
        return $this->dnd;
    }

    public function setDnd($dnd): Contact
    {
        $this->dnd = $dnd;

        return $this;
    }

    public function getActionClosed()
    {
        return $this->actionClosed;
    }

    public function setActionClosed($actionClosed): Contact
    {
        $this->actionClosed = $actionClosed;
        return $this;
    }

    public function getActionStatus()
    {
        return $this->actionStatus;
    }

    public function setActionStatus($actionStatus): Contact
    {
        $this->actionStatus = $actionStatus;
        return $this;
    }

    public function getActionComment()
    {
        return $this->actionComment;
    }

    public function setActionComment($actionComment): Contact
    {
        $this->actionComment = $actionComment;

        return $this;
    }

    public function getContract()
    {
        return $this->contract;
    }

    public function setContract($contract): Contact
    {
        $this->contract = $contract;

        return $this;
    }

    public function getContractVersion()
    {
        return $this->contractVersion;
    }

    public function setContractVersion($contractVersion): Contact
    {
        $this->contractVersion = $contractVersion;

        return $this;
    }

    public function getNda()
    {
        return $this->nda;
    }

    public function setNda($nda): Contact
    {
        $this->nda = $nda;

        return $this;
    }

    public function getNdaApprover()
    {
        return $this->ndaApprover;
    }

    public function setNdaApprover($ndaApprover): Contact
    {
        $this->ndaApprover = $ndaApprover;

        return $this;
    }

    public function getProgramDoa()
    {
        return $this->programDoa;
    }

    public function setProgramDoa($programDoa): Contact
    {
        $this->programDoa = $programDoa;

        return $this;
    }

    public function getContactOrganisation(): ?ContactOrganisation
    {
        return $this->contactOrganisation;
    }

    public function setContactOrganisation(?ContactOrganisation $contactOrganisation): Contact
    {
        $this->contactOrganisation = $contactOrganisation;

        return $this;
    }

    public function getIdea()
    {
        return $this->idea;
    }

    public function setIdea($idea): Contact
    {
        $this->idea = $idea;

        return $this;
    }

    public function getFavouriteIdea()
    {
        return $this->favouriteIdea;
    }

    public function setFavouriteIdea($favouriteIdea): Contact
    {
        $this->favouriteIdea = $favouriteIdea;

        return $this;
    }

    public function getOrganisationLog()
    {
        return $this->organisationLog;
    }

    public function setOrganisationLog($organisationLog): Contact
    {
        $this->organisationLog = $organisationLog;

        return $this;
    }

    public function getAffiliation()
    {
        return $this->affiliation;
    }

    public function setAffiliation($affiliation): Contact
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    public function getProxyAffiliation()
    {
        return $this->proxyAffiliation;
    }

    public function setProxyAffiliation($proxyAffiliation): Contact
    {
        $this->proxyAffiliation = $proxyAffiliation;

        return $this;
    }

    public function getAffiliationLog()
    {
        return $this->affiliationLog;
    }

    public function setAffiliationLog($affiliationLog): Contact
    {
        $this->affiliationLog = $affiliationLog;

        return $this;
    }

    public function getFinancial()
    {
        return $this->financial;
    }

    public function setFinancial($financial): Contact
    {
        $this->financial = $financial;

        return $this;
    }

    public function getAffiliationDescription()
    {
        return $this->affiliationDescription;
    }

    public function setAffiliationDescription($affiliationDescription): Contact
    {
        $this->affiliationDescription = $affiliationDescription;

        return $this;
    }

    public function getAffiliationVersion()
    {
        return $this->affiliationVersion;
    }

    public function setAffiliationVersion($affiliationVersion): Contact
    {
        $this->affiliationVersion = $affiliationVersion;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Collections\ArrayCollection|Invoice $invoice
     *
     * @return Contact
     */
    public function setInvoice($invoice): Contact
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    public function setPublication($publication): Contact
    {
        $this->publication = $publication;

        return $this;
    }

    public function getPublicationDownload()
    {
        return $this->publicationDownload;
    }

    public function setPublicationDownload($publicationDownload): Contact
    {
        $this->publicationDownload = $publicationDownload;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): Contact
    {
        $this->photo = $photo;

        return $this;
    }

    public function getAssociate()
    {
        return $this->associate;
    }

    public function setAssociate($associate): Contact
    {
        $this->associate = $associate;

        return $this;
    }

    public function getFunder(): ?Funder
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

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile($profile): Contact
    {
        $this->profile = $profile;

        return $this;
    }

    public function getRegistration()
    {
        return $this->registration;
    }

    public function setRegistration($registration): Contact
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Badge[]
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param Collections\ArrayCollection|Badge $badge
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

    public function setBadgeContact($badgeContact): Contact
    {
        $this->badgeContact = $badgeContact;

        return $this;
    }

    public function getBoothContact()
    {
        return $this->boothContact;
    }

    public function setBoothContact($boothContact): Contact
    {
        $this->boothContact = $boothContact;

        return $this;
    }

    public function getProjectBooth()
    {
        return $this->projectBooth;
    }

    public function setProjectBooth($projectBooth): Contact
    {
        $this->projectBooth = $projectBooth;

        return $this;
    }

    public function getOrganisationBooth()
    {
        return $this->organisationBooth;
    }

    public function setOrganisationBooth($organisationBooth): Contact
    {
        $this->organisationBooth = $organisationBooth;

        return $this;
    }

    public function getBoothFinancial()
    {
        return $this->boothFinancial;
    }

    /**
     * @param Collections\ArrayCollection|\Event\Entity\Booth\Financial $boothFinancial
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
     * @param Note[]|Collections\ArrayCollection $note
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
     * @param Selection[]|Collections\ArrayCollection $selection
     *
     * @return Contact
     */
    public function setSelection($selection): Contact
    {
        $this->selection = $selection;

        return $this;
    }

    public function getSelectionContact()
    {
        return $this->selectionContact;
    }

    public function setSelectionContact($selectionContact): Contact
    {
        $this->selectionContact = $selectionContact;

        return $this;
    }

    public function getMailingContact()
    {
        return $this->mailingContact;
    }

    /**
     * @param Collections\ArrayCollection|\Mailing\Entity\Contact $mailingContact
     *
     * @return Contact
     */
    public function setMailingContact($mailingContact): Contact
    {
        $this->mailingContact = $mailingContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Mailing
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * @param Collections\ArrayCollection|Mailing $mailing
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
     * @return Collections\ArrayCollection|Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param Collections\ArrayCollection|Result $result
     *
     * @return Contact
     */
    public function setResult($result): Contact
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Workpackage[]
     */
    public function getWorkpackage()
    {
        return $this->workpackage;
    }

    /**
     * @param Collections\ArrayCollection|Workpackage[] $workpackage
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
     * @param Collections\ArrayCollection|\Project\Entity\Workpackage\Document $workpackageDocument
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
     * @param Collections\ArrayCollection|\Project\Entity\Idea\Message $ideaMessage
     *
     * @return Contact
     */
    public function setIdeaMessage($ideaMessage): Contact
    {
        $this->ideaMessage = $ideaMessage;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Evaluation
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * @param Collections\ArrayCollection|Evaluation $evaluation
     *
     * @return Contact
     */
    public function setEvaluation($evaluation): Contact
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * @return Calendar|Collections\ArrayCollection
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param Calendar|Collections\ArrayCollection $calendar
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
     * @param \Calendar\Entity\Contact|Collections\ArrayCollection $calendarContact
     *
     * @return Contact
     */
    public function setCalendarContact($calendarContact): Contact
    {
        $this->calendarContact = $calendarContact;

        return $this;
    }

    /**
     * @return Document|Collections\ArrayCollection
     */
    public function getCalendarDocument()
    {
        return $this->calendarDocument;
    }

    /**
     * @param Document|Collections\ArrayCollection $calendarDocument
     *
     * @return Contact
     */
    public function setCalendarDocument($calendarDocument): Contact
    {
        $this->calendarDocument = $calendarDocument;

        return $this;
    }

    /**
     * @return Collections\Collection|\Evaluation\Entity\Reviewer
     */
    public function getProjectReviewers()
    {
        return $this->projectReviewers;
    }

    /**
     * @param Collections\Collection|\Evaluation\Entity\Reviewer $projectReviewers
     *
     * @return Contact
     */
    public function setProjectReviewers($projectReviewers): Contact
    {
        $this->projectReviewers = $projectReviewers;

        return $this;
    }

    /**
     * @return \Evaluation\Entity\Reviewer\Contact
     */
    public function getProjectReviewerContact()
    {
        return $this->projectReviewerContact;
    }

    /**
     * @param \Evaluation\Entity\Reviewer\Contact $projectReviewerContact
     *
     * @return Contact
     */
    public function setProjectReviewerContact($projectReviewerContact): Contact
    {
        $this->projectReviewerContact = $projectReviewerContact;

        return $this;
    }

    /**
     * @return Collections\Collection|\Project\Entity\Version\Reviewer
     */
    public function getProjectVersionReviewers()
    {
        return $this->projectVersionReviewers;
    }

    /**
     * @param Collections\Collection|\Project\Entity\Version\Reviewer $projectVersionReviewers
     *
     * @return Contact
     */
    public function setProjectVersionReviewers($projectVersionReviewers): Contact
    {
        $this->projectVersionReviewers = $projectVersionReviewers;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Report
     */
    public function getProjectReport()
    {
        return $this->projectReport;
    }

    /**
     * @param Collections\ArrayCollection|Report $projectReport
     *
     * @return Contact
     */
    public function setProjectReport($projectReport): Contact
    {
        $this->projectReport = $projectReport;

        return $this;
    }

    /**
     * @return Collections\Collection|\Project\Entity\Calendar\Reviewer
     */
    public function getProjectCalendarReviewers()
    {
        return $this->projectCalendarReviewers;
    }

    /**
     * @param Collections\Collection $projectCalendarReviewers
     *
     * @return Contact
     */
    public function setProjectCalendarReviewers($projectCalendarReviewers): Contact
    {
        $this->projectCalendarReviewers = $projectCalendarReviewers;

        return $this;
    }

    public function getProjectReportReviewers()
    {
        return $this->projectReportReviewers;
    }

    public function setProjectReportReviewers($projectReportReviewers): Contact
    {
        $this->projectReportReviewers = $projectReportReviewers;

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
     * @param Collections\ArrayCollection|\Project\Entity\Invite[] $invite
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
     * @param Collections\ArrayCollection|\Project\Entity\Invite[] $inviteContact
     *
     * @return Contact
     */
    public function setInviteContact($inviteContact): Contact
    {
        $this->inviteContact = $inviteContact;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Invite[]
     */
    public function getIdeaInvite()
    {
        return $this->ideaInvite;
    }

    /**
     * @param Collections\ArrayCollection|Invite[] $ideaInvite
     *
     * @return Contact
     */
    public function setIdeaInvite($ideaInvite): Contact
    {
        $this->ideaInvite = $ideaInvite;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Invite[]
     */
    public function getIdeaInviteContact()
    {
        return $this->ideaInviteContact;
    }

    /**
     * @param Collections\ArrayCollection|Invite[] $ideaInviteContact
     *
     * @return Contact
     */
    public function setIdeaInviteContact($ideaInviteContact): Contact
    {
        $this->ideaInviteContact = $ideaInviteContact;

        return $this;
    }

    /**
     * @return Loi[]|Collections\ArrayCollection
     */
    public function getLoi()
    {
        return $this->loi;
    }

    /**
     * @param Loi[]|Collections\ArrayCollection $loi
     *
     * @return Contact
     */
    public function setLoi($loi): Contact
    {
        $this->loi = $loi;

        return $this;
    }

    /**
     * @return Loi[]|Collections\ArrayCollection
     */
    public function getLoiApprover()
    {
        return $this->loiApprover;
    }

    /**
     * @param Loi[]|Collections\ArrayCollection $loiApprover
     *
     * @return Contact
     */
    public function setLoiApprover($loiApprover): Contact
    {
        $this->loiApprover = $loiApprover;

        return $this;
    }

    public function getAffiliationDoa()
    {
        return $this->affiliationDoa;
    }

    public function setAffiliationDoa($affiliationDoa): Contact
    {
        $this->affiliationDoa = $affiliationDoa;

        return $this;
    }

    public function getAffiliationDoaApprover()
    {
        return $this->affiliationDoaApprover;
    }

    public function setAffiliationDoaApprover($affiliationDoaApprover): Contact
    {
        $this->affiliationDoaApprover = $affiliationDoaApprover;
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
     * @param \Admin\Entity\Permit\Contact[]|Collections\Collection $permitContact
     *
     * @return Contact
     */
    public function setPermitContact($permitContact): Contact
    {
        $this->permitContact = $permitContact;

        return $this;
    }

    /**
     * @return Session[]|Collections\Collection
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session[]|Collections\Collection $session
     *
     * @return Contact
     */
    public function setSession($session): Contact
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Voter[]
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param Collections\ArrayCollection|Voter[] $voter
     *
     * @return Contact
     */
    public function setVoter($voter): Contact
    {
        $this->voter = $voter;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Tour
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param Collections\ArrayCollection|Tour $tour
     *
     * @return Contact
     */
    public function setTour($tour): Contact
    {
        $this->tour = $tour;

        return $this;
    }

    public function getTourContact()
    {
        return $this->tourContact;
    }

    public function setTourContact($tourContact): Contact
    {
        $this->tourContact = $tourContact;

        return $this;
    }

    public function getDoaReminderReceiver()
    {
        return $this->doaReminderReceiver;
    }

    public function setDoaReminderReceiver($doaReminderReceiver): Contact
    {
        $this->doaReminderReceiver = $doaReminderReceiver;

        return $this;
    }

    public function getDoaReminderSender()
    {
        return $this->doaReminderSender;
    }

    public function setDoaReminderSender($doaReminderSender): Contact
    {
        $this->doaReminderSender = $doaReminderSender;

        return $this;
    }

    public function getMagazineArticle()
    {
        return $this->magazineArticle;
    }

    public function setMagazineArticle($magazineArticle): Contact
    {
        $this->magazineArticle = $magazineArticle;
        return $this;
    }

    public function getBlog()
    {
        return $this->blog;
    }

    public function setBlog($blog): Contact
    {
        $this->blog = $blog;

        return $this;
    }


    public function getBlogMessage()
    {
        return $this->blogMessage;
    }

    /**
     * @param Collections\ArrayCollection|Message $blogMessage
     *
     * @return Contact
     */
    public function setBlogMessage($blogMessage): Contact
    {
        $this->blogMessage = $blogMessage;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Entry[]
     */
    public function getJournalEntry()
    {
        return $this->journalEntry;
    }

    /**
     * @param Collections\ArrayCollection|Entry[] $journalEntry
     *
     * @return Contact
     */
    public function setJournalEntry($journalEntry): Contact
    {
        $this->journalEntry = $journalEntry;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Journal[]
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param Collections\ArrayCollection|Journal[] $journal
     *
     * @return Contact
     */
    public function setJournal($journal): Contact
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Journal[]
     */
    public function getOrganisationJournal()
    {
        return $this->organisationJournal;
    }

    public function setOrganisationJournal($organisationJournal): Contact
    {
        $this->organisationJournal = $organisationJournal;

        return $this;
    }

    public function getInvoiceLog()
    {
        return $this->invoiceLog;
    }

    public function setInvoiceLog($invoiceLog): Contact
    {
        $this->invoiceLog = $invoiceLog;

        return $this;
    }

    public function getReminder()
    {
        return $this->reminder;
    }

    public function setReminder($reminder): Contact
    {
        $this->reminder = $reminder;

        return $this;
    }

    public function getAchievement()
    {
        return $this->achievement;
    }

    public function setAchievement($achievement): Contact
    {
        $this->achievement = $achievement;

        return $this;
    }

    public function getIdeaPartner()
    {
        return $this->ideaPartner;
    }

    public function setIdeaPartner($ideaPartner): Contact
    {
        $this->ideaPartner = $ideaPartner;

        return $this;
    }

    public function getProjectLog()
    {
        return $this->projectLog;
    }

    public function setProjectLog($projectLog): Contact
    {
        $this->projectLog = $projectLog;

        return $this;
    }

    public function getProjectChangelog()
    {
        return $this->projectChangelog;
    }

    public function setProjectChangelog(Collections\ArrayCollection $projectChangelog): Contact
    {
        $this->projectChangelog = $projectChangelog;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Item[]
     */
    public function getProjectReportItem()
    {
        return $this->projectReportItem;
    }

    /**
     * @param Collections\ArrayCollection|Item[] $projectReportItem
     *
     * @return Contact
     */
    public function setProjectReportItem($projectReportItem): Contact
    {
        $this->projectReportItem = $projectReportItem;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|EffortSpent[]
     */
    public function getProjectReportEffortSpent()
    {
        return $this->projectReportEffortSpent;
    }

    /**
     * @param Collections\ArrayCollection|EffortSpent[] $projectReportEffortSpent
     *
     * @return Contact
     */
    public function setProjectReportEffortSpent($projectReportEffortSpent): Contact
    {
        $this->projectReportEffortSpent = $projectReportEffortSpent;

        return $this;
    }

    public function getChangeRequestProcess()
    {
        return $this->changeRequestProcess;
    }

    public function setChangeRequestProcess($changerequestProcess): Contact
    {
        $this->changeRequestProcess = $changerequestProcess;

        return $this;
    }

    public function getChangeRequestCostChange()
    {
        return $this->changeRequestCostChange;
    }

    public function setChangeRequestCostChange($changerequestCostChange): Contact
    {
        $this->changeRequestCostChange = $changerequestCostChange;

        return $this;
    }

    public function getChangeRequestCountry()
    {
        return $this->changeRequestCountry;
    }

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
     * @return Collections\ArrayCollection|WorkpackageDescription[]|Collections\Collection
     */
    public function getProjectReportWorkpackageDescription()
    {
        return $this->projectReportWorkpackageDescription;
    }

    /**
     * @param Collections\Collection|WorkpackageDescription[] $projectReportWorkpackageDescription
     *
     * @return Contact
     */
    public function setProjectReportWorkpackageDescription($projectReportWorkpackageDescription): Contact
    {
        $this->projectReportWorkpackageDescription = $projectReportWorkpackageDescription;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Doa[]|Collections\Collection
     */
    public function getParentDoa()
    {
        return $this->parentDoa;
    }

    /**
     * @param Collections\ArrayCollection|Doa[]|Collections\Collection $parentDoa
     *
     * @return Contact
     */
    public function setParentDoa($parentDoa): Contact
    {
        $this->parentDoa = $parentDoa;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|OParent[]|Collections\Collection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collections\ArrayCollection|OParent[]|Collections\Collection $parent
     *
     * @return Contact
     */
    public function setParent($parent): Contact
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Financial[]
     */
    public function getParentFinancial()
    {
        return $this->parentFinancial;
    }

    /**
     * @param Collections\ArrayCollection|Financial[] $parentFinancial
     *
     * @return Contact
     */
    public function setParentFinancial($parentFinancial): Contact
    {
        $this->parentFinancial = $parentFinancial;

        return $this;
    }

    /**
     * @return Collections\ArrayCollection|Organisation[]
     */
    public function getParentOrganisation()
    {
        return $this->parentOrganisation;
    }

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
     * @return Collections\ArrayCollection|Pca[]
     */
    public function getPca()
    {
        return $this->pca;
    }

    /**
     * @param Collections\ArrayCollection|Pca[] $pca
     *
     * @return Contact
     */
    public function setPca($pca): Contact
    {
        $this->pca = $pca;

        return $this;
    }

    public function getPageview()
    {
        return $this->pageview;
    }

    public function setPageview($pageview): Contact
    {
        $this->pageview = $pageview;

        return $this;
    }

    public function getProxyProject()
    {
        return $this->proxyProject;
    }

    public function setProxyProject($proxyProject): Contact
    {
        $this->proxyProject = $proxyProject;
        return $this;
    }

    public function getDateActivated(): ?DateTime
    {
        return $this->dateActivated;
    }

    public function setDateActivated(?DateTime $dateActivated): Contact
    {
        $this->dateActivated = $dateActivated;
        return $this;
    }

    public function getTriggerUpdate(): ?bool
    {
        return $this->triggerUpdate;
    }

    public function setTriggerUpdate(bool $triggerUpdate): Contact
    {
        $this->triggerUpdate = $triggerUpdate;
        return $this;
    }

    public function getOfficeContact(): ?Office\Contact
    {
        return $this->officeContact;
    }

    public function setOfficeContact(?Office\Contact $officeContact): Contact
    {
        $this->officeContact = $officeContact;
        return $this;
    }

    public function getMagazineDownload()
    {
        return $this->magazineDownload;
    }

    public function setMagazineDownload($magazineDownload): Contact
    {
        $this->magazineDownload = $magazineDownload;
        return $this;
    }

    public function getMagazineArticleDownload()
    {
        return $this->magazineArticleDownload;
    }

    public function setMagazineArticleDownload($magazineArticleDownload): Contact
    {
        $this->magazineArticleDownload = $magazineArticleDownload;
        return $this;
    }

    public function getOrganisationUpdates(): Collections\Collection
    {
        return $this->organisationUpdates;
    }

    public function setOrganisationUpdates(Collections\Collection $organisationUpdates): Contact
    {
        $this->organisationUpdates = $organisationUpdates;
        return $this;
    }

    public function getQualityResults()
    {
        return $this->qualityResults;
    }

    public function setQualityResults($qualityResults): Contact
    {
        $this->qualityResults = $qualityResults;
        return $this;
    }

    public function getQualityResultComments()
    {
        return $this->qualityResultComments;
    }

    public function setQualityResultComments($qualityResultComments): Contact
    {
        $this->qualityResultComments = $qualityResultComments;
        return $this;
    }

    public function getQualityActions()
    {
        return $this->qualityActions;
    }

    public function setQualityActions($qualityActions): Contact
    {
        $this->qualityActions = $qualityActions;
        return $this;
    }

    public function getIdeaMeetingInvite()
    {
        return $this->ideaMeetingInvite;
    }

    public function setIdeaMeetingInvite($ideaMeetingInvite): Contact
    {
        $this->ideaMeetingInvite = $ideaMeetingInvite;
        return $this;
    }

    public function getIdeaMeetingParticipant()
    {
        return $this->ideaMeetingParticipant;
    }

    public function setIdeaMeetingParticipant($ideaMeetingParticipant): Contact
    {
        $this->ideaMeetingParticipant = $ideaMeetingParticipant;
        return $this;
    }

    public function getCallSessionParticipant()
    {
        return $this->callSessionParticipant;
    }

    public function setCallSessionParticipant($callSessionParticipant): Contact
    {
        $this->callSessionParticipant = $callSessionParticipant;
        return $this;
    }
}
