<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
use Contact\Entity;
use Contact\Form;
use Contact\Options;

return array(
    'initializers' => array(
        'contact_service_initializer' => 'Contact\Service\ServiceInitializer'
    ),
    'factories'    => array(

        'contact_community_options' => function ($sm) {
            $config = $sm->get('Config');

            return new Options\ModuleOptions(isset($config['community']) ? $config['community'] : array());
        },
        'contact_module_config'     => 'Contact\Factory\ConfigServiceFactory',
        'contact_cache'             => 'Contact\Factory\CacheFactory',
        'contact_contact_form'      => function ($sm) {
            return new Form\Contact($sm, new Entity\Contact());
        },
        'Contact\Provider\Identity\AuthenticationIdentityProvider'
                                    => 'Contact\Factory\AuthenticationIdentityProviderServiceFactory',
    ),
    'invokables'   => array(
        'contact_impersonate_form'     => 'Contact\Form\Impersonate',
        'contact_contact_service'      => 'Contact\Service\ContactService',
        'contact_address_service'      => 'Contact\Service\AddressService',
        'contact_form_service'         => 'Contact\Service\FormService',
        'contact_contact_form_filter'  => 'Contact\Form\FilterContact',
        'contact_password_form'        => 'Contact\Form\Password',
        'contact_password_form_filter' => 'Contact\Form\PasswordFilter',

    )
);
