<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     Form
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Project\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Callback;

use Doctrine\ORM\EntityManager;


class FilterProjectBasics extends InputFilter
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->add(array(
            'name'       => 'project',
            'required'   => false,
            'filters'    => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 5,
                        'max'      => 15,
                    ),
                ),
                array(
                    'name'    => '\DoctrineModule\Validator\UniqueObject',
                    'options' => array(
                        'object_repository' => $entityManager->getRepository('Project\Entity\Project'),
                        'object_manager'    => $entityManager,
                        'fields'            => array('project'),
                    ),
                )
            ),
        ));

        $this->add(array(
                'name'       => 'dateEnd',
                'required'   => true,
                'validators' => array(
                    array(
                        'name' => 'Date',
                    ),
                    array(
                        'name'    => 'Callback',
                        'options' => array(
                            'messages' => array(
                                Callback::INVALID_VALUE => 'The end date should be greater than start date',
                            ),
                            'callback' => function ($value, $context = array()) {
                                    $startDate = \DateTime::createFromFormat('Y-m-d', $context['dateStart']);
                                    $endDate   = \DateTime::createFromFormat('Y-m-d', $value);

                                    return $endDate >= $startDate;
                                },
                        ),
                    ),
                ),
            )
        );
    }
}
