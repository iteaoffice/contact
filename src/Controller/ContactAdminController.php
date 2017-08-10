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

use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Entity\OptIn;
use Contact\Entity\Photo;
use Contact\Form\ContactFilter;
use Contact\Form\Impersonate;
use Contact\Form\Import;
use Deeplink\Entity\Target;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Zend\Paginator\Paginator;
use Zend\Session\Container;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
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
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new ContactFilter($this->getEntityManager());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'      => $paginator,
                'form'           => $form,
                'encodedFilter'  => urlencode($filterPlugin->getHash()),
                'order'          => $filterPlugin->getOrder(),
                'direction'      => $filterPlugin->getDirection(),
                'projectService' => $this->getProjectService(),
            ]
        );
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $filterPlugin = $this->getContactFilter();

        $contactQuery = $this->getContactService()->findEntitiesFiltered(Contact::class, $filterPlugin->getFilter());

        /** @var Contact[] $contacts */
        $contacts = $contactQuery->getResult();

        // Open the output stream
        $fh = fopen('php://output', 'wb');

        ob_start();

        fputcsv(
            $fh,
            [
                'Email',
                'Firstname',
                'Lastname',
                'Organisation',
                'Country',
            ]
        );

        foreach ($contacts as $contact) {
            fputcsv(
                $fh,
                [
                    $contact->getEmail(),
                    $contact->getFirstName(),
                    trim(sprintf("%s %s", $contact->getMiddleName(), $contact->getLastName())),
                    !is_null($contact->getContactOrganisation()) ? $contact->getContactOrganisation()
                        ->getOrganisation() : '',
                    !is_null($contact->getContactOrganisation()) ? $contact->getContactOrganisation()
                        ->getOrganisation()->getCountry() : '',
                ]
            );
        }

        $string = ob_get_clean();

        //To be able to open the file correctly in Excel, we need to convert it to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF8');

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            "attachment; filename=\"contact.csv\""
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($string));

        $response->setContent($string);

        return $response;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function viewAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));
        $selections = $this->getSelectionService()->findSelectionsByContact($contact);
        $optIn = $this->getContactService()->findAll(OptIn::class);

        if (is_null($contact)) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        if ($this->getRequest()->isPost() && !empty($data['selection'])) {
            foreach ((array)$data['selection'] as $selectionId) {
                $selection = $this->getSelectionService()->findSelectionById($selectionId);
                foreach ($selection->getSelectionContact() as $selectionContact) {
                    if ($selectionContact->getContact() === $contact) {
                        $this->getSelectionService()->removeEntity($selectionContact);

                        $this->flashMessenger()
                            ->addSuccessMessage(
                                sprintf(
                                    $this->translate("txt-contact-%s-has-removed-form-selection-%s-successfully"),
                                    $contact->getDisplayName(),
                                    $selection->getSelection()
                                ),
                                $contact
                            );
                    }
                }
            }

            return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
        }

        return new ViewModel(
            [
                'contact'             => $contact,
                'contactService'      => $this->getContactService(),
                'selections'          => $selections,
                'selectionService'    => $this->getSelectionService(),
                'projects'            => $this->getProjectService()->findProjectParticipationByContact($contact),
                'projectService'      => $this->getProjectService(),
                'optIn'               => $optIn,
                'callService'         => $this->getCallService(),
                'registrationService' => $this->getRegistrationService(),
                'ideaService'         => $this->getIdeaService(),
            ]
        );
    }

    /***
     * @return ViewModel
     */
    public function permitAction(): ViewModel
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));

        $this->getAdminService()->findPermitContactByContact($contact);

        return new ViewModel(
            [
                'contact' => $contact,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function impersonateAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));
        $form = new Impersonate($this->getEntityManager());

        $data = $this->getRequest()->getPost()->toArray();

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

        return new ViewModel(
            [
                'deeplink'       => $deeplink,
                'contact'        => $contact,
                'contactService' => $this->getContactService(),
                'form'           => $form,
            ]
        );
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $contact = $this->getContactService()->findContactById($this->params('id'));

        if (is_null($contact)) {
            return $this->notFoundAction();
        }


        //Get contacts in an organisation
        if ($this->getContactService()->hasOrganisation($contact)) {
            $data = array_merge(
                [
                    'contact' => [
                        'organisation' => $contact->getContactOrganisation()->getOrganisation()->getId(),
                        'branch'       => $contact->getContactOrganisation()->getBranch(),
                    ],
                ],
                $this->getRequest()->getPost()->toArray()
            );

            $form = $this->getFormService()->prepare(Contact::class, $contact, $data);
            $form->get('contact_entity_contact')->get("organisation")
                ->injectOrganisation($contact->getContactOrganisation()->getOrganisation());
        } else {
            $data = $this->getRequest()->getPost()->toArray();
            $form = $this->getFormService()->prepare(Contact::class, $contact, $data);
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

                $this->getContactSearchService()->deleteDocument($contact);

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }

            /** Reactivate a contact */
            if (isset($data['reactivate'])) {
                $this->flashMessenger()
                    ->addSuccessMessage(sprintf($this->translate("txt-contact-%s-has-been-reactivated"), $contact));

                $contact->setDateEnd(null);
                $this->getContactService()->updateEntity($contact);

                $this->getContactSearchService()->updateDocument($contact);

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

                //Reset the access when the array is empty
                if (!isset($data['contact_entity_contact']['access'])) {
                    $contact->setAccess(new ArrayCollection());
                }

                $contact = $this->getContactService()->updateEntity($contact);

                //Reset the roles of this contact
                $this->getAdminService()->refreshAccessRolesByContact($contact);
                $this->getAdminService()->resetCachedAccessRolesByContact($contact);

                /** Update the organisation if there is any */
                if (isset($data['contact_entity_contact']['organisation'])) {
                    //Update the contactOrganisation (if set)
                    if (is_null($contact->getContactOrganisation())) {
                        $contactOrganisation = new ContactOrganisation();
                        $contactOrganisation->setContact($contact);
                    } else {
                        $contactOrganisation = $contact->getContactOrganisation();
                    }

                    $contactOrganisation->setBranch(
                        empty($data['contact_entity_contact']['branch']) ? null
                            : $data['contact_entity_contact']['branch']
                    );
                    $contactOrganisation->setOrganisation(
                        $this->getOrganisationService()
                            ->findOrganisationById($data['contact_entity_contact']['organisation'])
                    );

                    $this->getContactService()->updateEntity($contactOrganisation);
                }

                //Handle the file upload
                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['contact_entity_contact']['file']['name']) && $fileData['contact_entity_contact']['file']['error'] === 0) {
                    /** @var Photo $photo */
                    $photo = $contact->getPhoto()->first();
                    if (!$photo) {
                        //Create a photo element
                        $photo = new Photo();
                    }
                    $photo->setPhoto(file_get_contents($fileData['contact_entity_contact']['file']['tmp_name']));
                    $photo->setThumb(file_get_contents($fileData['contact_entity_contact']['file']['tmp_name']));
                    $photo->setContact($contact);
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['contact_entity_contact']['file']);
                    $photo->setWidth($imageSizeValidator->width);
                    $photo->setHeight($imageSizeValidator->height);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['contact_entity_contact']['file']);
                    $photo->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type));

                    $this->getContactService()->updateEntity($photo);
                }

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel(
            [
                'contactService' => $this->getContactService(),
                'contact'        => $contact,
                'form'           => $form,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        $contact = new Contact();


        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->getFormService()->prepare($contact, $contact, $data);

        //Disable the inarray validator for organisations
        $form->get('contact_entity_contact')->get('organisation')->setDisableInArrayValidator(true);

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

                if (isset($data['contact_entity_contact']['organisation'])) {
                    $contactOrganisation = new ContactOrganisation();
                    $contactOrganisation->setContact($contact);

                    $contactOrganisation->setBranch(
                        strlen($data['contact_entity_contact']['branch']) === 0 ? null
                            : $data['contact_entity_contact']['branch']
                    );
                    $contactOrganisation->setOrganisation(
                        $this->getOrganisationService()
                            ->findOrganisationById($data['contact_entity_contact']['organisation'])
                    );

                    $this->getContactService()->updateEntity($contactOrganisation);
                }

                return $this->redirect()->toRoute('zfcadmin/contact-admin/view', ['id' => $contact->getId()]);
            }
        }

        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function importAction()
    {
        set_time_limit(0);

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

            if (isset($data['import'], $data['key']) && $importSession->active) {
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
            $text = trim(
                sprintf(
                    "%s, %s (%s)",
                    trim(sprintf("%s %s", $result['middleName'], $result['lastName'])),
                    $result['firstName'],
                    $result['email']
                )
            );

            /*
             * Do a fall-back to the email when the name is empty
             */
            if (empty($text)) {
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
