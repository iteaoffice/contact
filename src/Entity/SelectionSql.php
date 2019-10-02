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
use Zend\Form\Annotation;

/**
 * SelectionSql.
 *
 * @ORM\Table(name="selection_sql")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 *
 * @category    Contact
 */
class SelectionSql extends AbstractEntity
{
    /**
     * @ORM\Column(name="sql_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Selection", cascade={"persist"}, inversedBy="sql")
     * @ORM\JoinColumn(name="selection_id", referencedColumnName="selection_id", nullable=false)
     *
     * @var Selection
     */
    private $selection;
    /**
     * @ORM\Column(name="sql_query", type="text", nullable=false)
     *
     * @var string
     */
    private $query;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): SelectionSql
    {
        $this->id = $id;
        return $this;
    }

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setSelection(Selection $selection): SelectionSql
    {
        $this->selection = $selection;
        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): SelectionSql
    {
        $this->query = $query;
        return $this;
    }
}
