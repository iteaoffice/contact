<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Contact\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Crypt\BlockCipher;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

use BjyAuthorize\Provider\Role\ProviderInterface;

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
class Contact extends EntityAbstract implements
    ResourceInterface,
    ProviderInterface,
    UserInterface
{
    /**
     * Key needed for the encryption and decryption of the Keys
     */
    const CRYPT_KEY = 'afc26c5daef5373cf4acb7ee107d423f';
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
    protected $messengerTemplates = array(
        self::MESSENGER_ACTIVE => self::MESSENGER_ACTIVE_VALUE,
    );

    /**
     * @ORM\Column(name="contact_id", type="integer", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
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
     * @Annotation\Options({"label":"txt-first-name"})
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
    //    /**
    //     * @ORM\Column(type="smallint",nullable=false)
    //     * @Annotation\Exclude()
    //     * @var int
    //     */
    //    private $state;
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
     * @Annotation\Attributes({"label":"txt-gender", "required":"true","class":"span3"})
     * @var \General\Entity\Gender
     */
    private $gender;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Title", cascade={"persist"}, inversedBy="contacts")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="title_id", nullable=false)
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntitySelect")
     * @Annotation\Options({"target_class":"General\Entity\Title"})
     * @Annotation\Attributes({"label":"txt-title", "required":"true","class":"span3"})
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
     * @Annotation\Options({"label":"txt-date"})
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
     * @Annotation\Type("\Zend\Form\Element\Date")
     * @Annotation\Options({"label":"txt-date-end"})
     * @var \datetime
     */
    private $dateEnd;
    /**
     * @ORM\Column(name="messenger", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"messengerTemplates"})
     * @Annotation\Attributes({"label":"txt-messenger", "required":"true"})
     * @Annotation\Required(true)
     * @var int
     */
    private $messenger;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Access", cascade={"persist"},inversedBy="contact")
     * @ORM\JoinTable(name="contact_access",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="access_id", referencedColumnName="access_id")}
     * )
     * @ORM\OrderBy({"access"="ASC"})
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Contact\Entity\Access"})
     * @Annotation\Attributes({"label":"txt-access"})
     * @var \Contact\Entity\Access[]
     */
    private $access;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Role", inversedBy="contact", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy=({"Name" =  "ASC"})
     * @ORM\JoinTable(name="admin_user_role",
     *      joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Admin\Entity\Role"})
     * @Annotation\Attributes({"label":"txt-roles"})
     * @var \Admin\Entity\Role[]
     */
    private $role;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Email", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Email[]
     */
    private $emailAddress;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Cv", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\CV[]
     */
    private $cv;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Address", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\CV[]
     */
    private $address;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Web", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Web[]
     */
    private $web;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\OptIn", cascade={"persist"},inversedBy="contact")
     * @ORM\JoinTable(name="contact_optin",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="optin_id", referencedColumnName="optin_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Contact\Entity\OptIn"})
     * @Annotation\Attributes({"label":"txt-opt-in"})
     * @var \Contact\Entity\OptIn[]
     */
    private $optIn;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Project", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]
     */
    private $project;
    /**
     * @ORM\OneToMany(targetEntity="\Project\Entity\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]
     */
    private $projectVersion;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Dnd", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]
     */
    private $dnd;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\Nda", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Project\Entity\Project[]
     */
    private $nda;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\ProgramDoa", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Program\Entity\ProgramDoa[]
     */
    private $programDoa;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\OpenId", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\OpenId[]
     */
    private $openId;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\ContactOrganisation", cascade={"persist"}, mappedBy="contact")
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
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Domain"})
     * @Annotation\Attributes({"label":"txt-domain"})
     * @var \Program\Entity\Domain[]
     */
    private $domain;
    /**
     * @ORM\ManyToMany(targetEntity="Exhibition\Entity\Idea", cascade={"persist"},inversedBy="contact")
     * @ORM\JoinTable(name="contact_idea",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="idea_id", referencedColumnName="idea_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Exhibition\Entity\Idea"})
     * @Annotation\Attributes({"label":"txt-idea"})
     * \Event\Entity\Idea[]
     * @todo
     * private $idea;
     */
    /**
     * @ORM\ManyToMany(targetEntity="Program\Entity\Technology", cascade={"persist"}, inversedBy="contact")
     * @ORM\JoinTable(name="contact_technology",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="technology_id", referencedColumnName="technology_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Program\Entity\Technology"})
     * @Annotation\Attributes({"label":"txt-technology"})
     * @var \Program\Entity\Technology[]
     */
    private $technology;
    /**
     * @ORM\OneToMany(targetEntity="Organisation\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Organisation\Entity\Log[]
     */
    private $organisationLog;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Affiliation[]
     */
    private $affiliation;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Log", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Log[]
     */
    private $affiliationLog;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Financial[]
     */
    private $financial;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Description", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Description[]
     */
    private $affiliationDescription;
    /**
     * @ORM\OneToMany(targetEntity="Affiliation\Entity\Version", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Version[]
     */
    private $affiliationVersion;
    /**
     * @ORM\OneToMany(targetEntity="Invoice\Entity\Invoice", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Invoice\Entity\Invoice[]
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Publication\Entity\Publication", cascade={"persist"}, mappedBy="contact")
     * @var \Publication\Entity\Publication[]
     */
    private $publication;
    /**
     * @ORM\OneToMany(targetEntity="Publication\Entity\Download", cascade={"persist"}, mappedBy="contact")
     * @var \Publication\Entity\Download[]
     */
    private $publicationDownload;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Photo", cascade={"persist"}, mappedBy="contact")
     * @var \Contact\Entity\Photo[]
     */
    private $photo;
    /**
     * @ORM\ManyToMany(targetEntity="Affiliation\Entity\Affiliation", cascade={"persist"}, mappedBy="associate")
     * @Annotation\Exclude()
     * @var \Affiliation\Entity\Affiliation[]
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
     * @var \Deeplink\Entity\Contact[]
     */
    private $deeplinkContact;
    /**
     * @ORM\OneToOne(targetEntity="\Contact\Entity\Profile", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Profile
     */
    private $profile;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Community", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Community[]
     */
    private $community;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Registration", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Registration[]
     */
    private $registration;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Badge\Badge", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Badge\Badge[]
     */
    private $badge;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Badge\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Badge\Contact[]
     */
    private $badgeContact;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Booth", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Booth\Booth[]
     */
    private $booth;
    /**
     * @ORM\OneToMany(targetEntity="Event\Entity\Booth\Financial", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Event\Entity\Booth\Financial[]
     */
    private $boothFinancial;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Note", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Note[]
     */
    private $note;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Selection", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Selection[]
     */
    private $selection;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\SelectionContact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\SelectionContact[]
     */
    private $selectionContact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Contact", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Mailing\Entity\Contact[]
     */
    private $mailingContact;
    /**
     * @ORM\OneToMany(targetEntity="Mailing\Entity\Mailing", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Mailing\Entity\Mailing[]
     */
    private $mailing;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->project                = new Collections\ArrayCollection();
        $this->projectVersion         = new Collections\ArrayCollection();
        $this->cv                     = new Collections\ArrayCollection();
        $this->web                    = new Collections\ArrayCollection();
        $this->role                   = new Collections\ArrayCollection();
        $this->address                = new Collections\ArrayCollection();
        $this->emailAddress           = new Collections\ArrayCollection();
        $this->access                 = new Collections\ArrayCollection();
        $this->optIn                  = new Collections\ArrayCollection();
        $this->domain                 = new Collections\ArrayCollection();
        $this->technology             = new Collections\ArrayCollection();
        $this->dnd                    = new Collections\ArrayCollection();
        $this->nda                    = new Collections\ArrayCollection();
        $this->programDoa             = new Collections\ArrayCollection();
        $this->domain                 = new Collections\ArrayCollection();
        $this->technology             = new Collections\ArrayCollection();
        $this->openId                 = new Collections\ArrayCollection();
        $this->organisationLog        = new Collections\ArrayCollection();
        $this->affiliationLog         = new Collections\ArrayCollection();
        $this->affiliationDescription = new Collections\ArrayCollection();
        $this->affiliation            = new Collections\ArrayCollection();
        $this->financial              = new Collections\ArrayCollection();
        $this->invoice                = new Collections\ArrayCollection();
        $this->publication            = new Collections\ArrayCollection();
        $this->publicationDownload    = new Collections\ArrayCollection();
        $this->photo                  = new Collections\ArrayCollection();
        $this->associate              = new Collections\ArrayCollection();
        $this->deeplinkContact        = new Collections\ArrayCollection();
        $this->community              = new Collections\ArrayCollection();
        $this->registration           = new Collections\ArrayCollection();
        $this->badge                  = new Collections\ArrayCollection();
        $this->badgeContact           = new Collections\ArrayCollection();
        $this->boothFinancial         = new Collections\ArrayCollection();
        $this->selection              = new Collections\ArrayCollection();
        $this->selectionContact       = new Collections\ArrayCollection();
        $this->mailingContact         = new Collections\ArrayCollection();
        $this->mailing                = new Collections\ArrayCollection();
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
     * toString returns the name
     *
     * @return string
     */
    public function __toString()
    {
        return trim(implode(' ', array($this->firstName, $this->middleName, $this->lastName)));
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        return __NAMESPACE__ . ':' . __CLASS__ . ':' . $this->id;
    }

    /**
     * Set input filter
     *
     * @param InputFilterInterface $inputFilter
     *
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Setting an inputFilter is currently not supported");
    }

    /**
     * @return \Zend\InputFilter\InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'name',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'email',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'       => 'label',
                        'required'   => true,
                        'filters'    => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 100,
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'address',
                        'required' => false,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'emailAddress',
                        'required' => false,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'role',
                        'required' => false,
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'project'        => $this->project,
            'projectVersion' => $this->projectVersion,
            'address'        => $this->address,
            'emailAddress'   => $this->emailAddress,
            'access'         => $this->access,
            'optIn'          => $this->optIn,
            'web'            => $this->web,
            'domain'         => $this->domain,
            'technology'     => $this->technology,
            'cv'             => $this->cv,
            'email'          => $this->email,
            'role'           => $this->role,
            'dnd'            => $this->dnd,
            'nda'            => $this->nda,
            'programDoa'     => $this->programDoa,
            'openId'         => $this->openId,
            'note'           => $this->note,
        );
    }

    public function populate()
    {
        return $this->getArrayCopy();
    }

    /**
     * Create a hash for a user
     *
     * @return string
     */
    public function parseHash()
    {
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'cast-256'));
        $blockCipher->setKey(self::CRYPT_KEY);

        return $blockCipher->encrypt($this->id);
    }

    /**
     * Decrypt a given hash
     *
     * @param $hash
     *
     * @return bool|string
     */
    public function decryptHash($hash)
    {
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'cast-256'));
        $blockCipher->setKey(self::CRYPT_KEY);

        return $blockCipher->decrypt($hash);
    }

    /**
     * Returns the string identifier of the Role.
     * We return the access here since that entity keeps the access roles
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->access;
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $roles
     */
    public function addRoles(Collections\Collection $roles)
    {
        foreach ($roles as $role) {
            $this->role->add($role);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $roles
     */
    public function removeRoles(Collections\Collection $roles)
    {
        foreach ($roles as $role) {
            $this->role->removeElement($role);
        }
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $optInCollection
     */
    public function addOptIn(Collections\Collection $optInCollection)
    {
        foreach ($optInCollection as $optIn) {
            $optIn->contact = $this;
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
            $access->contact = $this;
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
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param \General\Entity\Gender $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return \General\Entity\Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $id
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
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     * @param \General\Entity\Title $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \General\Entity\Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        return null;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return null;
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
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return (string)$this;
    }

    /**
     * @param string $displayName
     *
     * @return bool|UserInterface
     */
    public function setDisplayName($displayName)
    {
        return false;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param \Contact\Entity\CV[] $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return \Contact\Entity\CV[]
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Contact\Entity\CV[] $cv
     */
    public function setCv($cv)
    {
        $this->cv = $cv;
    }

    /**
     * @return \Contact\Entity\CV[]
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param \Contact\Entity\Web[] $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return \Contact\Entity\Web[]
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param \Contact\Entity\Email[] $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return \Contact\Entity\Email[]
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param \Contact\Entity\OptIn[] $optIn
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;
    }

    /**
     * @return \Contact\Entity\OptIn[]
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * @param \Contact\Entity\Access[] $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return \Contact\Entity\Access[]
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param \Project\Entity\Project[] $dnd
     */
    public function setDnd($dnd)
    {
        $this->dnd = $dnd;
    }

    /**
     * @return \Project\Entity\Project[]
     */
    public function getDnd()
    {
        return $this->dnd;
    }

    /**
     * @param \Project\Entity\Project[] $nda
     */
    public function setNda($nda)
    {
        $this->nda = $nda;
    }

    /**
     * @return \Project\Entity\Project[]
     */
    public function getNda()
    {
        return $this->nda;
    }

    /**
     * @param \Program\Entity\ProgramDoa[] $programDoa
     */
    public function setProgramDoa($programDoa)
    {
        $this->programDoa = $programDoa;
    }

    /**
     * @return \Program\Entity\ProgramDoa[]
     */
    public function getProgramDoa()
    {
        return $this->programDoa;
    }

    /**
     * @param \Project\Entity\Project[] $projectVersion
     */
    public function setProjectVersion($projectVersion)
    {
        $this->projectVersion = $projectVersion;
    }

    /**
     * @return \Project\Entity\Project[]
     */
    public function getProjectVersion()
    {
        return $this->projectVersion;
    }

    /**
     * @param \Program\Entity\Domain[] $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return \Program\Entity\Domain[]
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param \Affiliation\Entity\Log[] $affiliationLog
     */
    public function setAffiliationLog($affiliationLog)
    {
        $this->affiliationLog = $affiliationLog;
    }

    /**
     * @return \Affiliation\Entity\Log[]
     */
    public function getAffiliationLog()
    {
        return $this->affiliationLog;
    }

    /**
     * @param \Contact\Entity\ContactOrganisation $contactOrganisation
     */
    public function setContactOrganisation($contactOrganisation)
    {
        $this->contactOrganisation = $contactOrganisation;
    }

    /**
     * @return \Contact\Entity\ContactOrganisation
     */
    public function getContactOrganisation()
    {
        return $this->contactOrganisation;
    }

    /**
     * @param \Affiliation\Entity\Financial[] $financial
     */
    public function setFinancial($financial)
    {
        $this->financial = $financial;
    }

    /**
     * @return \Affiliation\Entity\Financial[]
     */
    public function getFinancial()
    {
        return $this->financial;
    }

    /**
     * @param \Organisation\Entity\Log[] $organisationLog
     */
    public function setOrganisationLog($organisationLog)
    {
        $this->organisationLog = $organisationLog;
    }

    /**
     * @return \Organisation\Entity\Log[]
     */
    public function getOrganisationLog()
    {
        return $this->organisationLog;
    }

    /**
     * @param \Program\Entity\Technology[] $technology
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;
    }

    /**
     * @return \Program\Entity\Technology[]
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param \Contact\Entity\OpenId[] $openId
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;
    }

    /**
     * @return \Contact\Entity\OpenId[]
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * @param \Affiliation\Entity\Affiliation[] $affiliation
     */
    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param \Affiliation\Entity\Description[] $affiliationDescription
     */
    public function setAffiliationDescription($affiliationDescription)
    {
        $this->affiliationDescription = $affiliationDescription;
    }

    /**
     * @return \Affiliation\Entity\Description[]
     */
    public function getAffiliationDescription()
    {
        return $this->affiliationDescription;
    }

    /**
     * @param \Affiliation\Entity\Version[] $affiliationVersion
     */
    public function setAffiliationVersion($affiliationVersion)
    {
        $this->affiliationVersion = $affiliationVersion;
    }

    /**
     * @return \Affiliation\Entity\Version[]
     */
    public function getAffiliationVersion()
    {
        return $this->affiliationVersion;
    }

    /**
     * @param \Invoice\Entity\Invoice[] $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return \Invoice\Entity\Invoice[]
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param \Publication\Entity\Publication[] $publication
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;
    }

    /**
     * @return \Publication\Entity\Publication[]
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @param \Publication\Entity\Download[] $publicationDownload
     */
    public function setPublicationDownload($publicationDownload)
    {
        $this->publicationDownload = $publicationDownload;
    }

    /**
     * @return \Publication\Entity\Download[]
     */
    public function getPublicationDownload()
    {
        return $this->publicationDownload;
    }

    /**
     * @param \Contact\Entity\Photo $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * Find the photo. We need to apply a trick here since the photo has a 1:n relation in the entities to avoid
     * the eager loading of the BLOB but we know that we only have 1 photo
     *
     * @return \Contact\Entity\Photo
     */
    public function getPhoto()
    {
        if ($this->photo->count() > 0) {
            return $this->photo[0];
        } else {
            return null;
        }
    }

    /**
     * @param \Affiliation\Entity\Affiliation[] $associate
     */
    public function setAssociate($associate)
    {
        $this->associate = $associate;
    }

    /**
     * @return \Affiliation\Entity\Affiliation[]
     */
    public function getAssociate()
    {
        return $this->associate;
    }

    /**
     * @param \Program\Entity\Funder $funder
     */
    public function setFunder($funder)
    {
        $this->funder = $funder;
    }

    /**
     * @return \Program\Entity\Funder
     */
    public function getFunder()
    {
        return $this->funder;
    }

    /**
     * @param \Admin\Entity\Role[] $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return \Admin\Entity\Role[]
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param \Deeplink\Entity\Contact[] $deeplinkContact
     */
    public function setDeeplinkContact($deeplinkContact)
    {
        $this->deeplinkContact = $deeplinkContact;
    }

    /**
     * @return \Deeplink\Entity\Contact[]
     */
    public function getDeeplinkContact()
    {
        return $this->deeplinkContact;
    }

    /**
     * @param \Contact\Entity\Profile $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return \Contact\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param \Contact\Entity\Community[] $community
     */
    public function setCommunity($community)
    {
        $this->community = $community;
    }

    /**
     * @return \Contact\Entity\Community[]
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * @param \Event\Entity\Registration[] $registration
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;
    }

    /**
     * @return \Event\Entity\Registration[]
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @param \Event\Entity\Badge\Badge[] $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    /**
     * @return \Event\Entity\Badge\Badge[]
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param \Event\Entity\Badge\Contact[] $badgeContact
     */
    public function setBadgeContact($badgeContact)
    {
        $this->badgeContact = $badgeContact;
    }

    /**
     * @return \Event\Entity\Badge\Contact[]
     */
    public function getBadgeContact()
    {
        return $this->badgeContact;
    }

    /**
     * @param \Event\Entity\Booth\Booth[] $booth
     */
    public function setBooth($booth)
    {
        $this->booth = $booth;
    }

    /**
     * @return \Event\Entity\Booth\Booth[]
     */
    public function getBooth()
    {
        return $this->booth;
    }

    /**
     * @param \Event\Entity\Booth\Financial[] $boothFinancial
     */
    public function setBoothFinancial($boothFinancial)
    {
        $this->boothFinancial = $boothFinancial;
    }

    /**
     * @return \Event\Entity\Booth\Financial[]
     */
    public function getBoothFinancial()
    {
        return $this->boothFinancial;
    }

    /**
     * @param \Contact\Entity\Note[] $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return \Contact\Entity\Note[]
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param \Contact\Entity\SelectionContact[] $selectionContact
     */
    public function setSelectionContact($selectionContact)
    {
        $this->selectionContact = $selectionContact;
    }

    /**
     * @return \Contact\Entity\SelectionContact[]
     */
    public function getSelectionContact()
    {
        return $this->selectionContact;
    }

    /**
     * @param \Contact\Entity\Selection[] $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @return \Contact\Entity\Selection[]
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param \Mailing\Entity\Contact[] $mailingContact
     */
    public function setMailingContact($mailingContact)
    {
        $this->mailingContact = $mailingContact;
    }

    /**
     * @return \Mailing\Entity\Contact[]
     */
    public function getMailingContact()
    {
        return $this->mailingContact;
    }
}
