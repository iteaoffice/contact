<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Entity;

/**
 * Interface EntityInterface
 *
 * @package Contact\Entity
 */
interface EntityInterface
{
    public function getId();

    public function __get($property);

    public function __set($property, $value);
}