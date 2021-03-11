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

use Contact\Entity\Profile;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\Uri;

/**
 * Class ProfileFieldset
 *
 * @package Contact\Form\Profile
 */
final class ProfileFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('profile');

        $this->add(
            [
                'type'    => Element\Radio::class,
                'name'    => 'visible',
                'options' => [
                    'label'         => _('txt-profile-visibility-label'),
                    'help-block'    => _('txt-profile-visibility-help-block'),
                    'value_options' => Profile::getVisibleTemplates(),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Textarea::class,
                'name'       => 'description',
                'options'    => [
                    'label'      => _('txt-profile-expertise-label'),
                    'help-block' => _('txt-profile-expertise-help-block'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-expertise'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Url::class,
                'name'       => 'linkedIn',
                'options'    => [
                    'label'      => _('txt-profile-linked-in-label'),
                    'help-block' => _('txt-profile-linked-in-help-block'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-linked-in-profile'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'linkedIn' => [
                'required'   => false,
                'validators' => [
                    new Uri(),
                    new Callback(
                        [
                            'messages' => [
                                Callback::INVALID_VALUE => 'Please give a LinkedIn address (URI should contain linkedin.com)',
                            ],
                            'callback' => static function (string $value) {
                                return strpos($value, 'linkedin.com') !== false;
                            },
                        ]
                    )
                ]
            ],
        ];
    }
}
