<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * SelectionMailingList.
 *
 * @ORM\Table(name="selection_mailinglist")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("contact_organisation")
 *
 * @category    Contact
 */
class SelectionMailinglist
{
    /**
     * Constant for main = 0 (not main).
     */
    public const NOT_MAIN = 0;
    /**
     * Constant for main = 1 (main).
     */
    public const MAIN = 1;
    /**
     * Textual versions of the main.
     *
     * @var array
     */
    protected $privateTemplates
        = [
            self::NOT_MAIN => 'txt-not-main',
            self::MAIN     => 'txt-main',
        ];
    /**
     * @var integer
     *
     * @ORM\Column(name="mailinglist_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="alias", type="string", length=60, nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Text")
     * @Annotation\Options({"label":"txt-alias"})
     *
     * @var string
     */
    private $alias;
    /**
     * @ORM\ManyToOne(targetEntity="Contact\Entity\Selection", cascade={"persist"}, inversedBy="mailingList")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="selection_id", referencedColumnName="selection_id", nullable=false)
     * })
     *
     * @var \Contact\Entity\Selection
     */
    private $selection;
    /**
     * @ORM\Column(name="main", type="smallint", nullable=false)
     * @Annotation\Type("\Zend\Form\Element\Radio")
     * @Annotation\Attributes({"array":"mainTemplates"})
     * @Annotation\Attributes({"label":"txt-main", "required":"true"})
     * @Annotation\Required(true)
     *
     * @var int
     */
    private $main;

    /**
     * @return array
     */
    public function getPrivateTemplates()
    {
        return $this->privateTemplates;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * @param int $main
     */
    public function setMain($main)
    {
        $this->main = $main;
    }

    /**
     * @return \Contact\Entity\Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param \Contact\Entity\Selection $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }
}
