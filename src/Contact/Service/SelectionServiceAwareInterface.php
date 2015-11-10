<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Service;

interface SelectionServiceAwareInterface
{
    /**
     * Get selectionService.
     *
     * @return SelectionService.
     */
    public function getSelectionService();

    /**
     * Set selectionService.
     *
     * @param SelectionService $selectionService
     */
    public function setSelectionService(SelectionService $selectionService);
}
