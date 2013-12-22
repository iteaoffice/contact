<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Member
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Member\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberType
 *
 * @ORM\Table(name="member_type")
 * @ORM\Entity
 */
class Type
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=60, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="fee", type="decimal", nullable=false)
     */
    private $fee;

    /**
     * @var float
     *
     * @ORM\Column(name="advance", type="decimal", nullable=false)
     */
    private $advance;

    /**
     * @var \MemberSubtype
     *
     * @ORM\ManyToOne(targetEntity="MemberSubtype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subtype_id", referencedColumnName="subtype_id")
     * })
     */
    private $subtype;
}
