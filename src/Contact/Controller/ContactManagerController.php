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

use Contact\Controller\Plugin\HandleImport;
use Contact\Form\Import;
use Contact\Form\Statistics;
use Contact\Service\StatisticsService;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContactManagerController.
 *
 * @method HandleImport handleImport()
 */
class ContactManagerController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function permitAction()
    {
        $contactService = $this->getContactService()->setContactId($this->getEvent()->getRouteMatch()->getParam('id'));

        $this->getAdminService()->findPermitContactByContact($contactService->getContact());

        return new ViewModel(
            [
                'contactService' => $contactService,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function impersonateAction()
    {
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray()
        );

        $contactService = $this->getContactService()->setContactId($this->getEvent()->getRouteMatch()->getParam('id'));
        $form = $this->getServiceLocator()->get('contact_impersonate_form');
        $form->setData($data);
        $deeplink = false;
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $data = $form->getData();
            //Create a target
            $target = $this->getDeeplinkService()->findEntityById('target', $data['target']);
            $key = (!empty($data['key']) ? $data['key'] : null);
            //Create a deeplink for the user which redirects to the profile-page
            $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contactService->getContact(), null, $key);
        }

        return new ViewModel(
            [
                'deeplink'       => $deeplink,
                'contactService' => $contactService,
                'form'           => $form,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function statisticsAction()
    {
        $filter = $this->getRequest()->getQuery()->get('filter', []);
        /*
         * @var StatisticsService
         */
        $statisticsService = $this->getServiceLocator()->get(StatisticsService::class);

        $form = new Statistics();
        $form->setData($_GET);

        $contacts = [];
        if ($this->getRequest()->isGet() && $form->isValid()) {
            $statisticsService->setFilter($form->getData());
//            $contacts = $statisticsService->getContacts();
        }

        return new ViewModel(['form' => $form, 'contacts' => $contacts]);
    }

    /**
     * @return ViewModel
     */
    public function importAction()
    {
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new Import();
        $form->setData($data);

        $handleImport = null;
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $handleImport = $this->handleImport(file_get_contents($data['file']['tmp_name']));
        }

        return new ViewModel(['form' => $form, 'handleImport' => $handleImport]);
    }

    /**
     * @return JsonModel
     */
    public function searchAction()
    {
        $search = $this->getRequest()->getPost()->get('search');

        $results = [];
        foreach ($this->getContactService()->searchContacts($search) as $result) {
            $text = trim(
                sprintf(
                    "%s %s",
                    $result['firstName'],
                    trim(sprintf("%s %s", $result['middleName'], $result['lastName']))
                )
            );

            /*
             * Do a fall-back to the email when the name is empty
             */
            if (strlen($text) === 0) {
                $text = $result['email'];
            }

            $results[] = [
                'value' => $result['id'],
                'text'  => $text,
            ];
        }

        return new JsonModel($results);
    }
}
