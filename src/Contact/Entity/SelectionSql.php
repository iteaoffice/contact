<?php
/**
 * Debranova copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 Debranova
 */
namespace Contact\Entity;

use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

/**
 * SelectionSql
 *
 * @ORM\Table(name="selection_sql")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 *
 * @category    Contact
 * @package     Entity
 */
class SelectionSql
{
    /**
     * @ORM\Column(name="sql_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Contact\Entity\Selection", cascade={"persist"}, inversedBy="sql")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="selection_id", referencedColumnName="selection_id", nullable=false)
     * })
     * @var \Contact\Entity\Selection
     */
    private $selection;
    /**
     * @ORM\Column(name="sql_query", type="string", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-query"})
     * @var string
     */
    private $query;

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
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param \Contact\Entity\Selection $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @return \Contact\Entity\Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }
}
