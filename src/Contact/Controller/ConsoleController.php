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

    /**
     * Check the status of pending trainigs and send an email to the involved mehtor
     *
     * @return array
     */
    public function partnerSearchUpdateAction()
    {
        $this->getSearchService()->setSolrClient('contact');
        $this->getSearchService()->updateContactIndex();
    }

    /**
     * Check the status of pending trainigs and send an email to the involved mehtor
     *
     * @return array
     */
    public function partnerSearchResetAction()
    {
        $this->getSearchService()->setSolrClient('contact');
        $this->getSearchService()->clearIndex(true);
        $this->getSearchService()->updateContactIndex();
    }

}