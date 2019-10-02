<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntitySelect;
use General\Entity;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class ContactFilter
 *
 * @package Contact\Form
 */
class ContactFilter extends Form
{
    /**
     * ContactFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'search',
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-search'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'    => EntityMultiCheckbox::class,
                'name'    => 'gender',
                'options' => [
                    'target_class'   => Entity\Gender::class,
                    'inline'         => true,
                    'object_manager' => $entityManager,
                    'label'          => _("txt-gender"),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'    => EntitySelect::class,
                'name'    => 'country',
                'options' => [
                    'target_class'   => Entity\Country::class,
                    'inline'         => true,
                    'object_manager' => $entityManager,
                    'label'          => _("txt-country"),
                    'find_method'    => [
                        'name'   => 'findForForm',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => ['country' => 'ASC'],
                        ],
                    ],
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'    => 'multicheckbox',
                'name'    => 'options',
                'options' => [
                    'value_options' => [
                        'hasOrganisation'    => _("txt-has-organisation"),
                        'hasNoOrganisation'  => _("txt-has-no-organisation"),
                        'includeDeactivated' => _("txt-include-deactivated"),
                        'onlyDeactivated'    => _("txt-only-deactivated"),
                    ],
                    'inline'        => true,
                    'label'         => _("txt-organisation"),
                ],
            ]
        );

        $this->add($filterFieldset);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-filter'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'clear',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
