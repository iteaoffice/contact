<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Controller\Selection;

use Contact\Controller\ContactAbstractController;
use Contact\Entity;
use Contact\Service\FormService;
use Contact\Service\SelectionService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Search\Form\SearchFilter;

/**
 * Class TypeController
 * @package Contact\Controller\Selection
 */
final class TypeController extends ContactAbstractController
{
    private SelectionService $selectionService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(SelectionService $selectionService, FormService $formService, TranslatorInterface $translator)
    {
        $this->selectionService = $selectionService;
        $this->formService      = $formService;
        $this->translator       = $translator;
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $contactQuery = $this->selectionService
            ->findFiltered(Entity\Selection\Type::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'     => $paginator,
                'form'          => $form,
                'encodedFilter' => urlencode($filterPlugin->getHash()),
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Entity\Selection\Type::class, $data);
        $form->remove('delete');


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/selection/type/list');
            }

            if ($form->isValid()) {
                /* @var $type Entity\Selection\Type */
                $type = $form->getData();

                $result = $this->selectionService->save($type);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-selection-type-has-been-created-successfully'),
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/selection/type/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        $type = $this->selectionService->find(Entity\Selection\Type::class, (int)$this->params('id'));

        if (null === $type) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($type, $data);

        if (! $this->selectionService->canDeleteType($type)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/selection/type/view',
                    [
                        'id' => $type->getId(),
                    ]
                );
            }
            if (isset($data['delete']) && $this->selectionService->canDeleteType($type)) {
                $this->selectionService->delete($type);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-selection-type-has-been-deleted-successfully'),
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/selection/type/list',
                    [
                        'id' => $type->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /* @var $type Entity\Selection\Type */
                $type = $form->getData();

                $this->selectionService->save($type);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-selection-type-has-been-updated-successfully'),
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/selection/type/view',
                    [
                        'id' => $type->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function viewAction(): ViewModel
    {
        $type = $this->selectionService->find(Entity\Selection\Type::class, (int)$this->params('id'));

        if (null === $type) {
            return $this->notFoundAction();
        }

        return new ViewModel(['type' => $type]);
    }
}
