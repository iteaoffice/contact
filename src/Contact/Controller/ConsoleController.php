<?php

/**
 * Jield copyright message placeholder.
 *
 * @category  Contact
 *
 * @author    Johan van der Heide <info@jield.nl>
 * @copyright Copyright (c) 2004-2014 Jield (http://jield.nl)
 */

namespace Contact\Controller;

use Contact\Controller\Plugin\PartnerSearch;
use Search\Service\SearchServiceAwareInterface;

/**
 * Class ContactController.
 * @method PartnerSearch partnerSearch()
 */
class ConsoleController extends ContactAbstractController implements SearchServiceAwareInterface
{
}
