<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Facebook
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Facebook;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;

/**
 * Class FacebookManagerController
 * @package Contact\Controller
 */
final class FacebookManagerController extends ContactAbstractController
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

    public function listAction(): ViewModel
    {
        $facebook = $this->contactService->findAll(Facebook::class);

        return new ViewModel(
            [
                'facebook' => $facebook,
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        /**
         * @var Facebook $facebook
         */
        $facebook = $this->contactService->find(Facebook::class, (int)$this->params('id'));

        if (null === $facebook) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'facebook' => $facebook,
                'contacts' => $this->contactService->findContactsInFacebook($facebook),
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Facebook::class, $data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /**
             * @var Facebook $facebook
             */
            $facebook = $form->getData();
            $this->contactService->save($facebook);

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate('txt-facebook-%s-has-successfully-been-deleted'),
                    $facebook->getFacebook()
                )
            );

            return $this->redirect()->toRoute('zfcadmin/facebook/view', ['id' => $facebook->getId()]);
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        /**
         * @var $facebook Facebook
         */
        $facebook = $this->contactService->find(Facebook::class, (int)$this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($facebook, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete'])) {
                /*
                 * @var Facebook
                 */
                $facebook = $form->getData();

                $this->contactService->delete($facebook);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-facebook-has-successfully-been-deleted'))
                );

                return $this->redirect()->toRoute('zfcadmin/facebook/list');
            }

            if (! isset($data['cancel']) && $form->isValid()) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-facebook-%s-has-successfully-been-updated'),
                        $facebook->getFacebook()
                    )
                );

                if (! isset($data['contact_entity_facebook']['access'])) {
                    $facebook->setAccess(null);
                }

                $this->contactService->save($facebook);

                return $this->redirect()->toRoute('zfcadmin/facebook/view', ['id' => $facebook->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
