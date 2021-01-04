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

use Contact\Entity;
use Contact\Entity\Dnd;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use General\Service\GeneralService;
use Program\Service\ProgramService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Validator\File\FilesSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;

use function count;

/**
 * Class DndController
 *
 * @package Contact\Controller
 */
final class DndController extends ContactAbstractController
{
    private ContactService $contactService;
    private ProgramService $programService;
    private FormService $formService;
    private GeneralService $generalService;
    private TranslatorInterface $translator;

    public function __construct(
        ContactService $contactService,
        ProgramService $programService,
        FormService $formService,
        GeneralService $generalService,
        TranslatorInterface $translator
    ) {
        $this->contactService = $contactService;
        $this->programService = $programService;
        $this->formService = $formService;
        $this->generalService = $generalService;
        $this->translator = $translator;
    }


    public function newAction()
    {
        $contact = $this->contactService->findContactById((int)$this->params('contactId'));

        if (null === $contact) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = $this->formService->prepare(Dnd::class, $data);
        $form->remove('delete');

        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view/general',
                    ['id' => $contact->getId()],
                    ['fragment' => 'legal']
                );
            }

            if ($form->isValid()) {
                $fileData = $this->params()->fromFiles();

                //Create a article object element
                $dndObject = new Entity\DndObject();
                $dndObject->setObject(
                    file_get_contents($fileData['contact_entity_dnd']['file']['tmp_name'])
                );
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['contact_entity_dnd']['file']);
                $dnd = new Entity\Dnd();
                $dnd->setSize($fileSizeValidator->size);

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['contact_entity_dnd']['file']);
                $dnd->setContentType(
                    $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                );

                //Fetch the call
                $dnd->setProgram($form->getData()->getProgram());
                $dnd->setContact($contact);
                $dndObject->setDnd($dnd);
                $this->contactService->save($dndObject);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-dnd-for-contact-%s-has-been-uploaded-successfully'),
                    $contact->parseFullName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view/general',
                    ['id' => $contact->getId()],
                    ['fragment' => 'legal']
                );
            }
        }

        return new ViewModel(
            [
                'contactService' => $this->contactService,
                'contact'        => $contact,
                'form'           => $form,
            ]
        );
    }

    public function editAction()
    {
        /**
         * @var Dnd $dnd
         */
        $dnd = $this->contactService->find(Dnd::class, (int)$this->params('id'));

        if (null === $dnd) {
            return $this->notFoundAction();
        }
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = $this->formService->prepare($dnd, $data);
        $form->getInputFilter()->get('contact_entity_dnd')->get('file')->setRequired(false);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view/general',
                    ['id' => $dnd->getContact()->getId()],
                    ['fragment' => 'legal']
                );
            }

            if (isset($data['delete'])) {
                $this->contactService->delete($dnd);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-dnd-for-contact-%s-has-been-deleted-successfully'),
                    $dnd->getContact()->parseFullName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);

                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view/general',
                    ['id' => $dnd->getContact()->getId()],
                    ['fragment' => 'legal']
                );
            }

            if ($form->isValid()) {
                $fileData = $this->params()->fromFiles();

                if ($fileData['contact_entity_dnd']['file']['error'] === 0) {
                    /*
                     * Remove the current entity
                     */
                    foreach ($dnd->getObject() as $object) {
                        $this->contactService->delete($object);
                    }

                    //Create a article object element
                    $dndObject = new Entity\DndObject();
                    $dndObject->setObject(file_get_contents($fileData['contact_entity_dnd']['file']['tmp_name']));
                    $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                    $fileSizeValidator->isValid($fileData['contact_entity_dnd']['file']);
                    $dnd->setSize($fileSizeValidator->size);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['contact_entity_dnd']['file']);
                    $dnd->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );

                    $dndObject->setDnd($dnd);
                    $this->contactService->save($dndObject);
                }

                $this->contactService->save($dnd);

                $changelogMessage = sprintf(
                    $this->translator->translate('txt-dnd-for-contact-%s-has-been-uploaded-successfully'),
                    $dnd->getContact()->parseFullName()
                );

                $this->flashMessenger()->addSuccessMessage($changelogMessage);


                return $this->redirect()->toRoute(
                    'zfcadmin/contact/view/general',
                    ['id' => $dnd->getContact()->getId()],
                    ['fragment' => 'legal']
                );
            }
        }

        return new ViewModel(
            [
                'contact' => $dnd->getContact(),
                'dnd'     => $dnd,
                'form'    => $form,
            ]
        );
    }

    public function downloadAction(): Response
    {
        /** @var Dnd $dnd */
        $dnd = $this->contactService->find(Dnd::class, (int)$this->params('id'));

        /** @var Response $response */
        $response = $this->getResponse();

        if (null === $dnd || count($dnd->getObject()) === 0) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $dnd->getObject()->first()->getObject();

        $response->setContent(stream_get_contents($object));
        $response->getHeaders()
            ->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $dnd->parseFileName() . '.' . $dnd->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine('Pragma: public')->addHeaderLine(
                'Content-Type: ' . $dnd->getContentType()
                    ->getContentType()
            )->addHeaderLine('Content-Length: ' . $dnd->getSize());

        return $response;
    }
}
