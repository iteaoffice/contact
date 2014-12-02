<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Contact\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Entity for the Facebook
 *
 * @ORM\Table(name="facebook")
 * @ORM\Entity(repositoryClass="Contact\Repository\Facebook")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_facebook")
 *
 * @category    Contact
 * @package     Entity
 */
class Facebook extends EntityAbstract
{
    /**
     * Constant for public = 0 (not public)
     */
    const NOT_PUBLIC = 0;
    /**
     * Constant for public = 1 (hidden)
     */
    const IS_PUBLIC = 1;

    /**
     * Textual versions of the hideForOthers
     *
     * @var array
     */
    protected $publicTemplates = [
        self::NOT_PUBLIC => 'txt-not-public',
        self::IS_PUBLIC  => 'txt-public',
    ];
    /**
     * @ORM\Column(name="facebook_id", length=10, type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    public $id;
    /**
     * @ORM\Column(name="facebook", type="string", length=80, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-facebook"})
     * @var string
     */
    public $facebook;
    /**
     * @ORM\Column(name="public", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"publicTemplates"})
     * @Annotation\Attributes({"label":"txt-public", "required":"true"})
     * @Annotation\Required(true)
     * @var int
     */
    public $public;
    /**
     * @ORM\Column(name="from_clause", type="string", length=255, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({
     * "required":"true",
     * "class":"form-control",
     * "placeholder":"txt-from-clause"})
     * @Annotation\Options({"label":"txt-from-clause","help-block": "txt-from-clause-explanation"})
     * @var string
     */
    private $fromClause;
    /**
     * @ORM\Column(name="where_clause", type="string", length=255, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({
     * "required":"true",
     * "class":"form-control",
     * "placeholder":"txt-where-clause"})
     * @Annotation\Options({"label":"txt-where-clause","help-block": "txt-where-clause-explanation"})
     * @var string
     */
    private $whereClause;
    /**
     * @ORM\Column(name="orderby_clause", type="string", length=255, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({
     * "required":"true",
     * "class":"form-control",
     * "placeholder":"txt-orderby-clause"})
     * @Annotation\Options({"label":"txt-orderby-clause","help-block": "txt-orderby-clause-explanation"})
     * @var string
     */
    private $orderbyClause;
    /**
     * @ORM\Column(name="contact_key", type="string", length=60, nullable=true)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({
     * "required":"true",
     * "class":"form-control",
     * "placeholder":"txt-contact-key"})
     * @Annotation\Options({"label":"txt-contact-key","help-block": "txt-contact-key-explanation"})
     * @var string
     */
    private $contactKey;
    /**
     * @ORM\Column(name="com_extra", type="string", length=255, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({
     * "required":"true",
     * "class":"form-control",
     * "placeholder":"txt-title"})
     * @Annotation\Options({"label":"txt-title","help-block": "txt-title-explanation"})
     * @var string
     */
    private $title;
    /**
     * @ORM\Column(name="com_sub", type="string", length=255, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Attributes({
     * "required":"true",
     * "class":"form-control",
     * "placeholder":"txt-title"})
     * @Annotation\Options({"label":"txt-sub-title","help-block": "txt-sub-title-explanation"})
     * @var string
     */
    private $subtitle;

    /**
     * Class constructor
     */
    public function __construct()
    {
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
     * @return string
     */
    public function __toString()
    {
        return (string)$this->facebook;
    }

    /**
     * @return array
     */
    public function getPublicTemplates()
    {
        return $this->publicTemplates;
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
            $factory = new InputFactory();
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'       => 'facebook',
                        'required'   => true,
                        'filters'    => [
                            ['name' => 'StripTags'],
                            ['name' => 'StringTrim'],
                        ],
                        'validators' => [
                            [
                                'name'    => 'StringLength',
                                'options' => [
                                    'encoding' => 'UTF-8',
                                    'min'      => 1,
                                    'max'      => 80,
                                ],
                            ],
                        ],
                    ]
                )
            );
            $inputFilter->add(
                $factory->createInput(
                    [
                        'name'     => 'public',
                        'required' => true,
                    ]
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @param bool $textual
     * @return int
     */
    public function getPublic($textual = false)
    {
        if ($textual) {
            return $this->publicTemplates[$this->public];
        }
        return $this->public;
    }

    /**
     * @param int $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return string
     */
    public function getFromClause()
    {
        return $this->fromClause;
    }

    /**
     * @param string $fromClause
     */
    public function setFromClause($fromClause)
    {
        $this->fromClause = $fromClause;
    }

    /**
     * @return string
     */
    public function getWhereClause()
    {
        return $this->whereClause;
    }

    /**
     * @param string $whereClause
     */
    public function setWhereClause($whereClause)
    {
        $this->whereClause = $whereClause;
    }

    /**
     * @return string
     */
    public function getOrderbyClause()
    {
        return $this->orderbyClause;
    }

    /**
     * @param string $orderbyClause
     */
    public function setOrderbyClause($orderbyClause)
    {
        $this->orderbyClause = $orderbyClause;
    }

    /**
     * @return string
     */
    public function getContactKey()
    {
        return $this->contactKey;
    }

    /**
     * @param string $contactKey
     */
    public function setContactKey($contactKey)
    {
        $this->contactKey = $contactKey;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }


}
