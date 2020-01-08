<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Phone
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Phone;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;

/**
 * Class PhoneManagerController
 *
 * @package Contact\Controller
 */
final class PhoneManagerController extends ContactAbstractController
{
    private ContactService $contactService;
    private FormService $formService;
    private TranslatorInterface $translator;

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
        $contact = $this->contactService->findContactById((int)$this->params('contact'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Phone::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (! isset($data['cancel']) && $form->isValid()) {
                /**
                 * @var $phone Phone
                 */
                $phone = $form->getData();
                $phone->setContact($contact);
                $this->contactService->save($phone);
            }

            return $this->redirect()
                ->toRoute('zfcadmin/contact/view/phone', ['id' => $contact->getId()], ['fragment' => 'phone']);
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
         * @var $phone Phone
         */
        $phone = $this->contactService->find(Phone::class, (int)$this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($phone, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                $this->contactService->delete($phone);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-phone-has-successfully-been-deleted'))
                );

                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/contact/view/phone',
                        ['id' => $phone->getContact()->getId()]
                    );
            }

            if (! isset($data['cancel']) && $form->isValid()) {
                $phone = $form->getData();
                $this->contactService->save($phone);
            }

            return $this->redirect()->toRoute(
                'zfcadmin/contact/view/phone',
                ['id' => $phone->getContact()->getId()]
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
