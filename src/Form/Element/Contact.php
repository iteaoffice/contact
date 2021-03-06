<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Form\Element;

use Contact\Entity;
use Laminas\Form\Element;

/**
 * Class Contact
 *
 * @package Contact\Form\Element
 */
class Contact extends Element\Select
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setDisableInArrayValidator(true);
    }

    public function injectContact(Entity\Contact $contact): void
    {
        $this->valueOptions[$contact->getId()] = $contact->getFormName();
    }
}
