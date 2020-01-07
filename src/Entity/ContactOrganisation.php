<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Organisation\Entity\Organisation;
use Organisation\Service\OrganisationService;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="contact_organisation")
 * @ORM\Entity
 * @Annotation\Hydrator("Laminas\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 */
class ContactOrganisation extends AbstractEntity
{
    /**
     * @ORM\Column(name="contact_organisation_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="branch", type="string", nullable=true)
     *
     * @var string
     */
    private $branch;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Contact",  cascade={"persist"}, inversedBy="contactOrganisation")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="contact_id", nullable=false)
     *
     * @var Contact
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="Organisation\Entity\Organisation", inversedBy="contactOrganisation", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="organisation_id", nullable=false)
     *
     * @var Organisation
     */
    private $organisation;

    public function toArray(): array
    {
        return [
            'organisation_id' => $this->organisation->getId(),
            'organisation'    => OrganisationService::parseBranch($this->branch, $this->organisation),
            'branch'          => $this->branch,
            'country'         => $this->organisation->getCountry()->getId(),
            'type'            => $this->organisation->getType()->getId()
        ];
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch($branch): void
    {
        $this->branch = $branch;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact($contact): void
    {
        $this->contact = $contact;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation($organisation): void
    {
        $this->organisation = $organisation;
    }
}
