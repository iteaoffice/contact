<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Facebook;
use Contact\Form\SendMessage;
use Contact\Service\ContactService;
use General\Service\EmailService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;

/**
 * Class FacebookController
 *
 * @package Contact\Controller
 */
final class FacebookController extends ContactAbstractController
{
    private array $config;
    private ContactService $contactService;
    private EmailService $emailService;
    private TranslatorInterface $translator;

    public function __construct(
        array $config,
        ContactService $contactService,
        EmailService $emailService,
        TranslatorInterface $translator
    ) {
        $this->config = $config;
        $this->contactService = $contactService;
        $this->emailService = $emailService;
        $this->translator = $translator;
    }

    public function facebookAction(): ViewModel
    {
        /**
         * @var Facebook $facebook
         */
        $facebook = $this->contactService->find(Facebook::class, (int)$this->params('facebook'));

        if (null === $facebook) {
            return $this->notFoundAction();
        }

        //This is a trick. The FB parser does a query to get the contacts but does not inlude the activation.
        //By do a check first if the contact is activated we create the identity of the logged in user and overrule the query
        $this->identity()->isActivated();

        return new ViewModel(
            [
                'facebook'          => $facebook,
                'contacts'          => $this->contactService->findContactsInFacebook($facebook),
                'contactInFacebook' => $this->contactService->isContactInFacebook($this->identity(), $facebook),
            ]
        );
    }

    public function sendMessageAction()
    {
        /**
         * @var $facebook Facebook
         */
        $facebook = $this->contactService->find(Facebook::class, (int)$this->params('id'));

        if (null === $facebook) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new SendMessage();
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community/contact/facebook/facebook', ['id' => $facebook->getId()]);
            }

            if ($form->isValid()) {
                $formValues = $form->getData();

                if (isset($formValues['cancel'])) {
                    return $this->redirect()
                        ->toRoute('community/contact/facebook/facebook', ['id' => $facebook->getId()]);
                }

                $this->emailService->setWebInfo('/contact/facebook/message');
                /*
                 * Inject the contacts in the email
                 */
                foreach ($this->contactService->findContactsInFacebook($facebook) as $contact) {
                    $this->emailService->addTo($contact['contact']);
                }

                $variables = [
                    'facebook'    => $facebook->getFacebook(),
                    'message'     => nl2br($form->getData()['message']),
                    'sender_name' => $this->identity()->parseFullName()
                ];

                $this->emailService->addTemplateVariables($variables);
                $this->emailService->send();

                $this->flashMessenger()
                    ->addSuccessMessage(
                        sprintf(
                            $this->translator->translate('txt-message-to-members-of-facebook-$%s-has-been-sent'),
                            $facebook->getFacebook()
                        )
                    );

                return $this->redirect()->toRoute('community/contact/facebook/facebook', ['id' => $facebook->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'     => $form,
                'facebook' => $facebook,
                'contacts' => $this->contactService->findContactsInFacebook($facebook),
            ]
        );
    }
}
