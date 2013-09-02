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
 * Entity for a DND
 *
 * @ORM\Table(name="contact_dnd")
 * @ORM\Entity
 *
 * @category    Contact
 * @package     Entity
 */
class DndObject //extends EntityAbstract implements ResourceInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="blob", nullable=false)
     */
    private $object;

    /**
     * @var \ContactDnd
     *
     * @ORM\ManyToOne(targetEntity="ContactDnd")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id")
     * })
     */
    private $dnd;
}
