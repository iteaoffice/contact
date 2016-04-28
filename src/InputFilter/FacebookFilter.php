<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Contact\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Zend\InputFilter\InputFilter;

/**
 * Jield webdev copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2015 Jield (http://jield.nl)
 */
class FacebookFilter extends InputFilter
{
    /**
     * ContactFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name'       => 'facebook',
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 80,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'     => 'public',
            'required' => true,
        ]);
        $inputFilter->add([
            'name'     => 'canSendMessage',
            'required' => true,
        ]);

        $this->add($inputFilter, 'contact_entity_facebook');
    }
}
