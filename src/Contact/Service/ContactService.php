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
class LocationService extends ServiceAbstract
{
    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * Find 1 entity based on the name
     *
     * @param         $entity
     * @param         $name
     * @return object
     */
    public function findEntityByName($entity, $name)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName($entity))->findOneBy(
            array('name' => $name)
        );
    }

    public function get($name)
    {
        // TODO: Implement get() method.
    }

    public function has($name)
    {
        // TODO: Implement has() method.
    }

}
