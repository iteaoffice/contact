<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domain.
 *
 * @ORM\Table(name="contact_link")
 * @ORM\Entity
 *
 * @category    Contact
 */
class Link
{
    /**
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact1_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact1;
    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact2_id", referencedColumnName="contact_id")
     * })
     *
     * @var \Contact\Entity\Contact
     */
    private $contact2;

    /**
     * @return Contact
     */
    public function getContact1()
    {
        return $this->contact1;
    }

    /**
     * @param Contact $contact1
     */
    public function setContact1($contact1)
    {
        $this->contact1 = $contact1;
    }

    /**
     * @return Contact
     */
    public function getContact2()
    {
        return $this->contact2;
    }

    /**
     * @param Contact $contact2
     */
    public function setContact2($contact2)
    {
        $this->contact2 = $contact2;
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
}
