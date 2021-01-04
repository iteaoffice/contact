<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Form\Profile;

use Contact\Entity\PhoneType;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;

use function sprintf;

/**
 * Class PhoneFieldset
 *
 * @package Contact\Form\Profile
 */
final class PhoneFieldset extends Fieldset
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('phone');
        /** @var PhoneType $phoneType */
        foreach ($entityManager->getRepository(PhoneType::class)->findAll(PhoneType::class) as $phoneType) {
            if (in_array($phoneType->getId(), [PhoneType::PHONE_TYPE_DIRECT, PhoneType::PHONE_TYPE_MOBILE], true)) {
                $this->add(
                    [
                        'type'       => Element\Text::class,
                        'name'       => $phoneType->getId(),
                        'options'    => [
                            'label' => sprintf(_('%s Phone number'), $phoneType->getType()),
                        ],
                        'attributes' => [
                            'class'       => 'form-control',
                            'placeholder' => sprintf(_('Give %s phone number'), $phoneType->getType()),
                        ],
                    ]
                );
            }
        }
    }
}
