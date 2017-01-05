<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Service\ContactService;

/**
 * Class CreateContactFromArray
 *
 * @package Contact\View\Helper
 */
class CreateContactFromArray extends AbstractViewHelper
{
    /**
     * @param array $contactDetails
     *
     * @return ContactService
     */
    public function __invoke(array $contactDetails)
    {
        $contact = new Contact();
        foreach ($contactDetails as $key => $value) {
            $contact->$key = $value;
        }

        return $contact;
    }
}
