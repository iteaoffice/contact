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
use Contact\Entity\Office\Contact;
use Contact\Service\Office\ContactService as OfficeContactService;
use Zend\View\Model\ViewModel;

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

    public function __construct(OfficeContactService $officeContactService)
    {
        $this->officeContactService = $officeContactService;
    }

    public function listAction(): ViewModel
    {
        return new ViewModel(
            [
                'officeContacts' => $this->officeContactService->findAll(Contact::class)
            ]
        );
    }

    public function newAction(): ViewModel
    {
        return new ViewModel(
            [

            ]
        );
    }

    public function editAction(): ViewModel
    {
        return new ViewModel(
            [

            ]
        );
    }
}
