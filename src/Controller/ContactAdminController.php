<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\OptIn;
use Contact\Form\ContactFilter;
use Contact\Form\Impersonate;
use Contact\Form\Import;
use Deeplink\Entity\Target;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContactManagerController.
 *
 *
 */
class ContactAdminController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $contactQuery = $this->getContactService()->findEntitiesFiltered(Contact::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new ContactFilter($this->getEntityManager());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'      => $paginator,
            'form'           => $form,
            'encodedFilter'  => urlencode($filterPlugin->getHash()),
            'order'          => $filterPlugin->getOrder(),
            'direction'      => $filterPlugin->getDirection(),
            'projectService' => $this->getProjectService(),
        ]);
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));
        $selections = $this->getSelectionService()->findSelectionsByContact($contact);
        $optIn = $this->getContactService()->findAll(OptIn::class);

        if (is_null($contact)) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'contact'            => $contact,
            'contactService'     => $this->getContactService(),
            'selections'         => $selections,
            'projects'           => $this->getProjectService()->findProjectParticipationByContact($contact),
            'projectService'     => $this->getProjectService(),
            'optIn'              => $optIn,
            'callService'        => $this->getCallService(),
            'registationService' => $this->getRegistrationService(),
            'ideaService'        => $this->getIdeaService(),
        ]);
    }

    /***
     * @return ViewModel
     */
    public function permitAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));

        $this->getAdminService()->findPermitContactByContact($contact);

        return new ViewModel([
            'contact' => $contact,
        ]);
    }

    /**
     * @return ViewModel
     */
    public function impersonateAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));
        $form = new Impersonate($this->getEntityManager());

        $data = array_merge_recursive($this->getRequest()->getPost()->toArray());

        $form->setData($data);
        $deeplink = false;
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $data = $form->getData();

            /** @var Target $target */
            $target = $this->getDeeplinkService()->findEntityById(Target::class, $data['target']);
            $key = (!empty($data['key']) ? $data['key'] : null);
            //Create a deeplink for the user which redirects to the profile-page
            $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contact, null, $key);
        }

        return new ViewModel([
            'deeplink'       => $deeplink,
            'contact'        => $contact,
            'contactService' => $this->getContactService(),
            'form'           => $form,
        ]);
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));

        if (is_null($contact)) {
            return $this->notFoundAction();
        }


        //Get contacts in an organisation
        if ($this->getContactService()->hasOrganisation($contact)) {
            $data = array_merge([
                'contact' => [
                    'organisation' => $contact->getContactOrganisation()->getOrganisation()->getId(),
                    'branch'       => $contact->getContactOrganisation()->getBranch(),
                ],
            ], $this->getRequest()->getPost()->toArray());

            $form = $this->getFormService()->prepare($contact, $contact, $data);
            $form->get('contact_entity_contact')->get("organisation")
                ->injectOrganisation($contact->getContactOrganisation()->getOrganisation());
        } else {
            $data = array_merge($this->getRequest()->getPost()->toArray());
            $form = $this->getFormService()->prepare($contact, $contact, $data);
        }

        /** Show or hide buttons based on the status of a contact */
        if ($this->getContactService()->isActive($contact)) {
            $form->remove('reactivate');
        } else {
            $form->remove('deactivate');
        }


        if ($this->getRequest()->isPost()) {

            /** Deactivate a contact */
            if (isset($data['deactivate'])) {
                $this->flashMessenger()
                    ->addSuccessMessage(sprintf($this->translate("txt-contact-%s-has-been-deactivated"), $contact));

                $contact->setDateEnd(new \DateTime());
                $this->getContactService()->updateEntity($contact);

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Reactivate a contact */
            if (isset($data['reactivate'])) {
                $this->flashMessenger()
                    ->addSuccessMessage(sprintf($this->translate("txt-contact-%s-has-been-reactivated"), $contact));

                $contact->setDateEnd(null);
                $this->getContactService()->updateEntity($contact);

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Cancel the form */
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Handle the form */
            if ($form->isValid()) {
                /**
                 * @var $contact Contact
                 */
                $contact = $form->getData();
                $contact = $this->getContactService()->updateEntity($contact);

                /** Update the organisation if there is any */
                if (isset($data['contact_entity_contact']['organisation'])) {
                    //Update the contactOrganisation (if set)
                    if (is_null($contact->getContactOrganisation())) {
                        $contactOrganisation = new ContactOrganisation();
                        $contactOrganisation->setContact($contact);
                    } else {
                        $contactOrganisation = $contact->getContactOrganisation();
                    }

                    $contactOrganisation->setBranch(strlen($data['contact_entity_contact']['branch']) === 0 ? null
                        : $data['contact_entity_contact']['branch']);
                    $contactOrganisation->setOrganisation($this->getOrganisationService()
                        ->findOrganisationById($data['contact_entity_contact']['organisation']));

                    $this->getContactService()->updateEntity($contactOrganisation);
                }

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel([
            'contactService' => $this->getContactService(),
            'contact'        => $contact,
            'form'           => $form,
        ]);
    }

    /**
     * @return ViewModel
     */
    public function newAction()
    {
        $contact = new Contact();


        $data = array_merge($this->getRequest()->getPost()->toArray());
        $form = $this->getFormService()->prepare($contact, $contact, $data);

        //Disable the inarray validator for organisations
        $form->get($contact->get("underscore_entity_name"))->get('organisation')->setDisableInArrayValidator(true);

        /** Show or hide buttons based on the status of a contact */

        $form->remove('reactivate');
        $form->remove('deactivate');


        if ($this->getRequest()->isPost()) {

            /** Cancel the form */
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact-admin/list');
            }

            /** Handle the form */
            if ($form->isValid()) {
                /**
                 * @var $contact Contact
                 */
                $contact = $form->getData();
                $contact = $this->getContactService()->updateEntity($contact);

                $contactOrganisation = new ContactOrganisation();
                $contactOrganisation->setContact($contact);

                $contactOrganisation->setBranch(strlen($data['contact']['branch']) === 0 ? null
                    : $data['contact']['branch']);
                $contactOrganisation->setOrganisation($this->getOrganisationService()
                    ->findOrganisationById($data['contact']['organisation']));

                $this->getContactService()->updateEntity($contactOrganisation);

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
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
        $form = new Import($this->getContactService(), $this->getSelectionService());
        $form->setData($data);

        /** store the data in the session, so we can use it when we really handle the import */
        $importSession = new Container('import');

        $handleImport = null;
        if ($this->getRequest()->isPost()) {
            if (isset($data['upload']) && $form->isValid()) {
                $fileData = file_get_contents($data['file']['tmp_name'], FILE_TEXT);

                $importSession->active = true;
                $importSession->fileData = $fileData;

                $handleImport = $this->handleImport(
                    $fileData,
                    null,
                    isset($data['optIn']) ? $data['optIn'] : [],
                    $data['selection_id'],
                    $data['selection']
                );
            }

            if (isset($data['import']) && $importSession->active && isset($data['key'])) {
                $handleImport = $this->handleImport(
                    $importSession->fileData,
                    $data['key'],
                    isset($data['optIn']) ? $data['optIn'] : [],
                    $data['selection_id'],
                    $data['selection']
                );
            }
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
            $text = trim(sprintf(
                "%s, %s (%s)",
                trim(sprintf("%s %s", $result['middleName'], $result['lastName'])),
                $result['firstName'],
                $result['email']
            ));

            /*
             * Do a fall-back to the email when the name is empty
             */
            if (strlen($text) === 0) {
                $text = $result['email'];
            }

            $results[] = [
                'value'        => $result['id'],
                'text'         => $text,
                'name'         => sprintf(
                    "%s, %s",
                    trim(sprintf("%s %s", $result['middleName'], $result['lastName'])),
                    $result['firstName']
                ),
                'id'           => $result['id'],
                'email'        => $result['email'],
                'organisation' => $result['organisation'],
            ];
        }

        return new JsonModel($results);
    }
}
