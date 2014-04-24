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

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SelectionContact
 *
 * @ORM\Table(name="selection_contact")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 *
 * @category    Contact
 * @package     Entity
 */
class SelectionContact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="selection_contact_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="selectionContact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Selection", cascade={"persist"}, inversedBy="selectionContact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="selection_id", referencedColumnName="selection_id", nullable=false)
     * })
     * @var \Contact\Entity\Selection
     */
    private $selection;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Exclude()
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @param \Contact\Entity\Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return \Contact\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
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
     * @param \Contact\Entity\Selection $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @return \Contact\Entity\Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }
}
