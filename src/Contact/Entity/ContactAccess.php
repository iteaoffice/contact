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

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity for the Contact
 *
 * @ORM\Table(name="contact_access")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class ContactAccess
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_access_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactAccessId;

//    /**
//     * @ORM\ManyToOne(targetEntity="\Admin\Entity\Access", inversedBy="contact")
//     * @ORM\JoinColumns({
//     * @ORM\JoinColumn(name="access_id", referencedColumnName="access_id")
//     * })
//     * @var \Admin\Entity\Access
//     */
//    private $access;
//    /**
//     * @ORM\ManyToOne(targetEntity="\Contact\Entity\Contact", inversedBy="access")
//     * @ORM\JoinColumns({
//     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
//     * })
//     * @var \Contact\Entity\Contact
//     */
//    private $contact;

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

}
