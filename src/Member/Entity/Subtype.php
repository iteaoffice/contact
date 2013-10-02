<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Member
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Debranova
 */
namespace Member\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberSubtype
 *
 * @ORM\Table(name="member_subtype")
 * @ORM\Entity
 */
class Subtype
{
    /**
     * @var integer
     *
     * @ORM\Column(name="subtype_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $subtypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="subtype", type="string", length=1, nullable=false)
     */
    private $subtype;
}
