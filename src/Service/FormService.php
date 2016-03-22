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

use Zend\Form\Form;

class FormService extends ServiceAbstract
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @param null $className
     * @param null $entity
     * @param bool $bind
     *
     * @return array|object
     */
    public function getForm($className = null, $entity = null, $bind = true)
    {
        if (!is_null($className) && is_null($entity)) {
            $entity = $this->getEntity($className);
        }
        if (!is_object($entity)) {
            throw new \InvalidArgumentException("No entity created given");
        }
        $formName = 'contact_' . $entity->get('underscore_entity_name') . '_form';
        $form = $this->getServiceLocator()->get($formName);
        $filterName = 'contact_' . $entity->get('underscore_entity_name') . '_form_filter';
        $filter = $this->getServiceLocator()->get($filterName);
        $form->setInputFilter($filter);
        if ($bind) {
            $form->bind($entity);
        }

        return $form;
    }

    /**
     * @param      $className
     * @param null $entity
     * @param      $data
     *
     * @return Form
     */
    public function prepare($className, $entity = null, $data = [])
    {
        $form = $this->getForm($className, $entity, true);
        $form->setData($data);

        return $form;
    }
}
