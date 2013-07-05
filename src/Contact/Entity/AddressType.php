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
use Zend\Permissions\Acl\Resource\ResourceInterface;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation AS Gedmo;

/**
 * AddressType
 *
 * @ORM\Table(name="address_type")
 * @ORM\Entity
 */
class AddressType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     */
    private $type;


}
