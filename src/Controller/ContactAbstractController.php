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

use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Controller\Plugin;
use Contact\Entity\Contact;
use Contact\Entity\Selection;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Helper\Identity;

/**
 * @method Identity|Contact identity()
 * @method FlashMessenger flashMessenger()
 * @method IsAllowed isAllowed($resource, $action)
 * @method Plugin\HandleImport handleImport(Contact $contact, string $data, ?array $import, ?array $optIn, ?int $selectionId, ?string $selectionName)
 * @method Plugin\SelectionExport selectionExport(Selection $selection, int $type)
 * @method Plugin\GetFilter getContactFilter(array $defaults = [])
 * @method Plugin\MergeContact mergeContact()
 */
abstract class ContactAbstractController extends AbstractActionController
{
}
