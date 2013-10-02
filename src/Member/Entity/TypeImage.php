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
 * MemberTypeImage
 *
 * @ORM\Table(name="member_type_image")
 * @ORM\Entity
 */
class TypeImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="member_type_image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $memberTypeImageId;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     */
    private $typeId;

    /**
     * @var \Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="image_id")
     * })
     */
    private $image;
}
