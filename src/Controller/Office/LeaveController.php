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
use Contact\Service\Office\ContactService as OfficeContactService;
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

    public function __construct(OfficeContactService $officeContactService)
    {
        $this->officeContactService = $officeContactService;
    }

    public function manageAction(): ViewModel
    {
        $upcomingLeave = [];
        if ($this->identity()->getOfficeContact() instanceof OfficeContact) {
            $upcomingLeave = $this->officeContactService->findUpcomingLeave($this->identity()->getOfficeContact());
        }

        return new ViewModel([
            'upcomingLeave' => $upcomingLeave
        ]);
    }

    public function newAction(): ViewModel
    {
        return new ViewModel([

        ]);
    }

    public function editAction(): ViewModel
    {
        return new ViewModel([

        ]);
    }
}
