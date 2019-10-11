<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */

declare(strict_types=1);

namespace Contact\Controller\Office;

use Contact\Controller\ContactAbstractController;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Office\Leave;
use Contact\Form\Element\Contact as ContactFormElement;
use Contact\Service\FormService;
use Contact\Service\Office\ContactService as OfficeContactService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Contact\Form\Office\ContactFilter;
use Zend\Form\Element;
use Zend\Http\Request;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use function date;
use function sprintf;

/**
 * Class ContactController
 *
 * @package Contact\Controller\Office
 */
final class ContactController extends ContactAbstractController
{
    /**
     * @var OfficeContactService
     */
    private $officeContactService;

    /**
     * @var FormService
     */
    private $formService;

    public function __construct(OfficeContactService $officeContactService, FormService $formService)
    {
        $this->officeContactService = $officeContactService;
        $this->formService          = $formService;
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter([
            'order'     => 'contact',
            'direction' => 'asc',
            'active'    => 'active'
        ]);
        $contactQuery = $this->officeContactService->findFiltered(OfficeContact::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new ContactFilter();
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    public function viewAction(): ViewModel
    {
        /** @var OfficeContact $officeContact */
        $officeContact = $this->officeContactService->find(OfficeContact::class, (int) $this->params('id'));

        if ($officeContact === null) {
            return $this->notFoundAction();
        }

        $year         = $this->params()->fromQuery('year', date('Y'));
        $filterPlugin = $this->getContactFilter([
            'order'           => 'dateStart',
            'direction'       => 'asc',
        ]);
        $filterValues = array_merge(
            $filterPlugin->getFilter(),
            ['officeContact' => $officeContact, 'year' => $year]
        );
        $leaveQuery = $this->officeContactService->findFiltered(Leave::class, $filterValues);
        $userLeave  = $leaveQuery->getQuery()->getResult();
        $years      = $this->officeContactService->findLeaveYears($officeContact);

        return new ViewModel([
            'officeContact' => $officeContact,
            'userLeave'     => $userLeave,
            'selectedYear'  => $year,
            'years'         => $years,
            'arguments'     => sprintf('year=%d', $year),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data    = $request->getPost()->toArray();
        $form    = $this->formService->prepare(new OfficeContact(), $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact/office/list');
            }

            if ($form->isValid()) {
                /** @var OfficeContact $officeContact */
                $officeContact = $form->getData();
                $this->officeContactService->save($officeContact);

                return $this->redirect()->toRoute(
                    'zfcadmin/contact/office/view',
                    ['id' => $officeContact->getId()]
                );
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var OfficeContact $officeContact */
        $officeContact = $this->officeContactService->find(OfficeContact::class, (int) $this->params('id'));

        if ($officeContact === null) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($officeContact, $data);
        /** @var ContactFormElement $contactElement */
        $contactElement = $form->get($officeContact->get('underscore_entity_name'))->get('contact');
        $contactElement->setValueOptions(
            [$officeContact->getContact()->getId() => $officeContact->getContact()->getDisplayName()]
        )->setDisableInArrayValidator(true);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact/office/list');
            }

            if ($form->isValid()) {
                /** @var OfficeContact $officeContact */
                $officeContact = $form->getData();
                $this->officeContactService->save($officeContact);
                return $this->redirect()->toRoute(
                    'zfcadmin/contact/office/view',
                    ['id' => $officeContact->getId()]
                );
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function newLeaveAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var OfficeContact $officeContact */
        $officeContact = $this->officeContactService->find(OfficeContact::class, (int) $this->params('id'));

        if ($officeContact === null) {
            return $this->notFoundAction();
        }

        $data  = $request->getPost()->toArray();
        $leave = new Leave();
        $form  = $this->formService->prepare($leave, $data);
        $form->remove('delete');
        /** @var Element $element */
        foreach ($form->get($leave->get('underscore_entity_name'))->getElements() as $element) {
            if ($element->hasAttribute('required')) {
                $element->removeAttribute('required');
            }
        }

        $redirectParams = [
            'zfcadmin/contact/office/view',
            ['id' => $officeContact->getId()],
            ['query' => ['year' => $leave->getDateStart()->format('Y')]]
        ];

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(...$redirectParams);
            }

            if ($form->isValid()) {
                /** @var Leave $leave */
                $leave = $form->getData();
                $leave->setOfficeContact($officeContact);
                $this->officeContactService->save($leave);

                return $this->redirect()->toRoute(...$redirectParams);
            }
        }

        return new ViewModel([
            'form'          => $form,
            'officeContact' => $officeContact
        ]);
    }

    public function editLeaveAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Leave $leave */
        $leave = $this->officeContactService->find(Leave::class, (int) $this->params('id'));

        if ($leave === null) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($leave, $data);
        /** @var Element $element */
        foreach ($form->get($leave->get('underscore_entity_name'))->getElements() as $element) {
            if ($element->hasAttribute('required')) {
                $element->removeAttribute('required');
            }
        }

        $redirectParams = [
            'zfcadmin/contact/office/view',
            ['id' => $leave->getOfficeContact()->getId()],
            ['query' => ['year' => $leave->getDateStart()->format('Y')]]
        ];

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(...$redirectParams);
            }

            if (isset($data['delete'])) {
                $this->officeContactService->delete($leave);
                return $this->redirect()->toRoute(...$redirectParams);
            }

            if ($form->isValid()) {
                /** @var Leave $leave */
                $leave = $form->getData();
                $this->officeContactService->save($leave);
                return $this->redirect()->toRoute(...$redirectParams);
            }
        }

        return new ViewModel([
            'form'          => $form,
            'officeContact' => $leave->getOfficeContact()
        ]);
    }
}
