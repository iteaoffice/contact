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
    ProviderInterface
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
    /**
     * @ORM\Column(type="smallint",nullable=false)
     * @Annotation\Exclude()
     * @var int
     */
    private $state;
    /**
     * @ORM\Column(name="email",type="string",length=60,nullable=false, unique=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-email"})
     * @var string
     */
    private $email;
    /**
     * @ORM\Column(name="password", type="string", length=40, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-password"})
     * @var string
     */
    private $password;
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
     * @Annotation\Required("true")
     * @var int
     */
    private $messenger;
    /**
     * @ORM\ManyToMany(targetEntity="Contact\Entity\Access", cascade={"persist"},inversedBy="contact")
     * @ORM\JoinTable(name="contact_access",
     *    joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="access_id", referencedColumnName="access_id")}
     * )
     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
     * @Annotation\Options({"target_class":"Contact\Entity\Access"})
     * @Annotation\Attributes({"label":"txt-access"})
     * @var \Contact\Entity\Access[]
     */
    private $access;
    //    /**
    //     * @ORM\ManyToMany(targetEntity="Admin\Entity\Role", inversedBy="contacts", cascade={"all"}, fetch="EXTRA_LAZY")
    //     * @ORM\OrderBy=({"Name" =  "ASC"})
    //     * @ORM\JoinTable(name="admin_user_role",
    //     *      joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")},
    //     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
    //     * )
    //     * @Annotation\Type("DoctrineORMModule\Form\Element\EntityMultiCheckbox")
    //     * @Annotation\Options({"target_class":"Admin\Entity\Role"})
    //     * @Annotation\Attributes({"label":"txt-roles"})
    //     * @var \Admin\Entity\Role[]
    //     */
    //    private $roles;
    /**
     * @ORM\OneToMany(targetEntity="\Contact\Entity\Email", cascade={"persist"}, mappedBy="contact")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Email[]
     */
    private $emailAddresses;
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
    private $addresses;
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
     * Class constructor
     */
    public function __construct()
    {
        $this->projects       = new Collections\ArrayCollection();
        $this->cv             = new Collections\ArrayCollection();
        $this->web            = new Collections\ArrayCollection();
        $this->addresses      = new Collections\ArrayCollection();
        $this->emailAddresses = new Collections\ArrayCollection();
        $this->access         = new Collections\ArrayCollection();
        $this->optIn          = new Collections\ArrayCollection();
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
        return implode(' ', array($this->firstName, $this->middleName, $this->lastName));
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
            'projects'  => $this->projects,
            'addresses' => $this->addresses,
            'web'       => $this->web,
            'cv'        => $this->cv,
            'email'     => $this->email,
//            'roles' => $this->roles
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
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
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
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey(self::CRYPT_KEY);

        return $blockCipher->decrypt($hash);
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * New function needed to make the hydrator happy
     *
     * @param Collections\Collection $roles
     */
    public function addRoles(Collections\Collection $roles)
    {
        foreach ($roles as $role) {
            $role->user = $this;
            $this->roles->add($role);
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
            $this->roles->removeElement($role);
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
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
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
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return UserInterface
     */
    public function setDisplayName($displayName)
    {
        return false;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param \Contact\Entity\CV[] $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * @return \Contact\Entity\CV[]
     */
    public function getAddresses()
    {
        return $this->addresses;
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
     * @param \Contact\Entity\Email[] $emailAddresses
     */
    public function setEmailAddresses($emailAddresses)
    {
        $this->emailAddresses = $emailAddresses;
    }

    /**
     * @return \Contact\Entity\Email[]
     */
    public function getEmailAddresses()
    {
        return $this->emailAddresses;
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
}
