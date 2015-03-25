<?php
/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */

namespace Contact\Service;

/**
 * Japaveh Webdesign copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   2004-2014 Japaveh Webdesign
 * @license     http://solodb.net/license.txt proprietary
 *
 * @link        http://solodb.net
 */
interface ContactServiceAwareInterface
{
    /**
     * The contact service.
     *
     * @param ContactService $contactService
     */
    public function setContactService(ContactService $contactService);

    /**
     * Get contact service.
     *
     * @return ContactService
     */
    public function getContactService();
}
