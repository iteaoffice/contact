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
use Contact\Service\FormService;
use Contact\Service\Office\ContactService as OfficeContactService;
use DateTime;
use DateTimeZone;
use Zend\Form\Element;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class LeaveController
 *
 * @package Contact\Controller\Office
 */
final class LeaveController extends ContactAbstractController
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
        $this->formService = $formService;
    }

    public function calendarAction()
    {
        $form = $this->formService->prepare(Leave::class);
        $form->remove('csrf');

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function listAction()
    {
        /** @var OfficeContact $officeContact */
        $officeContact = $this->identity()->getOfficeContact();

        if ($officeContact === null) {
            return $this->notFoundAction();
        }

        $years        = $this->officeContactService->findLeaveYears($officeContact);
        $year         = $this->params()->fromQuery('year', end($years));
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


        return new ViewModel([
            'officeContact' => $officeContact,
            'userLeave'     => $userLeave,
            'selectedYear'  => $year,
            'years'         => $years,
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data    = $request->getPost()->toArray();
        $leave   = new Leave();
        $form    = $this->formService->prepare($leave, $data);
        $form->remove('delete');
        /** @var Element $element */
        foreach ($form->get($leave->get('underscore_entity_name'))->getElements() as $element) {
            if ($element->hasAttribute('required')) {
                $element->removeAttribute('required');
            }
        }

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/contact/office/leave/list');
            }

            if ($form->isValid()) {
                /** @var Leave $leave */
                $leave = $form->getData();
                $leave->setOfficeContact($this->identity()->getOfficeContact());
                $this->officeContactService->save($leave);

                return $this->redirect()->toRoute(
                    'zfcadmin/contact/office/leave/list',
                    [],
                    ['query' => ['year' => $leave->getDateStart()->format('Y')]]
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
            'zfcadmin/contact/office/leave/list',
            [],
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
            'form' => $form
        ]);
    }

    public function updateAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $leave = new Leave();
            if ($request->getPost()->get('id') !== null) {
                $leave = $this->officeContactService->find(Leave::class, (int)$request->getPost()->get('id'));
                if ($leave === null) {
                    return $this->notFoundAction();
                }
            }

            $form = $this->formService->prepare($leave);
            $form->remove('csrf');
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                /** @var Leave $leave */
                $leave = $form->getData();
                $leave->setOfficeContact($this->identity()->getOfficeContact());
                $this->officeContactService->save($leave);
                return new JsonModel($this->officeContactService->parseFullCalendarEvent($leave));
            }
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(400);

            return new JsonModel(['errors' => $form->getMessages()]);
        }

        return $this->notFoundAction();
    }

    public function moveAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Leave $leave */
        $leave   = $this->officeContactService->find(Leave::class, (int)$request->getPost()->get('id'));
        if ($leave === null) {
            return $this->notFoundAction();
        }
        $leave->setDateStart(new DateTime($request->getPost('start', 'now')));
        $leave->setDateEnd(new DateTime($request->getPost('end', 'now')));
        $this->officeContactService->save($leave);
        return new JsonModel();
    }

    public function deleteAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $leave = $this->officeContactService->find(Leave::class, (int)$request->getPost()->get('id'));
            if ($leave === null) {
                return $this->notFoundAction();
            }
            $this->officeContactService->delete($leave);
            return new JsonModel();
        }

        return $this->notFoundAction();
    }

    public function fetchAction()
    {
        /** @var Request $request */
        $request   = $this->getRequest();
        $timeZone  = new DateTimeZone($request->getQuery('timeZone', 'UTC'));
        $start     = new DateTime($request->getQuery('start', 'now'), $timeZone);
        $end       = new DateTime($request->getQuery('end', 'now'), $timeZone);
        $leaveList = $this->officeContactService->findLeave($this->identity()->getOfficeContact(), $start, $end);
        $eventList = [];

        foreach ($leaveList as $leave) {
            $eventList[] = $this->officeContactService->parseFullCalendarEvent($leave);
        }

        return new JsonModel($eventList);
    }
}
