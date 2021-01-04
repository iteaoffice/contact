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

use Contact\Entity\OptIn;
use Contact\Form\OptInFilter;
use Contact\Service\ContactService;
use Contact\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

/**
 * Class OptInManagerController
 *
 * @package Contact\Controller
 */
final class OptInManagerController extends ContactAbstractController
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
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $optInQuery = $this->contactService->findFiltered(OptIn::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($optInQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new OptInFilter();

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'      => $paginator,
                'form'           => $form,
                'encodedFilter'  => urlencode($filterPlugin->getHash()),
                'order'          => $filterPlugin->getOrder(),
                'direction'      => $filterPlugin->getDirection(),
                'contactService' => $this->contactService
            ]
        );
    }


    public function viewAction(): ViewModel
    {
        /**
         * @var OptIn $optIn
         */
        $optIn = $this->contactService->find(OptIn::class, (int)$this->params('id'));

        if (null === $optIn) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'optIn'          => $optIn,
                'contactService' => $this->contactService
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(OptIn::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /**
             * @var OptIn $optIn
             */
            $optIn = $form->getData();
            $this->contactService->save($optIn);

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate('txt-optIn-%s-has-successfully-been-deleted'),
                    $optIn->getOptIn()
                )
            );

            return $this->redirect()->toRoute('zfcadmin/opt-in/view', ['id' => $optIn->getId()]);
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        /**
         * @var $optIn OptIn
         */
        $optIn = $this->contactService->find(OptIn::class, (int)$this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($optIn, $data);

        if (! $this->contactService->canDeleteOptIn($optIn)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete']) && $this->contactService->canDeleteOptIn($optIn)) {
                $this->contactService->delete($optIn);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-opt-in-has-successfully-been-deleted'))
                );

                return $this->redirect()->toRoute('zfcadmin/opt-in/list');
            }

            if (! isset($data['cancel']) && $form->isValid()) {
                $optIn = $form->getData();
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-opt-in-%s-has-successfully-been-updated'),
                        $optIn->getOptIn()
                    )
                );

                $this->contactService->save($optIn);
            }

            return $this->redirect()->toRoute('zfcadmin/opt-in/view', ['id' => $optIn->getId()]);
        }

        return new ViewModel(['form' => $form]);
    }
}
