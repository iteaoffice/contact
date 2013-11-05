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
 * @ORM\Table(name="contact_dnd_object")
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
     * @ORM\Column(name="object", type="blob", nullable=false)
     * @var string
     */
    private $object;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Dnd", cascade="persist", inversedBy="object")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="dnd_id", referencedColumnName="dnd_id", unique=true)
     * })
     * @var \Contact\Entity\Dnd;
     */
    private $dnd;

    /**
     * @param \Contact\Entity\Dnd $dnd
     */
    public function setDnd($dnd)
    {
        $this->dnd = $dnd;
    }

    /**
     * @return \Contact\Entity\Dnd
     */
    public function getDnd()
    {
        return $this->dnd;
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
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }
}
