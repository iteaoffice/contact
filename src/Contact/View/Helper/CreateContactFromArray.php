<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Project
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Service\ContactService;

/**
 * Class VersionServiceProxy
 * @package General\View\Helper
 */
class CreateContactFromArray extends HelperAbstract
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
