<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
use Contact\Form;
use Contact\Options;
use Contact\Entity;

return array(
    'factories' => array(
        'contact_community_options' => function ($sm) {
                $config = $sm->get('Config');

                return new Options\ModuleOptions(isset($config['community']) ? $config['community'] : array());
            },
        'contact_contact_form'      => function ($sm) {
                return new Form\Contact($sm, new Entity\Contact());
            },

        'Contact\Provider\Identity\AuthenticationIdentityProvider'
                                    => 'Contact\Service\AuthenticationIdentityProviderServiceFactory',
    ),
);
