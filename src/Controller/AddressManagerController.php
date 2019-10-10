<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Address
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Address;
use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class AddressManagerController
 *
 * @package Contact\Controller
 */
final class AddressManagerController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ContactService $contactService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->contactService = $contactService;
        $this->formService = $formService;
        $this->translator = $translator;
    }


    public function newAction()
    {
        /**
         * @var $contact Contact
         */
        $contact = $this->contactService->findContactById((int)$this->params('contact'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Address::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (!isset($data['cancel']) && $form->isValid()) {
                /** @var Address $address */
                $address = $form->getData();
                $address->setContact($contact);
                $this->contactService->save($address);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-address-has-successfully-been-created'))
                );
            }

            return $this->redirect()
                ->toRoute(
                    'zfcadmin/contact/view/address',
                    ['id' => $contact->getId()]
                );
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $contact,

            ]
        );
    }

    public function editAction()
    {
        /**
         * @var $address Address
         */
        $address = $this->contactService->find(Address::class, (int)$this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($address, $data);

        if ($this->getRequest()->isPost()) {
            /**
             * Handle the delete request
             */
            if (isset($data['delete'])) {
                $this->contactService->delete($address);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-address-has-successfully-been-deleted'))
                );

                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/contact/view/address',
                        ['id' => $address->getContact()->getId()]
                    );
            }

            if (!isset($data['cancel']) && $form->isValid()) {
                $address = $form->getData();
                $this->contactService->save($address);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-address-has-successfully-been-updated'))
                );
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact/view/address',
                ['id' => $address->getContact()->getId()]
            );
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $address->getContact(),
            ]
        );
    }
}
