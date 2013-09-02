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

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Annotation;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity for the Contact
 *
 * @ORM\Table(name="organisation_type")
 * @ORM\Entity
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("organisation_type")
 *
 * @category    Contact
 * @package     Entity
 */
class OrganisationType
{
    /**
     * Needed to fill the ACL for this entity
     */
    const INVOICE_YES       = 1;
    const INVOICE_YES_VALUE = "txt-invoice";
    const INVOICE_NO        = 0;
    const INVOICE_NO_VALUE  = "txt-no-invoice";

    /**
     * @var array
     */
    protected $invoiceTemplates = array(
        self::INVOICE_YES => self::INVOICE_YES_VALUE,
        self::INVOICE_NO  => self::INVOICE_NO_VALUE,
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(name="description", type="string", length=40, nullable=true)
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(name="invoice", type="smallint", nullable=false)
     * @var integer
     */
    private $invoice;
    /**
     * @ORM\OneToMany(targetEntity="Contact\Entity\Organisation", cascade={"persist"}, mappedBy="type")
     * @Annotation\Exclude()
     * @var \Contact\Entity\Organisation[]
     */
    private $organisation;
}
