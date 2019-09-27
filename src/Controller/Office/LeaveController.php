<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */

declare(strict_types=1);

namespace Contact\Controller\Office;

use Contact\Controller\ContactAbstractController;
use Contact\Entity\Office\Leave;
use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Service\FormService;
use Contact\Service\Office\ContactService as OfficeContactService;
use DateTime;
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
     * @var FormService
     */
    private $formService;

    /**
     * @var OfficeContactService
     */
    private $officeContactService;

    public function __construct(FormService $formService, OfficeContactService $officeContactService)
    {
        $this->formService          = $formService;
        $this->officeContactService = $officeContactService;
    }

    public function manageAction()
    {
        $form = $this->formService->prepare(Leave::class);
        $form->remove('csrf');

        return new ViewModel([
            'form'          => $form
        ]);
    }

    public function updateAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $leave = new Leave();
            if ($request->getPost()->get('id') !== null) {
                $leave = $this->officeContactService->find(Leave::class, (int) $request->getPost()->get('id'));
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
        $leave = $this->officeContactService->find(Leave::class, (int) $request->getPost()->get('id'));
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
            $leave = $this->officeContactService->find(Leave::class, (int) $request->getPost()->get('id'));
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
        $request  = $this->getRequest();
        $timeZone = new \DateTimeZone($request->getQuery('timeZone', 'UTC'));
        $start    = new DateTime($request->getQuery('start', 'now'), $timeZone);
        $end      = new DateTime($request->getQuery('end', 'now'), $timeZone);

        $leaveList = $this->officeContactService->findLeave($this->identity()->getOfficeContact(), $start, $end);
        $eventList = [];
        foreach ($leaveList as $leave) {
            $eventList[] = $this->officeContactService->parseFullCalendarEvent($leave);
        }

        return new JsonModel($eventList);
    }
}
