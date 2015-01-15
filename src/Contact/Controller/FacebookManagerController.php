<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Facebook
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Controller;

use Contact\Entity\Facebook;
use Zend\View\Model\ViewModel;

/**
 *
 */
class FacebookManagerController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        return new ViewModel(
            [
                'facebook' => $this->getContactService()->findAll('facebook'),
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        /**
         * @var $facebook Facebook
         */
        $facebook = $this->getContactService()->findEntityById('facebook', $this->params('id'));

        return new ViewModel(
            [
                'facebook' => $facebook,
                'contacts' => $this->getContactService()->findContactsInFacebook($facebook),
            ]
        );
    }

    /**
     * Create a new facebook
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $form = $this->getFormService()->prepare('facebook', null, $_POST);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /**
             * @var $facebook Facebook
             */
            $facebook = $this->getContactService()->newEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/facebook-manager/view',
                ['id' => $facebook->getId()]
            );
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Edit an facebook by finding it and call the corresponding form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $facebook = $this->getContactService()->findEntityById(
            'facebook',
            $this->params('id')
        );
        $form = $this->getFormService()->prepare($facebook->get('entity_name'), $facebook, $_POST);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            if (isset($data['delete'])) {
                /**
                 * @var $facebook Facebook
                 */
                $facebook = $form->getData();

                $this->getContactService()->removeEntity($facebook);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    sprintf(_("txt-facebook-has-successfully-been-deleted"))
                );

                return $this->redirect()->toRoute('zfcadmin/facebook-manager/list');
            }

            if (!isset($data['cancel'])) {
                $facebook = $this->getContactService()->updateEntity($facebook);
            }

            return $this->redirect()->toRoute(
                'zfcadmin/facebook-manager/view',
                ['id' => $facebook->getId()]
            );
        }

        return new ViewModel(['form' => $form]);
    }
}
