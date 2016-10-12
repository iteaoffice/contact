<?php
// Generated by ZF2's ./bin/classmap_generator.php
return array(
    'Contact\Repository\Contact'                                   => __DIR__ . '/src/Repository/Contact.php',
    'Contact\Repository\Selection'                                 => __DIR__ . '/src/Repository/Selection.php',
    'Contact\Repository\Facebook'                                  => __DIR__ . '/src/Repository/Facebook.php',
    'Contact\Repository\Address'                                   => __DIR__ . '/src/Repository/Address.php',
    'Contact\Options\CommunityOptionsInterface'                    => __DIR__ . '/src/Options/CommunityOptionsInterface.php',
    'Contact\Options\ModuleOptions'                                => __DIR__ . '/src/Options/ModuleOptions.php',
    'Contact\Search\Factory\ContactSearchFactory'                  => __DIR__ . '/src/Search/Factory/ContactSearchFactory.php',
    'Contact\Search\Service\ContactSearchService'                  => __DIR__ . '/src/Search/Service/ContactSearchService.php',
    'Contact\Module'                                               => __DIR__ . '/src/Module.php',
    'Contact\Provider\Identity\AuthenticationIdentityProvider'     => __DIR__ . '/src/Provider/Identity/AuthenticationIdentityProvider.php',
    'Contact\Hydrator\Profile'                                     => __DIR__ . '/src/Hydrator/Profile.php',
    'Contact\Factory\AuthenticationIdentityProviderServiceFactory' => __DIR__ . '/src/Factory/AuthenticationIdentityProviderServiceFactory.php',
    'Contact\Factory\ModuleOptionsFactory'                         => __DIR__ . '/src/Factory/ModuleOptionsFactory.php',
    'Contact\Factory\ContactServiceFactory'                        => __DIR__ . '/src/Factory/ContactServiceFactory.php',
    'Contact\Factory\InputFilterFactory'                           => __DIR__ . '/src/Factory/InputFilterFactory.php',
    'Contact\Factory\FormServiceFactory'                           => __DIR__ . '/src/Factory/FormServiceFactory.php',
    'Contact\Factory\SelectionServiceFactory'                      => __DIR__ . '/src/Factory/SelectionServiceFactory.php',
    'Contact\Factory\AddressServiceFactory'                        => __DIR__ . '/src/Factory/AddressServiceFactory.php',
    'Contact\Entity\SelectionSql'                                  => __DIR__ . '/src/Entity/SelectionSql.php',
    'Contact\Entity\OptIn'                                         => __DIR__ . '/src/Entity/OptIn.php',
    'Contact\Entity\PhoneType'                                     => __DIR__ . '/src/Entity/PhoneType.php',
    'Contact\Entity\Photo'                                         => __DIR__ . '/src/Entity/Photo.php',
    'Contact\Entity\Note'                                          => __DIR__ . '/src/Entity/Note.php',
    'Contact\Entity\Contact'                                       => __DIR__ . '/src/Entity/Contact.php',
    'Contact\Entity\Web'                                           => __DIR__ . '/src/Entity/Web.php',
    'Contact\Entity\HydrateInterface'                              => __DIR__ . '/src/Entity/HydrateInterface.php',
    'Contact\Entity\Selection'                                     => __DIR__ . '/src/Entity/Selection.php',
    'Contact\Entity\AddressTypeSort'                               => __DIR__ . '/src/Entity/AddressTypeSort.php',
    'Contact\Entity\ContactOrganisation'                           => __DIR__ . '/src/Entity/ContactOrganisation.php',
    'Contact\Entity\Cv'                                            => __DIR__ . '/src/Entity/Cv.php',
    'Contact\Entity\OpenId'                                        => __DIR__ . '/src/Entity/OpenId.php',
    'Contact\Entity\Email'                                         => __DIR__ . '/src/Entity/Email.php',
    'Contact\Entity\Community'                                     => __DIR__ . '/src/Entity/Community.php',
    'Contact\Entity\Facebook'                                      => __DIR__ . '/src/Entity/Facebook.php',
    'Contact\Entity\Dnd'                                           => __DIR__ . '/src/Entity/Dnd.php',
    'Contact\Entity\EntityAbstract'                                => __DIR__ . '/src/Entity/EntityAbstract.php',
    'Contact\Entity\Address'                                       => __DIR__ . '/src/Entity/Address.php',
    'Contact\Entity\DndObject'                                     => __DIR__ . '/src/Entity/DndObject.php',
    'Contact\Entity\SelectionMailinglist'                          => __DIR__ . '/src/Entity/SelectionMailinglist.php',
    'Contact\Entity\Profile'                                       => __DIR__ . '/src/Entity/Profile.php',
    'Contact\Entity\Link'                                          => __DIR__ . '/src/Entity/Link.php',
    'Contact\Entity\AddressType'                                   => __DIR__ . '/src/Entity/AddressType.php',
    'Contact\Entity\EntityInterface'                               => __DIR__ . '/src/Entity/EntityInterface.php',
    'Contact\Entity\Phone'                                         => __DIR__ . '/src/Entity/Phone.php',
    'Contact\Entity\SelectionContact'                              => __DIR__ . '/src/Entity/SelectionContact.php',
    'Contact\Service\ContactService'                               => __DIR__ . '/src/Service/ContactService.php',
    'Contact\Service\AddressService'                               => __DIR__ . '/src/Service/AddressService.php',
    'Contact\Service\SelectionService'                             => __DIR__ . '/src/Service/SelectionService.php',
    'Contact\Service\FormService'                                  => __DIR__ . '/src/Service/FormService.php',
    'Contact\Service\ServiceInterface'                             => __DIR__ . '/src/Service/ServiceInterface.php',
    'Contact\Service\ServiceAbstract'                              => __DIR__ . '/src/Service/ServiceAbstract.php',
    'Contact\View\Factory\ViewHelperFactory'                       => __DIR__ . '/src/View/Factory/ViewHelperFactory.php',
    'Contact\View\Helper\PhoneLink'                                => __DIR__ . '/src/View/Helper/PhoneLink.php',
    'Contact\View\Helper\ImageAbstract'                            => __DIR__ . '/src/View/Helper/ImageAbstract.php',
    'Contact\View\Helper\ContactHandler'                           => __DIR__ . '/src/View/Helper/ContactHandler.php',
    'Contact\View\Helper\LinkAbstract'                             => __DIR__ . '/src/View/Helper/LinkAbstract.php',
    'Contact\View\Helper\NoteLink'                                 => __DIR__ . '/src/View/Helper/NoteLink.php',
    'Contact\View\Helper\FacebookLink'                             => __DIR__ . '/src/View/Helper/FacebookLink.php',
    'Contact\View\Helper\CreateContactFromArray'                   => __DIR__ . '/src/View/Helper/CreateContactFromArray.php',
    'Contact\View\Helper\ContactPhoto'                             => __DIR__ . '/src/View/Helper/ContactPhoto.php',
    'Contact\View\Helper\CommunityLink'                            => __DIR__ . '/src/View/Helper/CommunityLink.php',
    'Contact\View\Helper\SelectionLink'                            => __DIR__ . '/src/View/Helper/SelectionLink.php',
    'Contact\View\Helper\ContactLink'                              => __DIR__ . '/src/View/Helper/ContactLink.php',
    'Contact\View\Helper\AddressLink'                              => __DIR__ . '/src/View/Helper/AddressLink.php',
    'Contact\View\Helper\AbstractViewHelper'                       => __DIR__ . '/src/View/Helper/AbstractViewHelper.php',
    'Contact\Version\Version'                                      => __DIR__ . '/src/Version/Version.php',
    'Contact\Acl\Factory\AssertionFactory'                         => __DIR__ . '/src/Acl/Factory/AssertionFactory.php',
    'Contact\Acl\Assertion\AssertionAbstract'                      => __DIR__ . '/src/Acl/Assertion/AssertionAbstract.php',
    'Contact\Acl\Assertion\Note'                                   => __DIR__ . '/src/Acl/Assertion/Note.php',
    'Contact\Acl\Assertion\Contact'                                => __DIR__ . '/src/Acl/Assertion/Contact.php',
    'Contact\Acl\Assertion\Selection'                              => __DIR__ . '/src/Acl/Assertion/Selection.php',
    'Contact\Acl\Assertion\Facebook'                               => __DIR__ . '/src/Acl/Assertion/Facebook.php',
    'Contact\Acl\Assertion\Address'                                => __DIR__ . '/src/Acl/Assertion/Address.php',
    'Contact\Acl\Assertion\Phone'                                  => __DIR__ . '/src/Acl/Assertion/Phone.php',
    'Contact\Controller\ContactAdminController'                    => __DIR__ . '/src/Controller/ContactAdminController.php',
    'Contact\Controller\FacebookManagerController'                 => __DIR__ . '/src/Controller/FacebookManagerController.php',
    'Contact\Controller\NoteManagerController'                     => __DIR__ . '/src/Controller/NoteManagerController.php',
    'Contact\Controller\AddressManagerController'                  => __DIR__ . '/src/Controller/AddressManagerController.php',
    'Contact\Controller\ProfileController'                         => __DIR__ . '/src/Controller/ProfileController.php',
    'Contact\Controller\ContactAbstractController'                 => __DIR__ . '/src/Controller/ContactAbstractController.php',
    'Contact\Controller\Factory\PluginFactory'                     => __DIR__ . '/src/Controller/Factory/PluginFactory.php',
    'Contact\Controller\Factory\ControllerFactory'                 => __DIR__ . '/src/Controller/Factory/ControllerFactory.php',
    'Contact\Controller\FacebookController'                        => __DIR__ . '/src/Controller/FacebookController.php',
    'Contact\Controller\ConsoleController'                         => __DIR__ . '/src/Controller/ConsoleController.php',
    'Contact\Controller\ContactManagerController'                  => __DIR__ . '/src/Controller/ContactManagerController.php',
    'Contact\Controller\Plugin\GetFilter'                          => __DIR__ . '/src/Controller/Plugin/GetFilter.php',
    'Contact\Controller\Plugin\HandleImport'                       => __DIR__ . '/src/Controller/Plugin/HandleImport.php',
    'Contact\Controller\SelectionManagerController'                => __DIR__ . '/src/Controller/SelectionManagerController.php',
    'Contact\Controller\PhoneManagerController'                    => __DIR__ . '/src/Controller/PhoneManagerController.php',
    'Contact\Controller\ContactController'                         => __DIR__ . '/src/Controller/ContactController.php',
    'Contact\InputFilter\ContactFilter'                            => __DIR__ . '/src/InputFilter/ContactFilter.php',
    'Contact\InputFilter\SelectionFilter'                          => __DIR__ . '/src/InputFilter/SelectionFilter.php',
    'Contact\InputFilter\FacebookFilter'                           => __DIR__ . '/src/InputFilter/FacebookFilter.php',
    'Contact\InputFilter\PasswordFilter'                           => __DIR__ . '/src/InputFilter/PasswordFilter.php',
    'Contact\Navigation\Factory\NavigationInvokableFactory'        => __DIR__ . '/src/Navigation/Factory/NavigationInvokableFactory.php',
    'Contact\Navigation\Factory\ContactNavigationServiceFactory'   => __DIR__ . '/src/Navigation/Factory/ContactNavigationServiceFactory.php',
    'Contact\Navigation\Service\ContactNavigationService'          => __DIR__ . '/src/Navigation/Service/ContactNavigationService.php',
    'Contact\Navigation\Service\NavigationServiceAbstract'         => __DIR__ . '/src/Navigation/Service/NavigationServiceAbstract.php',
    'Contact\Navigation\Invokable\AddressLabel'                    => __DIR__ . '/src/Navigation/Invokable/AddressLabel.php',
    'Contact\Navigation\Invokable\FacebookLabel'                   => __DIR__ . '/src/Navigation/Invokable/FacebookLabel.php',
    'Contact\Navigation\Invokable\SelectionLabel'                  => __DIR__ . '/src/Navigation/Invokable/SelectionLabel.php',
    'Contact\Navigation\Invokable\ContactLabel'                    => __DIR__ . '/src/Navigation/Invokable/ContactLabel.php',
    'Contact\Navigation\Invokable\PhoneLabel'                      => __DIR__ . '/src/Navigation/Invokable/PhoneLabel.php',
    'Contact\Form\ContactPhotoFieldset'                            => __DIR__ . '/src/Form/ContactPhotoFieldset.php',
    'Contact\Form\Search'                                          => __DIR__ . '/src/Form/Search.php',
    'Contact\Form\ContactFilter'                                   => __DIR__ . '/src/Form/ContactFilter.php',
    'Contact\Form\ContactCommunityFieldset'                        => __DIR__ . '/src/Form/ContactCommunityFieldset.php',
    'Contact\Form\ContactProfileFieldset'                          => __DIR__ . '/src/Form/ContactProfileFieldset.php',
    'Contact\Form\SelectionFilter'                                 => __DIR__ . '/src/Form/SelectionFilter.php',
    'Contact\Form\SendMessage'                                     => __DIR__ . '/src/Form/SendMessage.php',
    'Contact\Form\ContactOrganisationFieldset'                     => __DIR__ . '/src/Form/ContactOrganisationFieldset.php',
    'Contact\Form\Password'                                        => __DIR__ . '/src/Form/Password.php',
    'Contact\Form\ObjectFieldset'                                  => __DIR__ . '/src/Form/ObjectFieldset.php',
    'Contact\Form\ContactFieldset'                                 => __DIR__ . '/src/Form/ContactFieldset.php',
    'Contact\Form\Impersonate'                                     => __DIR__ . '/src/Form/Impersonate.php',
    'Contact\Form\Element\Contact'                                 => __DIR__ . '/src/Form/Element/Contact.php',
    'Contact\Form\View\Helper\ContactFormElement'                  => __DIR__ . '/src/Form/View/Helper/ContactFormElement.php',
    'Contact\Form\CreateObject'                                    => __DIR__ . '/src/Form/CreateObject.php',
    'Contact\Form\ContactAddressFieldset'                          => __DIR__ . '/src/Form/ContactAddressFieldset.php',
    'Contact\Form\Import'                                          => __DIR__ . '/src/Form/Import.php',
    'Contact\Form\Profile'                                         => __DIR__ . '/src/Form/Profile.php',
    'Contact\Form\SelectionContacts'                               => __DIR__ . '/src/Form/SelectionContacts.php',
);
