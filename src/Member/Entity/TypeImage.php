<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * MemberTypeImage
 *
 * @ORM\Table(name="member_type_image")
 * @ORM\Entity
 */
class MemberTypeImage
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
