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

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpenId
 *
 * @ORM\Table(name="contact_openid")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_openid")
 *
 * @category    Contact
 * @package     Entity
 */
class OpenId
{
    /**
     * @var integer
     *
     * @ORM\Column(name="openid_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="identity", type="string", length=255, nullable=false)
     * @var string
     */
    private $identity;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="openId")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
}
