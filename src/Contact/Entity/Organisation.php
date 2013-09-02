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

use Zend\Form\Annotation;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity for the Contact
 *
 * @ORM\Table(name="organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation")
 *
 * @category    Contact
 * @package     Entity
 */
class Organisation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="organisation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="organisation", type="string", length=60, nullable=false)
     * @var string
     */
    private $organisation;
    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $dateUpdated;
    /**
     * @ORM\ManyToOne(targetEntity="General\Entity\Country", cascade={"persist"}, inversedBy="organisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id")
     * })
     * @var \General\Entity\Country
     */
    private $country;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\OrganisationType", cascade={"persist"}, inversedBy="organisation")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="type_id", referencedColumnName="type_id")
     * })
     * @var \Contact\Entity\OrganisationType
     */
    private $type;
    /**
     * @ORM\OneToMany(targetEntity="\Program\Entity\ProgramDoa", cascade={"persist"}, mappedBy="organisation")
     * @Annotation\Exclude()
     * @var \Program\Entity\ProgramDoa[]
     */
    private $programDoa;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->call       = new Collections\ArrayCollection();
        $this->programDoa = new Collections\ArrayCollection();
    }
}
