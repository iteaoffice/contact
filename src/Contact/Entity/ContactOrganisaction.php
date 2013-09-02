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
 * Organisation
 *
 * @ORM\Table(name="contact_organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 *
 * @category    Contact
 * @package     Entity
 */
class ContactOrganisaction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_organisation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="branch", type="string", length=40, nullable=true)
     * @var string
     */
    private $branch;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="contactOrganisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact[]
     */
    private $contact;
    /**
     * @ORM\OneToOne(targetEntity="Organisation",  cascade={"persist"}, inversedBy="contactOrganisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id")
     * })
     * @var Organisation
     * @todo
     * private $organisation;
     */
}
