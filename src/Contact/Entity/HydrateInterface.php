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

interface HydrateInterface
{
    /**
     * Needed for the hydration of form elements.
     *
     * @return array
     */
    public function getArrayCopy();

    public function populate();
}
