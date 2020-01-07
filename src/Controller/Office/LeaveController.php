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

use Admin\Entity\Access;
use Admin\Service\AdminService;
use Contact\Controller\ContactAbstractController;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Office\Leave;
use Contact\Service\FormService;
use Contact\Service\Office\ContactService as OfficeContactService;
use DateTime;
use DateTimeZone;
use Laminas\Form\Element;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use function in_array;
use function sprintf;

/**
 * Class LeaveController
 *
 * @package Contact\Controller\Office
 */
final class LeaveController extends ContactAbstractController
{
    private OfficeContactService $officeContactService;
    private AdminService $adminService;
    private FormService $formService;

    public function __construct(
        OfficeContactService $officeContactService,
        AdminService $adminService,
        FormService $formService
    ) {
        $this->officeContactService = $officeContactService;
        $this->adminService = $adminService;
        $this->formService = $formService;
    }

    public function calendarAction(): ViewModel
    {
        $leave = new Leave();
        $form = $this->formService->prepare($leave);
        $form->remove('csrf');
        $form->get($leave->get('underscore_entity_name'))->remove('officeContact');

        $isManagementAssistant = in_array(
            Access::ACCESS_MANAGEMENT_ASSISTANT,
            $this->adminService->findAccessRolesByContactAsArray($this->identity()),
            true
        );

        return new ViewModel(
            [
                'form'                  => $form,
                'isManagementAssistant' => $isManagementAssistant
            ]
        );
    }

    public function officeCalendarAction(): ViewModel
    {
        $leave = new Leave();
        $form = $this->formService->prepare($leave);
        $form->remove('csrf');

        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }

    public function listAction(): ViewModel
    {
        /** @var OfficeContact $officeContact */
        $officeContact = $this->identity()->getOfficeContact();

        if ($officeContact === null) {
            return $this->notFoundAction();
        }

        $years = $this->officeContactService->findLeaveYears($officeContact);
        $year = $this->params()->fromQuery('year', end($years));
        $filterPlugin = $this->getContactFilter(
            [
                'order'     => 'dateStart',
                'direction' => 'asc',
            ]
        );
        $filterValues = array_merge(
            $filterPlugin->getFilter(),
            ['officeContact' => $officeContact, 'year' => $year]
        );
        $leaveQuery = $this->officeContactService->findFiltered(Leave::class, $filterValues);
        $userLeave = $leaveQuery->getQuery()->getResult();

        $isManagementAssistant = in_array(
            Access::ACCESS_MANAGEMENT_ASSISTANT,
            $this->adminService->findAccessRolesByContactAsArray($this->identity()),
            true
        );

        return new ViewModel(
            [
                'officeContact'         => $officeContact,
                'userLeave'             => $userLeave,
                'selectedYear'          => $year,
                'years'                 => $years,
                'arguments'             => sprintf('year=%d', $year),
                'order'                 => $filterPlugin->getOrder(),
                'direction'             => $filterPlugin->getDirection(),
                'isManagementAssistant' => $isManagementAssistant
            ]
        );
    }

    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $data = $request->getPost()->toArray();
        $leave = new Leave();
        $form = $this->formService->prepare($leave, $data);
        $form->remove('delete');
        $form->get($leave->get('underscore_entity_name'))->remove('officeContact');
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

        return new ViewModel(
            [
                'form' => $form
            ]
        );
    }

    public function editAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Leave $leave */
        $leave = $this->officeContactService->find(Leave::class, (int)$this->params('id'));

        if ($leave === null) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($leave, $data);
        $form->get($leave->get('underscore_entity_name'))->remove('officeContact');
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

        return new ViewModel(
            [
                'form' => $form
            ]
        );
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
            $data = $request->getPost()->toArray();
            $form->remove('csrf');
            $form->setData($data);

            if ($form->isValid()) {
                $isManagementAssistant = in_array(
                    Access::ACCESS_MANAGEMENT_ASSISTANT,
                    $this->adminService->findAccessRolesByContactAsArray($this->identity()),
                    true
                );

                /** @var Leave $leave */
                $leave = $form->getData();
                if (! $isManagementAssistant) {
                    $leave->setOfficeContact($this->identity()->getOfficeContact());
                }
                $this->officeContactService->save($leave);

                // The form came from the office calendar, so parse different response
                if (isset($data[$leave->get('underscore_entity_name')]['officeContact'])) {
                    return new JsonModel($this->officeContactService->parseOfficeCalendarEvent($leave));
                }

                return new JsonModel($this->officeContactService->parseCalendarEvent($leave));
            }
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(400);

            return new JsonModel(
                [
                    'errors' => $form->getMessages()
                ]
            );
        }

        return $this->notFoundAction();
    }

    public function moveAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Leave $leave */
        $leave = $this->officeContactService->find(Leave::class, (int)$request->getPost()->get('id'));
        $isManagementAssistant = in_array(
            Access::ACCESS_MANAGEMENT_ASSISTANT,
            $this->adminService->findAccessRolesByContactAsArray($this->identity()),
            true
        );

        if (($leave === null)
            || (
                ($leave->getOfficeContact()->getContact()->getId() !== $this->identity()->getId())
                && ! $isManagementAssistant
            )
        ) {
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
            /** @var Leave $leave */
            $leave = $this->officeContactService->find(Leave::class, (int)$request->getPost()->get('id'));

            $isManagementAssistant = in_array(
                Access::ACCESS_MANAGEMENT_ASSISTANT,
                $this->adminService->findAccessRolesByContactAsArray($this->identity()),
                true
            );

            if (($leave === null)
                || (
                    ($leave->getOfficeContact()->getContact()->getId() !== $this->identity()->getId())
                    && ! $isManagementAssistant
                )
            ) {
                return $this->notFoundAction();
            }

            $this->officeContactService->delete($leave);
            return new JsonModel();
        }

        return $this->notFoundAction();
    }

    public function fetchAction(): JsonModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $timeZone = new DateTimeZone($request->getQuery('timeZone', 'UTC'));
        $start = new DateTime($request->getQuery('start', 'now'), $timeZone);
        $end = new DateTime($request->getQuery('end', 'now'), $timeZone);
        $leaveList = $this->officeContactService->findLeave($this->identity()->getOfficeContact(), $start, $end);
        $eventList = [];

        foreach ($leaveList as $leave) {
            $eventList[] = $this->officeContactService->parseCalendarEvent($leave);
        }

        return new JsonModel($eventList);
    }

    public function fetchAllAction(): JsonModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $timeZone = new DateTimeZone($request->getQuery('timeZone', 'UTC'));
        $start = new DateTime($request->getQuery('start', 'now'), $timeZone);
        $end = new DateTime($request->getQuery('end', 'now'), $timeZone);
        $leaveList = $this->officeContactService->findAllLeave($start, $end);
        $eventList = [];

        /** @var Leave $leave */
        foreach ($leaveList as $leave) {
            $eventList[] = $this->officeContactService->parseOfficeCalendarEvent($leave);
        }

        return new JsonModel($eventList);
    }
}
