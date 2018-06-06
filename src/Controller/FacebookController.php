<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Facebook;
use Contact\Form\SendMessage;
use Contact\Service\ContactService;
use General\Service\EmailService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class FacebookController
 *
 * @package Contact\Controller
 */
final class FacebookController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var EmailService
     */
    private $emailService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ContactService $contactService,
        EmailService $emailService,
        TranslatorInterface $translator
    ) {
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
                /*
                 * Send the email tot he office
                 */
                $email = $this->emailService->create();
                $email->setPersonal(false); //Send 1 email to everyone
                $email->setFromContact($this->identity());
                /*
                 * Inject the contacts in the email
                 */
                foreach ($this->contactService->findContactsInFacebook($facebook) as $contact) {
                    $email->addTo($contact['contact']);
                }

                $email->setSubject(
                    \sprintf(
                        '[%s] Message received from %s',
                        $facebook->getFacebook(),
                        $this->identity()->getDisplayName()
                    )
                );

                $email->setHtmlLayoutName('signature_twig');
                $email->setMessage(nl2br($form->getData()['message']));

                $this->emailService->send();

                $this->flashMessenger()
                    ->addSuccessMessage(
                        sprintf(
                            $this->translator->translate("txt-message-to-attendees-for-%s-has-been-sent"),
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
