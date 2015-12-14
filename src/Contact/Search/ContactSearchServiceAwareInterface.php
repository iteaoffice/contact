<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Search
 *
 * @author    Bart van Eijck <bart.van.eijck@itea3.org>
 * @copyright Copyright (c) 2004-2015 ITEA Office (http://itea3.org)
 */

namespace Contact\Search;

/**
 * Interface ContactSearchServiceAwareInterface
 *
 * @package Contact\Search
 */
interface ContactSearchServiceAwareInterface
{
    /**
     * Set a contact search service
     *
     * @param ContactSearchService $contactSearchService
     */
    public function setContactSearchService(ContactSearchService $contactSearchService);

    /**
     * Get the contact search service
     *
     * @return ContactSearchService
     */
    public function getContactSearchService();
}
