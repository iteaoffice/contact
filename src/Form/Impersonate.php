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

namespace Contact\Form;

use Deeplink\Entity\Target;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

/**
 * Class Impersonate
 *
 * @package Contact\Form
 */
final class Impersonate extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $this->add(
            [
                'type'       => EntitySelect::class,
                'name'       => 'target',
                'options'    => [
                    'target_class'   => Target::class,
                    'object_manager' => $entityManager,
                    'find_method'    => [
                        'name'   => 'findTargetsWithRoute',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [],
                        ],
                    ],
                    'help-block'     => _('txt-deeplink-target-form-element-explanation'),
                ],
                'attributes' => [
                    'label' => _('txt-target'),
                    'class' => 'form-control',
                    'id'    => 'target',
                ],
            ]
        );
        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'key',
                'options'    => [
                    'help-block' => _('txt-deeplink-key-form-element-explanation'),
                ],
                'attributes' => [
                    'label'       => _('txt-key'),
                    'class'       => 'form-control',
                    'id'          => 'key',
                    'placeholder' => _('txt-key'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
    }
}
