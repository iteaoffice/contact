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
 * MemberTypeLogo
 *
 * @ORM\Table(name="member_type_logo")
 * @ORM\Entity
 */
class TypeLogo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="logo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $logoId;
    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="blob", nullable=false)
     */
    private $logo;
    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     */
    private $typeId;
    /**
     * @var integer
     *
     * @ORM\Column(name="contenttype_id", type="integer", nullable=false)
     */
    private $contenttypeId;
    /**
     * @var string
     *
     * @ORM\Column(name="logo_extension", type="string", length=20, nullable=false)
     */
    private $logoExtension;
}
