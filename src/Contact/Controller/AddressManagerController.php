<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Address
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

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
     * Create a new address.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        /**
         * @var $contact Contact
         */
        $contact = $this->getContactService()->setContactId($this->params('contact'))->getContact();

        if (is_null($contact)) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray()
        );

        $form = $this->getFormService()->prepare('address', null, $data);
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

            return $this->redirect()->toRoute(
                'zfcadmin/contact-admin/view',
                ['id' => $contact->getId()],
                ['fragment' => 'address']
            );
        }

        return new ViewModel([
            'form'    => $form,
            'contact' => $contact,

        ]);
    }

    /**
     * Edit an address by finding it and call the corresponding form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        /**
         * @var $address Address
         */
        $address = $this->getContactService()->findEntityById('address', $this->params('id'));
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray()
        );
        $form = $this->getFormService()->prepare($address->get('entity_name'), $address, $data);

        if ($this->getRequest()->isPost()) {
            /**
             * Handle the delete request
             */
            if (isset($data['delete'])) {
                $this->getContactService()->removeEntity($address);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf($this->translate("txt-address-has-successfully-been-deleted"))
                );

                return $this->redirect()->toRoute(
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

        return new ViewModel([
            'form'    => $form,
            'contact' => $address->getContact()

        ]);
    }
}
