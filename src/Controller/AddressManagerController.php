<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Address
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Address;
use Contact\Entity\Contact;
use Zend\View\Model\ViewModel;

/**
 *
 */
class AddressManagerController extends ContactAbstractController
{
    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        /**
         * @var $contact Contact
         */
        $contact = $this->getContactService()->findContactById($this->params('contact'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->getFormService()->prepare(Address::class, null, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (!isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var $address Address
                 */
                $address = $form->getData();
                $address->setContact($contact);
                $this->getContactService()->newEntity($address);
            }

            return $this->redirect()
                ->toRoute(
                    'zfcadmin/contact-admin/view',
                    ['id' => $contact->getId()],
                    ['fragment' => 'address']
                );
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $contact,

            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /**
         * @var $address Address
         */
        $address = $this->getContactService()->findEntityById(Address::class, $this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->getFormService()->prepare($address, $address, $data);

        if ($this->getRequest()->isPost()) {
            /**
             * Handle the delete request
             */
            if (isset($data['delete'])) {
                $this->getContactService()->removeEntity($address);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf($this->translate("txt-address-has-successfully-been-deleted")));

                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/contact-admin/view',
                        ['id' => $address->getContact()->getId()],
                        ['fragment' => 'address']
                    );
            }

            if (!isset($data['cancel']) && $form->isValid()) {
                /*
                * @var Address
                */
                $address = $form->getData();
                $address = $this->getContactService()->updateEntity($address);
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact-admin/view',
                ['id' => $address->getContact()->getId()],
                ['fragment' => 'address']
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
