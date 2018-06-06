<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;

/**
 * Contact Log
 *
 * @ORM\Table(name="contact_log")
 * @ORM\Entity
 * @Annotation\Name("contact_log")
 */
class Log extends AbstractEntity
{
    /**
     * @ORM\Column(name="log_id", type="integer", length=10, options={"unsigned":true}, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="logCreatedBy", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="contact_id", nullable=false)
     *
     * @var Contact
     */
    private $createdBy;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Contact", inversedBy="log", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    private $dateCreated;
    /**
     * @ORM\Column(name="log", type="text", length=65535, nullable=true)
     *
     * @var string
     */
    private $log;

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function __isset($property)
    {
        return isset($this->$property);
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
     *
     * @return Log
     */
    public function setId(int $id): Log
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Contact
     */
    public function getCreatedBy(): ?Contact
    {
        return $this->createdBy;
    }

    /**
     * @param Contact $createdBy
     *
     * @return Log
     */
    public function setCreatedBy(Contact $createdBy): Log
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     *
     * @return Log
     */
    public function setContact(Contact $contact): Log
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return Log
     */
    public function setDateCreated(\DateTime $dateCreated): Log
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return string
     */
    public function getLog(): ?string
    {
        return $this->log;
    }

    /**
     * @param string $log
     *
     * @return Log
     */
    public function setLog(string $log): Log
    {
        $this->log = $log;
        return $this;
    }
}
