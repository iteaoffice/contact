<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Service;

use Contact\Entity\Contact;

/**
 * ContactService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class ContactService extends ServiceAbstract
{
    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * @param $email
     *
     * @return null|Contact
     */
    public function findContactByEmail($email)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('contact'))->findContactByEmail($email);
    }

    /**
     * Find a contact based on a hash
     *
     * @param $hash
     *
     * @return null|Contact
     */
    public function findContactByHash($hash)
    {
        $contact   = new Contact();
        $contactId = $contact->decryptHash($hash);

        return $this->getEntityManager()->find($this->getFullEntityName('contact'), $contactId);
    }


}
