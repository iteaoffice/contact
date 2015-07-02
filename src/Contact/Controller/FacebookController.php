<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\Facebook;
use Contact\Form\SendMessage;
use General\Service\EmailServiceAwareInterface;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Contact
 *
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      bool isAllowed($resource, $action)
 */
class FacebookController extends ContactAbstractController implements EmailServiceAwareInterface
{
    /**
     * @return ViewModel
     */
    public function facebookAction()
    {
        /*
         * @var Facebook
         */
        $facebook = $this->getContactService()->findEntityById('facebook', $this->params('id'));
        $view =  new ViewModel(
            [
                'facebook'          => $facebook,
                'contacts'          => $this->getContactService()->findContactsInFacebook($facebook),
                'contactInFacebook' => $this->getContactService()->findContactInFacebook($facebook),
            ]);
        $view->setTemplate($this->getContactService()->getFacebookTemplate());

        return $view;
    }

    /**
     * Special action which produces an HTML version of the review calendar.
     *
     * @return ViewModel
     */
    public function sendMessageAction()
    {
        /*
         * @var Facebook
         */
        $facebook = $this->getContactService()->findEntityById('facebook', $this->params('id'));

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray()
        );

        $form = new SendMessage();
        $form->setData($data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formValues = $form->getData();

            if (isset($formValues['cancel'])) {
                return $this->redirect()->toRoute(
                    'community/contact/facebook/facebook',
                    ['id' => $facebook->getId()]
                );
            }
            /*
             * Send the email tot he office
             */
            $email = $this->getEmailService()->create();
            $email->setPersonal(false); //Sent 1 email to everyone
            $email->setFromContact($this->zfcUserAuthentication()->getIdentity());
            /*
             * Inject the contacts in the email
             */
            foreach ($this->getContactService()->findContactsInFacebook($facebook) as $contact) {
                $email->addTo($contact['contact']);
            }

            $email->setSubject(
                sprintf(
                    '[%s-%s] Message received from %s',
                    $facebook->getFacebook(),
                    $this->zfcUserAuthentication()->getIdentity()->getDisplayName()
                )
            );

            $email->setHtmlLayoutName('signature_twig');
            $email->setMessage(nl2br($form->getData()['message']));

            $this->getEmailService()->send();

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    _("txt-message-to-attendees-for-%s-has-been-sent"),
                    $facebook->getFacebook()
                )
            );

            return $this->redirect()->toRoute(
                'community/contact/facebook/facebook',
                ['id' => $facebook->getId()]
            );
        }

        return new ViewModel(
            [
                'form'     => $form,
                'facebook' => $facebook,
                'contacts' => $this->getContactService()->findContactsInFacebook($facebook),
            ]
        );
    }
}
