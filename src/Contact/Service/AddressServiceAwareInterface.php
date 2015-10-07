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
interface AddressServiceAwareInterface
{
    /**
     * The address service.
     *
     * @param AddressService $addressService
     */
    public function setAddressService(AddressService $addressService);

    /**
     * Get address service.
     *
     * @return AddressService
     */
    public function getAddressService();
}
