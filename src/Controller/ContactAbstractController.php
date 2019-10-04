<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Controller\Plugin;
use Contact\Entity\Contact;
use Contact\Entity\Selection;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Helper\Identity;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @method Identity|Contact identity()
 * @method FlashMessenger flashMessenger()
 * @method IsAllowed isAllowed($resource, $action)
 * @method Plugin\HandleImport handleImport(Contact $contact, string $data, ?array $import, ?array $optIn, ?int $selectionId, ?string $selectionName)
 * @method Plugin\SelectionExport selectionExport(Selection $selection, int $type)
 * @method Plugin\GetFilter getContactFilter(array $defaults = [])
 * @method ZfcUserAuthentication zfcUserAuthentication()
 * @method Plugin\MergeContact mergeContact()
 */
abstract class ContactAbstractController extends AbstractActionController
{
}
