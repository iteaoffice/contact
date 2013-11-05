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
 * Domain
 *
 * @ORM\Table(name="contact_link")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class Link
{
    /**
     * @ORM\Column(name="link_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact1_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact1;
    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="contact2_id", referencedColumnName="contact_id")
     * })
     * @var \Contact\Entity\Contact
     */
    private $contact2;
}
