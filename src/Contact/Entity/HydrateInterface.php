<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Entity;

interface HydrateInterface
{
    /**
     * Needed for the hydration of form elements
     *
     * @return array
     */
    public function getArrayCopy();

    public function populate();
}
