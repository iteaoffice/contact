<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Phone
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Contact;
use Contact\Entity\Phone;
use Zend\View\Model\ViewModel;

/**
 *
 */
class PhoneManagerController extends ContactAbstractController
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

        $form = $this->getFormService()->prepare(Phone::class, null, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (!isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var $phone Phone
                 */
                $phone = $form->getData();
                $phone->setContact($contact);
                $this->getContactService()->newEntity($phone);
            }

            return $this->redirect()
                ->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()], ['fragment' => 'phone']);
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
         * @var $phone Phone
         */
        $phone = $this->getContactService()->findEntityById(Phone::class, $this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->getFormService()->prepare($phone, $phone, $data);

        if ($this->getRequest()->isPost()) {
            /**
             * Handle the delete request
             */
            if (isset($data['delete'])) {
                $this->getContactService()->removeEntity($phone);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(sprintf($this->translate("txt-phone-has-successfully-been-deleted")));

                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/contact-admin/view',
                        ['id' => $phone->getContact()->getId()],
                        ['fragment' => 'phone']
                    );
            }

            if (!isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var Phone $phone
                 */
                $phone = $form->getData();
                $phone = $this->getContactService()->updateEntity($phone);
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact-admin/view',
                ['id' => $phone->getContact()->getId()],
                ['fragment' => 'phone']
            );
        }

        return new ViewModel(
            [
                'form'    => $form,
                'contact' => $phone->getContact(),

            ]
        );
    }
}
