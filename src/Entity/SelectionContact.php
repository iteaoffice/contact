<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;

/**
 * SelectionContact.
 *
 * @ORM\Table(name="selection_contact")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 *
 * @category    Contact
 */
class SelectionContact extends AbstractEntity
{
    /**
     * @ORM\Column(name="selection_contact_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="selectionContact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Selection", cascade={"persist"}, inversedBy="selectionContact")
     * @ORM\JoinColumn(name="selection_id", referencedColumnName="selection_id", nullable=false)
     *
     * @var Selection
     */
    private $selection;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     *
     * @var DateTime
     */
    private $dateCreated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): SelectionContact
    {
        $this->id = $id;
        return $this;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): SelectionContact
    {
        $this->contact = $contact;
        return $this;
    }

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setSelection(Selection $selection): SelectionContact
    {
        $this->selection = $selection;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): SelectionContact
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }
}
