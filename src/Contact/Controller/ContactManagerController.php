<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Controller;

use Contact\Controller\Plugin\HandleImport;
use Contact\Form\Import;
use Contact\Form\Statistics;
use Contact\Service\StatisticsService;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContactManagerController.
 *
 * @method HandleImport handleImport()
 */
class ContactManagerController extends ContactAbstractController
{
}
