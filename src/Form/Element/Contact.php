<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form\Element;

use Contact\Entity;
use Zend\Form\Element;

/**
 * Class Contact
 *
 * @package Contact\Form\Element
 */
class Contact extends Element\Select
{

    /**
     * Contact constructor.
     *
     * @param null $name
     * @param array $options
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setDisableInArrayValidator(true);
    }

    /**
     * @param Entity\Contact $contact
     */
    public function injectContact(Entity\Contact $contact)
    {
        $this->valueOptions[$contact->getId()] = $contact->getFormName();
    }
}
