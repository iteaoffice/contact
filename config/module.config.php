<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Config
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
$config = array(
    'controllers' => array(
        'invokables' => array(
            'contact' => 'Contact\Controller\ContactController',
            'contact-manager' => 'Contact\Controller\ContactManagerController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'contactLink' => 'Contact\View\Helper\ContactLink',
            'contactIcon' => 'Contact\View\Helper\ContactIcon',
            'facilityLink' => 'Contact\View\Helper\FacilityLink',
            'areaLink' => 'Contact\View\Helper\AreaLink',
            'area2Link' => 'Contact\View\Helper\Area2Link',
            'subAreaLink' => 'Contact\View\Helper\SubAreaLink',
            'operAreaLink' => 'Contact\View\Helper\OperAreaLink',
            'operSubAreaLink' => 'Contact\View\Helper\OperSubAreaLink',
            'messageLink' => 'Contact\View\Helper\MessageLink',
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'contact-assertion' => 'Contact\Acl\Assertion\Contact',
        ),
        'invokables' => array(
            'contact_generic_service' => 'Contact\Service\ContactService',
            'contact_form_service' => 'Contact\Service\FormService',
            'contact_contact_form_filter' => 'Contact\Form\FilterCreateContact',
            'contact_facility_form_filter' => 'Contact\Form\FilterCreateFacility',
            'contact_area_form_filter' => 'Contact\Form\FilterCreateArea',
            'contact_area2_form_filter' => 'Contact\Form\FilterCreateArea2',
            'contact_sub_area_form_filter' => 'Contact\Form\FilterCreateSubArea',
            'contact_oper_area_form_filter' => 'Contact\Form\FilterCreateOperArea',
            'contact_oper_sub_area_form_filter' => 'Contact\Form\FilterCreateOperSubArea',
            'contact_message_form_filter' => 'Contact\Form\FilterCreateMessage',

        )
    ),
    'doctrine' => array(
        'driver' => array(
            'contact_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/Contact/Entity/'
                )
            ),
            'orm_default' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'Contact\Entity' => 'contact_annotation_driver',
                )
            )
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                )
            ),
        ),
    )
);

$configFiles = array(
    __DIR__ . '/module.config.routes.php',
    __DIR__ . '/module.config.navigation.php',
    __DIR__ . '/module.config.authorize.php',
);

foreach ($configFiles as $configFile) {
    $config = Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
}

return $config;
